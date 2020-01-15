<?php declare(strict_types=1);

namespace Hanaboso\MongoDataGrid;

use DateTime;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\Query\Expr;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Exception;
use Hanaboso\MongoDataGrid\Exception\GridException;
use Hanaboso\MongoDataGrid\Result\ResultData;
use Hanaboso\Utils\Date\DateTimeUtils;
use LogicException;
use MongoDB\BSON\Regex;
use MongoDB\Driver\Exception\CommandException;

/**
 * Class GridFilterAbstract
 *
 * @package Hanaboso\MongoDataGrid
 */
abstract class GridFilterAbstract
{

    public const EQ       = 'EQ';
    public const NEQ      = 'NEQ';
    public const GT       = 'GT';
    public const LT       = 'LT';
    public const GTE      = 'GTE';
    public const LTE      = 'LTE';
    public const LIKE     = 'LIKE';
    public const STARTS   = 'STARTS';
    public const ENDS     = 'ENDS';
    public const NEMPTY   = 'NEMPTY';
    public const EMPTY    = 'EMPTY';
    public const BETWEEN  = 'BETWEEN';
    public const NBETWEEN = 'NBETWEEN';

    public const ASCENDING  = 'ASC';
    public const DESCENDING = 'DESC';

    public const COLUMN    = 'column';
    public const OPERATOR  = 'operator';
    public const VALUE     = 'value';
    public const DIRECTION = 'direction';
    public const SEARCH    = 'search';

    protected const DATE_FORMAT = DateTimeUtils::DATE_TIME;

    /**
     * @var DocumentManager
     */
    protected DocumentManager $dm;

    /**
     * @var string
     * @phpstan-var class-string
     */
    protected string $document;

    /**
     * @var Builder|NULL
     */
    private ?Builder $countQuery;

    /**
     * @var bool
     */
    private bool $useTextSearch;

    /**
     * @var mixed[]
     */
    private array $orderCols;

    /**
     * @var Builder
     */
    private Builder $searchQuery;

    /**
     * @var mixed[]
     */
    private array $searchableCols;

    /**
     * @var mixed[]
     */
    private array $filterCols;

    /**
     * @var mixed[]
     */
    private array $filterColsCallbacks;

    /**
     * GridFilterAbstract constructor.
     *
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
        $this->setDocument();

        $this->filterCols          = $this->filterCols();
        $this->orderCols           = $this->orderCols();
        $this->searchableCols      = $this->searchableCols();
        $this->filterColsCallbacks = $this->configFilterColsCallbacks();
        $this->useTextSearch       = $this->useTextSearch();
    }

    /**
     * @param GridRequestDtoInterface $gridRequestDto
     *
     * @return ResultData
     * @throws Exception
     */
    public function getData(GridRequestDtoInterface $gridRequestDto): ResultData
    {
        $this->searchQuery = $this->prepareSearchQuery();
        $this->countQuery  = $this->configCustomCountQuery();
        $this->searchQuery->hydrate(FALSE);

        $this->processSortations($gridRequestDto);
        $this->processConditions($gridRequestDto, $this->searchQuery);

        if ($this->countQuery) {
            $this->processConditions($gridRequestDto, $this->countQuery);
        } else {
            $this->countQuery = clone $this->searchQuery;
        }

        $this->processPagination($gridRequestDto);

        try {
            $data = new ResultData($this->searchQuery->getQuery(), static::DATE_FORMAT);
            /** @var Builder $countQuery */
            $countQuery = $this->countQuery;
            /** @var int $total */
            $total = $countQuery->count()->getQuery()->execute();
            $gridRequestDto->setTotal($total);
        } catch (CommandException $e) {
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
     * @return DocumentRepository<mixed>
     */
    public function getRepository(): DocumentRepository
    {
        /** @var DocumentRepository<mixed> $repo */
        $repo = $this->dm->getRepository($this->document);

        return $repo;
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
            foreach ($sortations as $sortation) {
                $column    = $sortation[self::COLUMN];
                $direction = $sortation[self::DIRECTION];
                if (!isset($this->orderCols[$column])) {
                    throw new GridException(
                        sprintf(
                            "Column '%s' cannot be used for sorting! Have you forgotten add it to '%s::orderCols'?",
                            $column,
                            static::class
                        ),
                        GridException::SORT_COLS_ERROR
                    );
                }

                $this->searchQuery->sort($this->orderCols[$column], $direction);
            }
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
        $conditions          = $dto->getFilter();
        $conditionExpression = $builder->expr();

        $exp = FALSE;
        foreach ($conditions as $andCondition) {
            $hasExpression = FALSE;
            $expression    = $builder->expr();

            foreach ($andCondition as $orCondition) {
                if (!array_key_exists(self::COLUMN, $orCondition) ||
                    !array_key_exists(self::OPERATOR, $orCondition) ||
                    !array_key_exists(self::VALUE, $orCondition) &&
                    !in_array($orCondition[self::OPERATOR], [self::EMPTY, self::NEMPTY], TRUE)) {
                    throw new LogicException(
                        sprintf(
                            "Advanced filter must have '%s', '%s' and '%s' field!",
                            self::COLUMN,
                            self::OPERATOR,
                            self::VALUE
                        )
                    );
                }

                if (!array_key_exists(self::VALUE, $orCondition)) {
                    $orCondition[self::VALUE] = '';
                }

                $column = $orCondition[self::COLUMN];

                $this->checkFilterColumn($column);
                $hasExpression            = TRUE;
                $orCondition[self::VALUE] = $this->processDateTime($orCondition[self::VALUE]);

                if (isset($this->filterColsCallbacks[$column])) {
                    $expression = $builder->expr();

                    $this->filterColsCallbacks[$column](
                        $this->searchQuery,
                        $orCondition[self::VALUE],
                        $this->filterCols[$column],
                        $expression,
                        $orCondition[self::OPERATOR]
                    );
                    $expression->addOr($expression);

                    continue;
                }

                $expression->addOr(
                    self::getCondition(
                        $builder,
                        $this->filterCols[$column],
                        $orCondition[self::VALUE],
                        $orCondition[self::OPERATOR]
                    )
                );
            }

            if ($hasExpression) {
                $conditionExpression->addAnd($expression);
                $exp = TRUE;
            }
        }

        if ($exp) {
            $builder->addAnd($conditionExpression);
        }

        $search = $dto->getSearch();

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
                if (!array_key_exists($column, $this->filterCols)) {
                    throw new GridException(
                        sprintf(
                            "Column '%s' cannot be used for searching! Have you forgotten add it to '%s::filterCols'?",
                            $column,
                            static::class
                        ),
                        GridException::SEARCHABLE_COLS_ERROR
                    );
                }

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
        $limit = $dto->getItemsPerPage();

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
        $inner = $value;
        if (is_array($value)) {
            $inner = $value[0];
        }

        if (is_string($inner) && preg_match('/\d{4}-\d{2}-\d{2}.\d{2}:\d{2}:\d{2}/', $inner)) {
            return new DateTime($inner);
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
    abstract protected function prepareSearchQuery(): Builder;

    /**
     *
     */
    abstract protected function setDocument(): void;

    /**
     * @return mixed[]
     */
    abstract protected function filterCols(): array;

    /**
     * @return mixed[]
     */
    abstract protected function orderCols(): array;

    /**
     * @return mixed[]
     */
    abstract protected function searchableCols(): array;

    /**
     * @return bool
     */
    abstract protected function useTextSearch(): bool;

    /**
     * -------------------------------------------- HELPERS -----------------------------------------------
     */

    /**
     * In child can configure GridFilterAbstract::filterColsCallbacks
     * example child content
     *
     * return [ESomeEnumCols::CREATED_AT_FROM => function (Builder $builder,string $value,string $name,Expr $expr,?string $operator){}]
     *
     * @return mixed[]
     */
    protected function configFilterColsCallbacks(): array
    {
        return [];
    }

    /**
     * In child can configure GridFilterAbstract::configCustomCountQuery
     * example child content
     * return $this->getRepository()->createQueryBuilder('c')->select('count(c.id)')
     */
    protected function configCustomCountQuery(): ?Builder
    {
        return NULL;
    }

    /**
     * @param Builder     $builder
     * @param string      $name
     * @param mixed       $value
     * @param string|NULL $operator
     *
     * @return Expr
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
                return $builder->expr()->field($name)->gte(self::getValue($value));
            case self::GT:
                return $builder->expr()->field($name)->gt(self::getValue($value));
            case self::LTE:
                return $builder->expr()->field($name)->lte(self::getValue($value));
            case self::LT:
                return $builder->expr()->field($name)->lt(self::getValue($value));
            case self::NEMPTY:
                return $builder->expr()
                    ->addOr($builder->expr()->field($name)->notEqual(NULL))
                    ->addOr($builder->expr()->field($name)->notEqual(self::getValue($value)));
            case self::EMPTY:
                return $builder->expr()
                    ->addOr($builder->expr()->field($name)->equals(NULL))
                    ->addOr($builder->expr()->field($name)->equals(self::getValue($value)));
            case self::LIKE:
                return $builder->expr()->field($name)->equals(
                    new Regex(sprintf('%s', preg_quote(self::getValue($value))), 'i')
                );
            case self::STARTS:
                return $builder->expr()->field($name)->equals(
                    new Regex(sprintf('^%s', preg_quote(self::getValue($value))), 'i')
                );
            case self::ENDS:
                return $builder->expr()->field($name)->equals(
                    new Regex(sprintf('%s$', preg_quote(self::getValue($value))), 'i')
                );
            case self::BETWEEN:
                if (is_array($value) && count($value) >= 2) {
                    return $builder->expr()
                        ->addAnd($builder->expr()->field($name)->gte($value[0]))
                        ->addAnd($builder->expr()->field($name)->lte($value[1]));
                }

                return $builder->expr()->field($name)->equals(self::getValue($value));
            case self::NBETWEEN:
                if (is_array($value) && count($value) >= 2) {
                    return $builder->expr()
                        ->addOr($builder->expr()->field($name)->lte($value[0]))
                        ->addOr($builder->expr()->field($name)->gte($value[1]));
                }

                return $builder->expr()->field($name)->notEqual(self::getValue($value));
            default:
                return $builder->expr()->field($name)->equals(self::getValue($value));
        }
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    private static function getValue($value)
    {
        return is_array($value) ? $value[0] : $value;
    }

}
