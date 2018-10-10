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
     * @return array
     */
    public function getFilter(): array;

    /**
     * @return string|NULL
     */
    public function getPage(): ?string;

    /**
     * @return int
     */
    public function getLimit(): int;

    /**
     * @return array
     */
    public function getOrderBy(): array;

    /**
     * @param int $total
     *
     * @return GridRequestDtoInterface
     */
    public function setTotal(int $total): GridRequestDtoInterface;

}