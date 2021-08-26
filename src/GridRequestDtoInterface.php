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
     * @param bool $withAdditional
     *
     * @return mixed[]
     */
    public function getFilter(bool $withAdditional = TRUE): array;

    /**
     * @return int
     */
    public function getPage(): int;

    /**
     * @return string|NULL
     */
    public function getSearch(): ?string;

    /**
     * @return int
     */
    public function getItemsPerPage(): int;

    /**
     * @return mixed[]
     */
    public function getOrderBy(): array;

    /**
     * @return mixed[]
     */
    public function getNativeQuery(): array;

    /**
     * @param int $total
     *
     * @return GridRequestDtoInterface
     */
    public function setTotal(int $total): GridRequestDtoInterface;

    /**
     * @return int
     */
    public function getTotal(): int;

}
