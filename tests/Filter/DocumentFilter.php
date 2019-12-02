<?php declare(strict_types=1);

namespace MongoDataGridTests\Filter;

use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\Query\Expr;
use Hanaboso\MongoDataGrid\GridFilterAbstract;
use MongoDataGridTests\Document\Document;

/**
 * Class DocumentFilter
 *
 * @package MongoDataGridTests\Filter
 */
final class DocumentFilter extends GridFilterAbstract
{

    /**
     * @return array
     */
    protected function filterCols(): array
    {
        return [
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
    }

    /**
     * @return array
     */
    protected function orderCols(): array
    {
        return [
            'id'     => '_id',
            'string' => 'string',
            'int'    => 'int',
            'float'  => 'float',
            'bool'   => 'bool',
            'date'   => 'date',
        ];
    }

    /**
     * @return array
     */
    protected function searchableCols(): array
    {
        return [
            'string',
            'int',
            'float',
        ];
    }

    /**
     * @return bool
     */
    protected function useTextSearch(): bool
    {
        return TRUE;
    }

    /**
     * @return Builder
     */
    protected function prepareSearchQuery(): Builder
    {
        return $this
            ->getRepository()
            ->createQueryBuilder()
            ->select('_id', 'string', 'int', 'float', 'bool', 'date');
    }

    /**
     * @return Builder|null
     */
    protected function configCustomCountQuery(): ?Builder
    {
        parent::configCustomCountQuery();

        return $this
            ->getRepository()
            ->createQueryBuilder()
            ->select(['_id']);
    }

    /**
     * @return array
     */
    protected function configFilterColsCallbacks(): array
    {
        parent::configFilterColsCallbacks();

        return [
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
