<?php declare(strict_types=1);

namespace Hanaboso\MongoDataGrid;

use DateTime;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ODM\MongoDB\Query\Builder;
use Hanaboso\MongoDataGrid\Exception\GridException;
use Hanaboso\MongoDataGrid\Result\ResultData;
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
     * @throws GridException
     * @throws MongoDBException
     * @throws MongoException
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

        $data = new ResultData($this->searchQuery->getQuery());
        $gridRequestDto->setTotal($this->countQuery->count()->getQuery()->execute());

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
     * @throws GridException
     * @throws MongoException
     */
    private function processConditions(GridRequestDtoInterface $dto, Builder $builder): void
    {
        $conditions = $dto->getFilter();

        if ($conditions) {
            foreach ($conditions as $column => $value) {
                if ($column === self::FILTER_SEARCH_KEY) {
                    continue;
                }

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

                if (isset($this->filterColsCallbacks[$column])) {
                    $this->filterColsCallbacks[$column]($this->searchQuery, $value, $this->filterCols[$column]);
                    continue;
                }

                if (is_string($value) && preg_match('/\d{4}-\d{2}-\d{2}.\d{2}:\d{2}:\d{2}/', $value)) {
                    $value = new DateTime($value);
                }

                $column = $this->filterCols[$column];

                if (is_null($value)) {
                    $builder->field($column)->equals(NULL);
                } elseif ($value === self::FILER_VAL_NOT_NULL) {
                    $builder->field($column)->notEqual(NULL);
                } elseif (is_array($value)) {
                    $builder->field($column)->in($value);
                } elseif (preg_match('/^([^\s]+)>=$/', $column, $columnMatches)) {
                    $builder->field($columnMatches[1])->gte($value);
                } elseif (preg_match('/^([^\s]+)>$/', $column, $columnMatches)) {
                    $builder->field($columnMatches[1])->gt($value);
                } elseif (preg_match('/^([^\s]+)<=$/', $column, $columnMatches)) {
                    $builder->field($columnMatches[1])->lte($value);
                } elseif (preg_match('/^([^\s]+)<$/', $column, $columnMatches)) {
                    $builder->field($columnMatches[1])->lt($value);
                } else {
                    $builder->field($column)->equals($value);
                }
            }
        }

        $search = $conditions[self::FILTER_SEARCH_KEY] ?? '';

        if ($search) {
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
                $regex = new MongoRegex(sprintf('/.*%s.*/i', preg_quote($search)));
                $searchExpression->addOr($builder->expr()->field($column)->equals($regex));
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

}
