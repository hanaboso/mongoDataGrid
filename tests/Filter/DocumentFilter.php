<?php declare(strict_types=1);

namespace Tests\Filter;

use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\Query\Expr;
use Hanaboso\MongoDataGrid\GridFilterAbstract;
use Tests\Document\Document;

/**
 * Class DocumentFilter
 *
 * @package Tests\Filter
 */
final class DocumentFilter extends GridFilterAbstract
{

    /**
     * @var string[]
     */
    protected $filterCols = [
        'id'            => '_id',
        'string'        => 'string',
        'int'           => 'int',
        'float'         => 'float',
        'bool'          => 'bool',
        'date'          => 'date',
        'int_gte'       => 'int>=',
        'int_gt'        => 'int>',
        'int_lt'        => 'int<',
        'int_lte'       => 'int<=',
        'custom_string' => 'string',
    ];

    /**
     * @var string[]
     */
    protected $orderCols = [
        'id'     => '_id',
        'string' => 'string',
        'int'    => 'int',
        'float'  => 'float',
        'bool'   => 'bool',
        'date'   => 'date',
    ];

    /**
     * @var string[]
     */
    protected $searchableCols = [
        'string',
        'int',
        'float',
    ];

    /**
     * @var bool
     */
    protected $useTextSearch = TRUE;

    /**
     *
     */
    protected function prepareSearchQuery(): void
    {
        $this->searchQuery = $this
            ->getRepository()
            ->createQueryBuilder()
            ->select('_id', 'string', 'int', 'float', 'bool', 'date');
    }

    /**
     *
     */
    protected function configCustomCountQuery(): void
    {
        parent::configCustomCountQuery();

        $this->countQuery = $this
            ->getRepository()
            ->createQueryBuilder()
            ->select(['_id']);
    }

    /**
     *
     */
    protected function configFilterColsCallbacks(): void
    {
        parent::configFilterColsCallbacks();

        $this->filterColsCallbacks = [
            'custom_string' => function (
                Builder $builder,
                string $value,
                string $name,
                Expr $expr,
                ?string $operator
            ): void {
                $builder;
                $operator;

                $expr->field($name)->equals($value);
            },
        ];
    }

    /**
     *
     */
    protected function setDocument(): void
    {
        $this->document = Document::class;
    }

}