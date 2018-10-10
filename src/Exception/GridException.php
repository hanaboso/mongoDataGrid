<?php declare(strict_types=1);

namespace Hanaboso\MongoDataGrid\Exception;

use Exception;

/**
 * Class GridException
 *
 * @package Hanaboso\MongoDataGrid\Exception
 */
final class GridException extends Exception
{

    public const FILTER_COLS_ERROR     = 1;
    public const ORDER_COLS_ERROR      = 2;
    public const SEARCHABLE_COLS_ERROR = 3;

}