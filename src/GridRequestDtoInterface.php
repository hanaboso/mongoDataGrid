<?php declare(strict_types=1);

namespace Hanaboso\MongoDataGrid;

/**
 * Interface GridRequestDtoInterface
 *
 * @package Hanaboso\MongoDataGrid
 */
interface GridRequestDtoInterface
{

    /**
     * @return mixed[]
     */
    public function getFilter(): array;

    /**
     * @return mixed[]
     */
    public function getAdvancedFilter(): array;

    /**
     * @return int
     */
    public function getPage(): int;

    /**
     * @return int
     */
    public function getLimit(): int;

    /**
     * @return mixed[]
     */
    public function getOrderBy(): array;

    /**
     * @param int $total
     *
     * @return GridRequestDtoInterface
     */
    public function setTotal(int $total): GridRequestDtoInterface;

}