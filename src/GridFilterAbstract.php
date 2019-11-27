<?php declare(strict_types=1);

namespace Hanaboso\MongoDataGrid;

use DateTime;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\MongoDB\Exception\ResultException;
use Doctrine\MongoDB\Query\Expr;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;
use Exception;
use Hanaboso\MongoDataGrid\Exception\GridException;
use Hanaboso\MongoDataGrid\Result\ResultData;
use LogicException;
use MongoException;
use MongoRegex;

/**
 * Class GridFilterAbstract
 *
 * @package Hanaboso\MongoDataGrid
 */
abstract class GridFilterAbstract
{

    /**
     * key in array filter for fulltext search
     */
    public const FILTER_SEARCH_KEY = '_MODIFIER_SEARCH';

    /**
     * Value for filter when filter creates where `dbCol` IS NOT NULL
     */
    public const FILER_VAL_NOT_NULL = '_MODIFIER_VAL_NOT_NULL';

    public const EQ       = 'EQ';
    public const NEQ      = 'NEQ';
    public const GT       = 'GT';
    public const LT       = 'LT';
    public const GTE      = 'GTE';
    public const LTE      = 'LTE';
    public const LIKE     = 'LIKE';
    public const STARTS   = 'STARTS';
    public const ENDS     = 'ENDS';
    public const FL       = 'FL';
    public const NFL      = 'NFL';
    public const BETWEEN  = 'BETWEEN';
    public const NBETWEEN = 'NBETWEEN';

    public const COLUMN    = 'column';
    public const OPERATION = 'operation';
    public const VALUE     = 'value';

    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var string
     */
    protected $document;

    /**
     * @var Builder
     */
    protected $searchQuery;

    /**
     * @var Builder|NULL
     */
    protected $countQuery = NULL;

    /**
     * @var array
     */
    protected $filters;

    /**
     * @var string
     */
    protected $order;

    /**
     * @var string
     */
    protected $search;

    /**
     * @var bool
     */
    protected $useTextSearch = FALSE;

    /**
     * @var array
     */
    protected $filterCols = [];

    /**
     * @var array
     */
    protected $orderCols = [];

    /**
     * @var array
     */
    protected $searchableCols = [];

    /**
     * @var array
     */
    protected $filterColsCallbacks = [];

    /**
     * GridFilterAbstract constructor.
     *
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
        $this->setDocument();
        $this->configFilterColsCallbacks();
        $this->configCustomCountQuery();
        $this->prepareSearchQuery();
        $this->searchQuery->hydrate(FALSE);
    }

    /**
     * @param GridRequestDtoInterface $gridRequestDto
     *
     * @return ResultData
     * @throws Exception
     */
    public function getData(GridRequestDtoInterface $gridRequestDto): ResultData
    {
        $this->processSortations($gridRequestDto);
        $this->processConditions($gridRequestDto, $this->searchQuery);

        if ($this->countQuery) {
            $this->processConditions($gridRequestDto, $this->countQuery);
        } else {
            $this->countQuery = clone $this->searchQuery;
        }

        $this->processPagination($gridRequestDto);

        try {
            $data = new ResultData($this->searchQuery->getQuery());
            $gridRequestDto->setTotal($this->countQuery->count()->getQuery()->execute());
        } catch (ResultException $e) {
            if ($e->getCode() === 27) {
                throw new LogicException(
                    sprintf(
                        "Column cannot be used for searching! Missing TEXT index on '%s::searchableCols' fields!",
                        static::class
                    )
                );
            }

            throw $e;
        }

        return $data;
    }

    /**
     * @return DocumentRepository|ObjectRepository
     */
    public function getRepository(): ObjectRepository
    {
        return $this->dm->getRepository($this->document);
    }

    /**
     * @param GridRequestDtoInterface $dto
     *
     * @throws GridException
     */
    private function processSortations(GridRequestDtoInterface $dto): void
    {
        $sortations = $dto->getOrderBy();

        if ($sortations) {
            [$columns, $direction] = $sortations;

            if (!isset($this->orderCols[$columns])) {
                throw new GridException(
                    sprintf(
                        "Column '%s' cannot be used for sorting! Have you forgotten add it to '%s::orderCols'?",
                        $columns,
                        static::class
                    ),
                    GridException::ORDER_COLS_ERROR
                );
            }

            $this->searchQuery->sort($this->orderCols[$columns], $direction);
        }
    }

    /**
     * @param GridRequestDtoInterface $dto
     * @param Builder                 $builder
     *
     * @throws Exception
     */
    private function processConditions(GridRequestDtoInterface $dto, Builder $builder): void
    {
        $conditions                  = $dto->getFilter();
        $advancedConditions          = $dto->getAdvancedFilter();
        $conditionExpression         = $builder->expr();
        $advancedConditionExpression = $builder->expr();

        /**
         * @var string $column
         * @var mixed  $value
         */
        foreach ($conditions as $column => $value) {
            if ($column === self::FILTER_SEARCH_KEY) {
                continue;
            }

            $this->checkFilterColumn($column);

            if (isset($this->filterColsCallbacks[$column])) {
                $expression = $builder->expr();

                $this->filterColsCallbacks[$column](
                    $this->searchQuery,
                    $value,
                    $this->filterCols[$column],
                    $expression,
                    NULL
                );

                $conditionExpression->addAnd($expression);
                continue;
            }

            $value  = $this->processDateTime($value);
            $column = $this->filterCols[$column];

            if (is_null($value)) {
                $conditionExpression->addAnd($builder->expr()->field($column)->equals(NULL));
            } else if ($value === self::FILER_VAL_NOT_NULL) {
                $conditionExpression->addAnd($builder->expr()->field($column)->notEqual(NULL));
            } else if (is_array($value)) {
                $conditionExpression->addAnd($builder->expr()->field($column)->in($value));
            } else if (preg_match('/^([^\s]+)>=$/', $column, $columnMatches)) {
                $conditionExpression->addAnd($builder->expr()->field($columnMatches[1])->gte($value));
            } else if (preg_match('/^([^\s]+)>$/', $column, $columnMatches)) {
                $conditionExpression->addAnd($builder->expr()->field($columnMatches[1])->gt($value));
            } else if (preg_match('/^([^\s]+)<=$/', $column, $columnMatches)) {
                $conditionExpression->addAnd($builder->expr()->field($columnMatches[1])->lte($value));
            } else if (preg_match('/^([^\s]+)<$/', $column, $columnMatches)) {
                $conditionExpression->addAnd($builder->expr()->field($columnMatches[1])->lt($value));
            } else {
                $conditionExpression->addAnd($builder->expr()->field($column)->equals($value));
            }
        }

        if ($conditions && (count($conditions) !== 1 || !isset($conditions[self::FILTER_SEARCH_KEY]))) {
            $builder->addAnd($conditionExpression);
        }

        $search   = NULL;
        $isSearch = TRUE;
        foreach ($advancedConditions as $andCondition) {
            $hasExpression = FALSE;
            $expression    = $builder->expr();

            foreach ($andCondition as $orCondition) {
                if (!array_key_exists(self::COLUMN, $orCondition) ||
                    !array_key_exists(self::OPERATION, $orCondition) ||
                    !array_key_exists(self::VALUE, $orCondition) &&
                    !in_array($orCondition[self::OPERATION], [self::FL, self::NFL], TRUE)) {
                    throw new LogicException(
                        sprintf(
                            "Advanced filter must have '%s', '%s' and '%s' field!",
                            self::COLUMN,
                            self::OPERATION,
                            self::VALUE
                        )
                    );
                }

                if (!array_key_exists(self::VALUE, $orCondition)) {
                    $orCondition[self::VALUE] = '';
                }

                $column = $orCondition[self::COLUMN];
                if ($column === self::FILTER_SEARCH_KEY) {
                    $search = $orCondition[self::VALUE];
                    continue;
                }

                $this->checkFilterColumn($column);
                $isSearch                 = FALSE;
                $hasExpression            = TRUE;
                $orCondition[self::VALUE] = $this->processDateTime($orCondition[self::VALUE]);

                if (isset($this->filterColsCallbacks[$column])) {
                    $expression = $builder->expr();

                    $this->filterColsCallbacks[$column](
                        $this->searchQuery,
                        $orCondition[self::VALUE],
                        $this->filterCols[$column],
                        $expression,
                        $orCondition[self::OPERATION]
                    );

                    $expression->addOr($expression);
                    continue;
                }

                $expression->addOr(
                    self::getCondition(
                        $builder,
                        $this->filterCols[$column],
                        $orCondition[self::VALUE],
                        $orCondition[self::OPERATION]
                    )
                );
            }

            if ($hasExpression) {
                $advancedConditionExpression->addAnd($expression);
            }
        }

        if ($advancedConditions && !$isSearch) {
            $builder->addAnd($advancedConditionExpression);
        }

        $search = $conditions[self::FILTER_SEARCH_KEY] ?? $search ?? '';

        if ($search) {
            if ($this->useTextSearch) {
                $builder->text($search);
            }

            $searchExpression = $builder->expr();

            if (!$this->searchableCols) {
                throw new GridException(
                    sprintf(
                        "Column cannot be used for searching! Have you forgotten add it to '%s::searchableCols'?",
                        static::class
                    ),
                    GridException::SEARCHABLE_COLS_ERROR
                );
            }

            foreach ($this->searchableCols as $column) {
                if (isset($this->filterColsCallbacks[$column])) {
                    $expression = $builder->expr();

                    $this->filterColsCallbacks[$column](
                        $this->searchQuery,
                        $search,
                        $this->filterCols[$column],
                        $expression,
                        NULL
                    );

                    $searchExpression->addOr($expression);
                    continue;
                }

                $searchExpression->addOr(self::getCondition($builder, $column, $search, self::LIKE));
            }

            $builder->addAnd($searchExpression);
        }
    }

    /**
     * @param GridRequestDtoInterface $dto
     */
    private function processPagination(GridRequestDtoInterface $dto): void
    {
        $page  = $dto->getPage();
        $limit = $dto->getLimit();

        $this->searchQuery->skip(--$page * $limit)->limit($limit);
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     * @throws Exception
     */
    private function processDateTime($value)
    {
        if (is_string($value) && preg_match('/\d{4}-\d{2}-\d{2}.\d{2}:\d{2}:\d{2}/', $value)) {
            return new DateTime($value);
        }

        return $value;
    }

    /**
     * @param string $column
     *
     * @throws GridException
     */
    private function checkFilterColumn(string $column): void
    {
        if (!isset($this->filterCols[$column])) {
            throw new GridException(
                sprintf(
                    "Column '%s' cannot be used for filtering! Have you forgotten add it to '%s::filterCols'?",
                    $column,
                    static::class
                ),
                GridException::FILTER_COLS_ERROR
            );
        }
    }

    /**
     *
     */
    abstract protected function prepareSearchQuery(): void;

    /**
     *
     */
    abstract protected function setDocument(): void;

    /**
     * -------------------------------------------- HELPERS -----------------------------------------------
     */

    /**
     * In child can configure GridFilterAbstract::filterColsCallbacks
     * example child content
     * $this->filterColsCallbacks[ESomeEnumCols::CREATED_AT_FROM] = [$object,'applyCreatedAtFrom']
     *
     * function applySomeFilter(QueryBuilder $qb,$filterVal,$colName){}
     */
    protected function configFilterColsCallbacks(): void
    {

    }

    /**
     * In child can configure GridFilterAbstract::configCustomCountQuery
     * example child content
     * $this->countQuery = $this->getRepository()->createQueryBuilder('c')->select('count(c.id)')
     */
    protected function configCustomCountQuery(): void
    {

    }

    /**
     * @param Builder     $builder
     * @param string      $name
     * @param mixed       $value
     * @param string|NULL $operator
     *
     * @return Expr
     * @throws MongoException
     */
    public static function getCondition(Builder $builder, string $name, $value, ?string $operator = NULL): Expr
    {
        switch ($operator) {
            case self::EQ:
                return is_array($value) ?
                    $builder->expr()->field($name)->in($value) :
                    $builder->expr()->field($name)->equals($value);
            case self::NEQ:
                return is_array($value) ?
                    $builder->expr()->field($name)->notIn($value) :
                    $builder->expr()->field($name)->notEqual($value);
            case self::GTE:
                return $builder->expr()->field($name)->gte($value);
            case self::GT:
                return $builder->expr()->field($name)->gt($value);
            case self::LTE:
                return $builder->expr()->field($name)->lte($value);
            case self::LT:
                return $builder->expr()->field($name)->lt($value);
            case self::FL:
                return $builder->expr()
                    ->addOr($builder->expr()->field($name)->notEqual(NULL))
                    ->addOr($builder->expr()->field($name)->notEqual($value));
            case self::NFL:
                return $builder->expr()
                    ->addOr($builder->expr()->field($name)->equals(NULL))
                    ->addOr($builder->expr()->field($name)->equals($value));
            case self::LIKE:
                return $builder->expr()->field($name)->equals(new MongoRegex(sprintf('/.*%s.*/i', preg_quote($value))));
            case self::STARTS:
                return $builder->expr()->field($name)->equals(new MongoRegex(sprintf('/^%s.*/i', preg_quote($value))));
            case self::ENDS:
                return $builder->expr()->field($name)->equals(new MongoRegex(sprintf('/.*%s$/i', preg_quote($value))));
            case self::BETWEEN:
                if (is_array($value) && count($value) >= 2) {
                    return $builder->expr()
                        ->addAnd($builder->expr()->field($name)->gte($value[0]))
                        ->addAnd($builder->expr()->field($name)->lte($value[1]));
                }

                return $builder->expr()->field($name)->equals($value);
            case self::NBETWEEN:
                if (is_array($value) && count($value) >= 2) {
                    return $builder->expr()
                        ->addOr($builder->expr()->field($name)->lte($value[0]))
                        ->addOr($builder->expr()->field($name)->gte($value[1]));
                }

                return $builder->expr()->field($name)->notEqual($value);
            default:
                return $builder->expr()->field($name)->equals($value);
        }
    }

}
