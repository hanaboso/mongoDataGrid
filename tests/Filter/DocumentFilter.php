<?php declare(strict_types=1);

namespace MongoDataGridTests\Filter;

use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\Query\Expr;
use Hanaboso\MongoDataGrid\GridFilterAbstract;
use Hanaboso\Utils\Date\DateTimeUtils;
use MongoDataGridTests\Document\Document;

/**
 * Class DocumentFilter
 *
 * @package MongoDataGridTests\Filter
 */
final class DocumentFilter extends GridFilterAbstract
{

    protected const DATE_FORMAT = DateTimeUtils::DATE_TIME;

    protected bool $allowNative = TRUE;

    /**
     * @return mixed[]
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
            'custom_string' => 'string',
        ];
    }

    /**
     * @return mixed[]
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
     * @return mixed[]
     */
    protected function searchableCols(): array
    {
        return [
            'string',
            'custom_string',
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
     * @return Builder|NULL
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
     * @return mixed[]
     */
    protected function configFilterColsCallbacks(): array
    {
        parent::configFilterColsCallbacks();

        return [
            'custom_string' => static function (
                Builder $builder,
                $value,
                string $name,
                Expr $expr,
                ?string $operator,
            ): void {
                $builder;
                $operator;

                $expr->field($name)->equals($value[0] ?? $value);
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
