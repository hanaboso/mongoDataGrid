<?php declare(strict_types=1);

namespace MongoDataGridTests\Integration;

use DateTime;
use DateTimeZone;
use Exception;
use Hanaboso\MongoDataGrid\Exception\GridException;
use Hanaboso\MongoDataGrid\GridFilterAbstract;
use Hanaboso\MongoDataGrid\GridRequestDto;
use LogicException;
use MongoDataGridTests\Document\Document;
use MongoDataGridTests\Filter\DocumentFilter;
use MongoDataGridTests\TestCaseAbstract;
use MongoDB\Driver\Exception\CommandException;
use Throwable;

/**
 * Class FilterTest
 *
 * @package MongoDataGridTests\Integration
 */
final class FilterTest extends TestCaseAbstract
{

    protected const PAGING = 'paging';

    private const DATETIME = 'Y-m-d H:i:s';
    private const SORTER   = 'sorter';
    private const FILTER   = 'filter';
    private const PAGE     = 'page';
    private const SEARCH   = 'search';

    private const ITEMS_PER_PAGE = 'itemsPerPage';

    /**
     * @var DateTime
     */
    private DateTime $today;

    /**
     * @throws Exception
     */
    public function testBasic(): void
    {
        $result = (new DocumentFilter($this->dm))->getData(new GridRequestDto([]))->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 0',
                    'int'    => 0,
                    'float'  => 0.0,
                    'bool'   => TRUE,
                    'date'   => $this->today->format(self::DATETIME),
                ], [
                    'id'     => $result[1]['id'],
                    'string' => 'String 1',
                    'int'    => 1,
                    'float'  => 1.1,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[2]['id'],
                    'string' => 'String 2',
                    'int'    => 2,
                    'float'  => 2.2,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[3]['id'],
                    'string' => 'String 3',
                    'int'    => 3,
                    'float'  => 3.3,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[4]['id'],
                    'string' => 'String 4',
                    'int'    => 4,
                    'float'  => 4.4,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[5]['id'],
                    'string' => 'String 5',
                    'int'    => 5,
                    'float'  => 5.5,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[6]['id'],
                    'string' => 'String 6',
                    'int'    => 6,
                    'float'  => 6.6,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[7]['id'],
                    'string' => 'String 7',
                    'int'    => 7,
                    'float'  => 7.7,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[8]['id'],
                    'string' => 'String 8',
                    'int'    => 8,
                    'float'  => 8.8,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[9]['id'],
                    'string' => 'String 9',
                    'int'    => 9,
                    'float'  => 9.9,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );
    }

    /**
     * @throws Exception
     */
    public function testSortations(): void
    {
        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::SORTER => [
                        [
                            'column'    => 'id',
                            'direction' => 'ASC',
                        ],
                    ],
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 0',
                    'int'    => 0,
                    'float'  => 0.0,
                    'bool'   => TRUE,
                    'date'   => $this->today->format(self::DATETIME),
                ], [
                    'id'     => $result[1]['id'],
                    'string' => 'String 1',
                    'int'    => 1,
                    'float'  => 1.1,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[2]['id'],
                    'string' => 'String 2',
                    'int'    => 2,
                    'float'  => 2.2,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[3]['id'],
                    'string' => 'String 3',
                    'int'    => 3,
                    'float'  => 3.3,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[4]['id'],
                    'string' => 'String 4',
                    'int'    => 4,
                    'float'  => 4.4,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[5]['id'],
                    'string' => 'String 5',
                    'int'    => 5,
                    'float'  => 5.5,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[6]['id'],
                    'string' => 'String 6',
                    'int'    => 6,
                    'float'  => 6.6,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[7]['id'],
                    'string' => 'String 7',
                    'int'    => 7,
                    'float'  => 7.7,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[8]['id'],
                    'string' => 'String 8',
                    'int'    => 8,
                    'float'  => 8.8,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[9]['id'],
                    'string' => 'String 9',
                    'int'    => 9,
                    'float'  => 9.9,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::SORTER => [
                        [
                            'column'    => 'id',
                            'direction' => 'DESC',
                        ],
                    ],
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 9',
                    'int'    => 9,
                    'float'  => 9.9,
                    'bool'   => FALSE,
                    'date'   => $this->today->format(self::DATETIME),
                ], [
                    'id'     => $result[1]['id'],
                    'string' => 'String 8',
                    'int'    => 8,
                    'float'  => 8.8,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[2]['id'],
                    'string' => 'String 7',
                    'int'    => 7,
                    'float'  => 7.7,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[3]['id'],
                    'string' => 'String 6',
                    'int'    => 6,
                    'float'  => 6.6,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[4]['id'],
                    'string' => 'String 5',
                    'int'    => 5,
                    'float'  => 5.5,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[5]['id'],
                    'string' => 'String 4',
                    'int'    => 4,
                    'float'  => 4.4,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[6]['id'],
                    'string' => 'String 3',
                    'int'    => 3,
                    'float'  => 3.3,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[7]['id'],
                    'string' => 'String 2',
                    'int'    => 2,
                    'float'  => 2.2,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[8]['id'],
                    'string' => 'String 1',
                    'int'    => 1,
                    'float'  => 1.1,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[9]['id'],
                    'string' => 'String 0',
                    'int'    => 0,
                    'float'  => 0.0,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::SORTER => [
                        [
                            'column'    => 'string',
                            'direction' => 'ASC',
                        ],
                    ],
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 0',
                    'int'    => 0,
                    'float'  => 0.0,
                    'bool'   => TRUE,
                    'date'   => $this->today->format(self::DATETIME),
                ], [
                    'id'     => $result[1]['id'],
                    'string' => 'String 1',
                    'int'    => 1,
                    'float'  => 1.1,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[2]['id'],
                    'string' => 'String 2',
                    'int'    => 2,
                    'float'  => 2.2,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[3]['id'],
                    'string' => 'String 3',
                    'int'    => 3,
                    'float'  => 3.3,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[4]['id'],
                    'string' => 'String 4',
                    'int'    => 4,
                    'float'  => 4.4,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[5]['id'],
                    'string' => 'String 5',
                    'int'    => 5,
                    'float'  => 5.5,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[6]['id'],
                    'string' => 'String 6',
                    'int'    => 6,
                    'float'  => 6.6,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[7]['id'],
                    'string' => 'String 7',
                    'int'    => 7,
                    'float'  => 7.7,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[8]['id'],
                    'string' => 'String 8',
                    'int'    => 8,
                    'float'  => 8.8,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[9]['id'],
                    'string' => 'String 9',
                    'int'    => 9,
                    'float'  => 9.9,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::SORTER => [
                        [
                            'column'    => 'string',
                            'direction' => 'DESC',
                        ],
                    ],
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 9',
                    'int'    => 9,
                    'float'  => 9.9,
                    'bool'   => FALSE,
                    'date'   => $this->today->format(self::DATETIME),
                ], [
                    'id'     => $result[1]['id'],
                    'string' => 'String 8',
                    'int'    => 8,
                    'float'  => 8.8,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[2]['id'],
                    'string' => 'String 7',
                    'int'    => 7,
                    'float'  => 7.7,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[3]['id'],
                    'string' => 'String 6',
                    'int'    => 6,
                    'float'  => 6.6,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[4]['id'],
                    'string' => 'String 5',
                    'int'    => 5,
                    'float'  => 5.5,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[5]['id'],
                    'string' => 'String 4',
                    'int'    => 4,
                    'float'  => 4.4,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[6]['id'],
                    'string' => 'String 3',
                    'int'    => 3,
                    'float'  => 3.3,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[7]['id'],
                    'string' => 'String 2',
                    'int'    => 2,
                    'float'  => 2.2,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[8]['id'],
                    'string' => 'String 1',
                    'int'    => 1,
                    'float'  => 1.1,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[9]['id'],
                    'string' => 'String 0',
                    'int'    => 0,
                    'float'  => 0.0,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::SORTER => [
                        [
                            'column'    => 'int',
                            'direction' => 'ASC',
                        ],
                    ],
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 0',
                    'int'    => 0,
                    'float'  => 0.0,
                    'bool'   => TRUE,
                    'date'   => $this->today->format(self::DATETIME),
                ], [
                    'id'     => $result[1]['id'],
                    'string' => 'String 1',
                    'int'    => 1,
                    'float'  => 1.1,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[2]['id'],
                    'string' => 'String 2',
                    'int'    => 2,
                    'float'  => 2.2,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[3]['id'],
                    'string' => 'String 3',
                    'int'    => 3,
                    'float'  => 3.3,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[4]['id'],
                    'string' => 'String 4',
                    'int'    => 4,
                    'float'  => 4.4,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[5]['id'],
                    'string' => 'String 5',
                    'int'    => 5,
                    'float'  => 5.5,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[6]['id'],
                    'string' => 'String 6',
                    'int'    => 6,
                    'float'  => 6.6,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[7]['id'],
                    'string' => 'String 7',
                    'int'    => 7,
                    'float'  => 7.7,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[8]['id'],
                    'string' => 'String 8',
                    'int'    => 8,
                    'float'  => 8.8,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[9]['id'],
                    'string' => 'String 9',
                    'int'    => 9,
                    'float'  => 9.9,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::SORTER => [
                        [
                            'column'    => 'int',
                            'direction' => 'DESC',
                        ],
                    ],
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 9',
                    'int'    => 9,
                    'float'  => 9.9,
                    'bool'   => FALSE,
                    'date'   => $this->today->format(self::DATETIME),
                ], [
                    'id'     => $result[1]['id'],
                    'string' => 'String 8',
                    'int'    => 8,
                    'float'  => 8.8,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[2]['id'],
                    'string' => 'String 7',
                    'int'    => 7,
                    'float'  => 7.7,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[3]['id'],
                    'string' => 'String 6',
                    'int'    => 6,
                    'float'  => 6.6,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[4]['id'],
                    'string' => 'String 5',
                    'int'    => 5,
                    'float'  => 5.5,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[5]['id'],
                    'string' => 'String 4',
                    'int'    => 4,
                    'float'  => 4.4,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[6]['id'],
                    'string' => 'String 3',
                    'int'    => 3,
                    'float'  => 3.3,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[7]['id'],
                    'string' => 'String 2',
                    'int'    => 2,
                    'float'  => 2.2,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[8]['id'],
                    'string' => 'String 1',
                    'int'    => 1,
                    'float'  => 1.1,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[9]['id'],
                    'string' => 'String 0',
                    'int'    => 0,
                    'float'  => 0.0,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::SORTER => [
                        [
                            'column'    => 'float',
                            'direction' => 'ASC',
                        ],
                    ],
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 0',
                    'int'    => 0,
                    'float'  => 0.0,
                    'bool'   => TRUE,
                    'date'   => $this->today->format(self::DATETIME),
                ], [
                    'id'     => $result[1]['id'],
                    'string' => 'String 1',
                    'int'    => 1,
                    'float'  => 1.1,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[2]['id'],
                    'string' => 'String 2',
                    'int'    => 2,
                    'float'  => 2.2,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[3]['id'],
                    'string' => 'String 3',
                    'int'    => 3,
                    'float'  => 3.3,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[4]['id'],
                    'string' => 'String 4',
                    'int'    => 4,
                    'float'  => 4.4,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[5]['id'],
                    'string' => 'String 5',
                    'int'    => 5,
                    'float'  => 5.5,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[6]['id'],
                    'string' => 'String 6',
                    'int'    => 6,
                    'float'  => 6.6,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[7]['id'],
                    'string' => 'String 7',
                    'int'    => 7,
                    'float'  => 7.7,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[8]['id'],
                    'string' => 'String 8',
                    'int'    => 8,
                    'float'  => 8.8,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[9]['id'],
                    'string' => 'String 9',
                    'int'    => 9,
                    'float'  => 9.9,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::SORTER => [
                        [
                            'column'    => 'float',
                            'direction' => 'DESC',
                        ],
                    ],
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 9',
                    'int'    => 9,
                    'float'  => 9.9,
                    'bool'   => FALSE,
                    'date'   => $this->today->format(self::DATETIME),
                ], [
                    'id'     => $result[1]['id'],
                    'string' => 'String 8',
                    'int'    => 8,
                    'float'  => 8.8,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[2]['id'],
                    'string' => 'String 7',
                    'int'    => 7,
                    'float'  => 7.7,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[3]['id'],
                    'string' => 'String 6',
                    'int'    => 6,
                    'float'  => 6.6,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[4]['id'],
                    'string' => 'String 5',
                    'int'    => 5,
                    'float'  => 5.5,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[5]['id'],
                    'string' => 'String 4',
                    'int'    => 4,
                    'float'  => 4.4,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[6]['id'],
                    'string' => 'String 3',
                    'int'    => 3,
                    'float'  => 3.3,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[7]['id'],
                    'string' => 'String 2',
                    'int'    => 2,
                    'float'  => 2.2,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[8]['id'],
                    'string' => 'String 1',
                    'int'    => 1,
                    'float'  => 1.1,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[9]['id'],
                    'string' => 'String 0',
                    'int'    => 0,
                    'float'  => 0.0,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::SORTER => [
                        [
                            'column'    => 'bool',
                            'direction' => 'ASC',
                        ],
                    ],
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 7',
                    'int'    => 7,
                    'float'  => 7.7,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('7 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[1]['id'],
                    'string' => 'String 9',
                    'int'    => 9,
                    'float'  => 9.9,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('2 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[2]['id'],
                    'string' => 'String 3',
                    'int'    => 3,
                    'float'  => 3.3,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('- 6 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[3]['id'],
                    'string' => 'String 5',
                    'int'    => 5,
                    'float'  => 5.5,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('2 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[4]['id'],
                    'string' => 'String 1',
                    'int'    => 1,
                    'float'  => 1.1,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('- 4 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[5]['id'],
                    'string' => 'String 8',
                    'int'    => 8,
                    'float'  => 8.8,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('7 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[6]['id'],
                    'string' => 'String 4',
                    'int'    => 4,
                    'float'  => 4.4,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('- 4 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[7]['id'],
                    'string' => 'String 0',
                    'int'    => 0,
                    'float'  => 0.0,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('- 4 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[8]['id'],
                    'string' => 'String 2',
                    'int'    => 2,
                    'float'  => 2.2,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('2 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[9]['id'],
                    'string' => 'String 6',
                    'int'    => 6,
                    'float'  => 6.6,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('4 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::SORTER => [
                        [
                            'column'    => 'bool',
                            'direction' => 'DESC',
                        ],
                    ],
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 8',
                    'int'    => 8,
                    'float'  => 8.8,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('2 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[1]['id'],
                    'string' => 'String 4',
                    'int'    => 4,
                    'float'  => 4.4,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-4 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[2]['id'],
                    'string' => 'String 0',
                    'int'    => 0,
                    'float'  => 0.0,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-4 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[3]['id'],
                    'string' => 'String 2',
                    'int'    => 2,
                    'float'  => 2.2,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('2 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[4]['id'],
                    'string' => 'String 6',
                    'int'    => 6,
                    'float'  => 6.6,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('4 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[5]['id'],
                    'string' => 'String 3',
                    'int'    => 3,
                    'float'  => 3.3,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('-3 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[6]['id'],
                    'string' => 'String 7',
                    'int'    => 7,
                    'float'  => 7.7,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('4 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[7]['id'],
                    'string' => 'String 1',
                    'int'    => 1,
                    'float'  => 1.1,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('- 6 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[8]['id'],
                    'string' => 'String 9',
                    'int'    => 9,
                    'float'  => 9.9,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('8 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[9]['id'],
                    'string' => 'String 5',
                    'int'    => 5,
                    'float'  => 5.5,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('- 4 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::SORTER => [
                        [
                            'column'    => 'date',
                            'direction' => 'ASC',
                        ],
                    ],
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 0',
                    'int'    => 0,
                    'float'  => 0.0,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-5 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[1]['id'],
                    'string' => 'String 1',
                    'int'    => 1,
                    'float'  => 1.1,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[2]['id'],
                    'string' => 'String 2',
                    'int'    => 2,
                    'float'  => 2.2,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[3]['id'],
                    'string' => 'String 3',
                    'int'    => 3,
                    'float'  => 3.3,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[4]['id'],
                    'string' => 'String 4',
                    'int'    => 4,
                    'float'  => 4.4,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[5]['id'],
                    'string' => 'String 5',
                    'int'    => 5,
                    'float'  => 5.5,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[6]['id'],
                    'string' => 'String 6',
                    'int'    => 6,
                    'float'  => 6.6,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[7]['id'],
                    'string' => 'String 7',
                    'int'    => 7,
                    'float'  => 7.7,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[8]['id'],
                    'string' => 'String 8',
                    'int'    => 8,
                    'float'  => 8.8,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[9]['id'],
                    'string' => 'String 9',
                    'int'    => 9,
                    'float'  => 9.9,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::SORTER => [
                        [
                            'column'    => 'date',
                            'direction' => 'DESC',
                        ],
                    ],
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 9',
                    'int'    => 9,
                    'float'  => 9.9,
                    'bool'   => FALSE,
                    'date'   => $this->today->format(self::DATETIME),
                ], [
                    'id'     => $result[1]['id'],
                    'string' => 'String 8',
                    'int'    => 8,
                    'float'  => 8.8,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[2]['id'],
                    'string' => 'String 7',
                    'int'    => 7,
                    'float'  => 7.7,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[3]['id'],
                    'string' => 'String 6',
                    'int'    => 6,
                    'float'  => 6.6,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[4]['id'],
                    'string' => 'String 5',
                    'int'    => 5,
                    'float'  => 5.5,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[5]['id'],
                    'string' => 'String 4',
                    'int'    => 4,
                    'float'  => 4.4,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[6]['id'],
                    'string' => 'String 3',
                    'int'    => 3,
                    'float'  => 3.3,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[7]['id'],
                    'string' => 'String 2',
                    'int'    => 2,
                    'float'  => 2.2,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[8]['id'],
                    'string' => 'String 1',
                    'int'    => 1,
                    'float'  => 1.1,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[9]['id'],
                    'string' => 'String 0',
                    'int'    => 0,
                    'float'  => 0.0,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        try {
            (new DocumentFilter($this->dm))->getData(
                new GridRequestDto(
                    [
                        self::SORTER => [
                            [
                                'column'    => 'Unknown',
                                'direction' => 'ASC',
                            ],
                        ],
                    ],
                ),
            )->toArray();
            self::assertEquals(TRUE, FALSE);
        } catch (Exception $e) {
            self::assertEquals(GridException::SORT_COLS_ERROR, $e->getCode());
            self::assertEquals(
                "Column 'Unknown' cannot be used for sorting! Have you forgotten add it to 'MongoDataGridTests\Filter\DocumentFilter::orderCols'?",
                $e->getMessage(),
            );
        }
    }

    /**
     * @throws Exception
     */
    public function testConditions(): void
    {
        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER => [
                        [
                            [
                                'column'   => 'string',
                                'value'    => ['String 1'],
                                'operator' => 'EQ',
                            ],
                        ],
                    ],
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 1',
                    'int'    => 1,
                    'float'  => 1.1,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER => [
                        [
                            [
                                'column'   => 'int',
                                'value'    => [2],
                                'operator' => 'EQ',
                            ],
                        ],
                    ],
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 2',
                    'int'    => 2,
                    'float'  => 2.2,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER => [
                        [
                            [
                                'column'   => 'float',
                                'value'    => [3.3],
                                'operator' => 'EQ',
                            ],
                        ],
                    ],
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 3',
                    'int'    => 3,
                    'float'  => 3.3,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER => [
                        [
                            [
                                'column'   => 'bool',
                                'value'    => [TRUE],
                                'operator' => 'EQ',
                            ],
                        ],
                        [
                            [
                                'column'   => 'string',
                                'value'    => ['String 4'],
                                'operator' => 'EQ',
                            ],
                        ],
                    ],
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 4',
                    'int'    => 4,
                    'float'  => 4.4,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER => [
                        [
                            [
                                'column'   => 'date',
                                'value'    => [(clone $this->today)->modify('1 day')->format(self::DATETIME)],
                                'operator' => 'EQ',
                            ],
                        ],
                    ],
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 5',
                    'int'    => 5,
                    'float'  => 5.5,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $dto    = new GridRequestDto(
            [
                self::FILTER => [
                    [
                        [
                            'column'   => 'int',
                            'value'    => [6, 7, 8],
                            'operator' => 'EQ',
                        ],
                    ],
                ],
            ],
        );
        $result = (new DocumentFilter($this->dm))->getData($dto)->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 6',
                    'int'    => 6,
                    'float'  => 6.6,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[1]['id'],
                    'string' => 'String 7',
                    'int'    => 7,
                    'float'  => 7.7,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[2]['id'],
                    'string' => 'String 8',
                    'int'    => 8,
                    'float'  => 8.8,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );
        self::assertEquals(
            [
                'filter'       => '[[{"column":"int","value":[6,7,8],"operator":"EQ"}]]',
                'page'         => 1,
                'search'       => NULL,
                'itemsPerPage' => 10,
                'total'        => 3,
                'sorter'       => NULL,
            ],
            $dto->getParamsForHeader(),
        );
        self::assertEquals(3, $dto->getTotal());

        $dto    = new GridRequestDto([self::SEARCH => 'String 9']);
        $result = (new DocumentFilter($this->dm))->getData($dto)->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 9',
                    'int'    => 9,
                    'float'  => 9.9,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),

                ],
            ],
            $result,
        );
        self::assertEquals(
            [
                'filter'       => '[]',
                'search'       => 'String 9',
                'page'         => 1,
                'itemsPerPage' => 10,
                'total'        => 1,
                'sorter'       => NULL,
            ],
            $dto->getParamsForHeader(),
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER => [
                        [
                            [
                                'column'   => 'int',
                                'value'    => [8],
                                'operator' => 'GTE',
                            ],
                        ],
                    ],
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 8',
                    'int'    => 8,
                    'float'  => 8.8,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),

                ], [
                    'id'     => $result[1]['id'],
                    'string' => 'String 9',
                    'int'    => 9,
                    'float'  => 9.9,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),

                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER => [
                        [
                            [
                                'column'   => 'int',
                                'value'    => [8],
                                'operator' => 'GT',
                            ],
                        ],
                    ],
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 9',
                    'int'    => 9,
                    'float'  => 9.9,
                    'bool'   => FALSE,
                    'date'   => $this->today->format(self::DATETIME),

                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER => [
                        [
                            [
                                'column'   => 'int',
                                'value'    => [1],
                                'operator' => 'LT',
                            ],
                        ],
                    ],
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 0',
                    'int'    => 0,
                    'float'  => 0.0,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-9 day')->format(self::DATETIME),

                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER => [
                        [
                            [
                                'column'   => 'int',
                                'value'    => [1],
                                'operator' => 'LTE',
                            ],
                        ],
                    ],
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 0',
                    'int'    => 0,
                    'float'  => 0.0,
                    'bool'   => TRUE,
                    'date'   => $this->today->format(self::DATETIME),

                ], [
                    'id'     => $result[1]['id'],
                    'string' => 'String 1',
                    'int'    => 1,
                    'float'  => 1.1,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),

                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER => [
                        [
                            [
                                'column'   => 'custom_string',
                                'value'    => ['String 0'],
                                'operator' => 'EQ',
                            ],
                        ],
                    ],
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 0',
                    'int'    => 0,
                    'float'  => 0.0,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER => [
                        [
                            [
                                'column'   => 'string',
                                'operator' => 'EMPTY',
                            ],
                        ],
                    ],
                ],
            ),
        )->toArray();
        self::assertEquals([], $result);

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER => [
                        [
                            [
                                'column'   => 'string',
                                'operator' => 'NEMPTY',
                            ],
                        ],
                    ],
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 0',
                    'int'    => 0,
                    'float'  => 0.0,
                    'bool'   => TRUE,
                    'date'   => $this->today->format(self::DATETIME),
                ], [
                    'id'     => $result[1]['id'],
                    'string' => 'String 1',
                    'int'    => 1,
                    'float'  => 1.1,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[2]['id'],
                    'string' => 'String 2',
                    'int'    => 2,
                    'float'  => 2.2,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[3]['id'],
                    'string' => 'String 3',
                    'int'    => 3,
                    'float'  => 3.3,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[4]['id'],
                    'string' => 'String 4',
                    'int'    => 4,
                    'float'  => 4.4,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[5]['id'],
                    'string' => 'String 5',
                    'int'    => 5,
                    'float'  => 5.5,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[6]['id'],
                    'string' => 'String 6',
                    'int'    => 6,
                    'float'  => 6.6,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[7]['id'],
                    'string' => 'String 7',
                    'int'    => 7,
                    'float'  => 7.7,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[8]['id'],
                    'string' => 'String 8',
                    'int'    => 8,
                    'float'  => 8.8,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[9]['id'],
                    'string' => 'String 9',
                    'int'    => 9,
                    'float'  => 9.9,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            (new GridRequestDto(
                [
                    self::FILTER => [
                        [
                            [
                                'column'   => 'string',
                                'operator' => 'NEMPTY',
                            ],
                        ],
                    ],
                ],
            ))->setAdditionalFilters(
                [
                    [
                        [
                            'column'   => 'string',
                            'operator' => 'EMPTY',
                        ],
                    ],
                ],
            ),
        )->toArray();
        self::assertEquals([], $result);

        $dto    = new GridRequestDto(
            [
                self::SEARCH => 'Unknown',
            ],
        );
        $result = (new DocumentFilter($this->dm))->getData($dto)->toArray();
        self::assertEquals([], $result);
        self::assertEquals(
            [
                'filter'       => '[]',
                'page'         => 1,
                'itemsPerPage' => 10,
                'search'       => 'Unknown',
                'total'        => 0,
                'sorter'       => NULL,
            ],
            $dto->getParamsForHeader(),
        );

        try {
            (new DocumentFilter($this->dm))->getData(
                new GridRequestDto(
                    [
                        self::FILTER => [
                            [
                                [
                                    'column'   => 'Unknown',
                                    'operator' => 'EQ',
                                    'value'    => 'abc',
                                ],
                            ],
                        ],
                    ],
                ),
            )->toArray();
            self::assertEquals(TRUE, FALSE);
        } catch (Exception $e) {
            self::assertEquals(GridException::FILTER_COLS_ERROR, $e->getCode());
            self::assertEquals(
                "Column 'Unknown' cannot be used for filtering! Have you forgotten add it to 'MongoDataGridTests\Filter\DocumentFilter::filterCols'?",
                $e->getMessage(),
            );
        }

        $documentFilter = (new DocumentFilter($this->dm));
        $this->setProperty($documentFilter, 'searchableCols', []);
        try {
            $documentFilter->getData(
                new GridRequestDto(
                    [
                        self::SEARCH => 'Unknown',
                    ],
                ),
            )->toArray();
            self::assertEquals(TRUE, FALSE);
        } catch (Exception $e) {
            self::assertEquals(GridException::SEARCHABLE_COLS_ERROR, $e->getCode());
            self::assertEquals(
                "Column cannot be used for searching! Have you forgotten add it to 'MongoDataGridTests\Filter\DocumentFilter::searchableCols'?",
                $e->getMessage(),
            );
        }

        $this->dm->getSchemaManager()->deleteDocumentIndexes(Document::class);
        $documentFilter = (new DocumentFilter($this->dm));
        try {
            $documentFilter->getData(
                new GridRequestDto(
                    [
                        self::SEARCH => 'Unknown',
                    ],
                ),
            )->toArray();
            self::assertEquals(TRUE, FALSE);
        } catch (Throwable $e) {
            self::assertEquals(
                "Column cannot be used for searching! Missing TEXT index on 'MongoDataGridTests\Filter\DocumentFilter::searchableCols' fields!",
                $e->getMessage(),
            );
        }
    }

    /**
     * @throws Exception
     */
    public function testAdvancedConditions(): void
    {
        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER =>
                        [
                            [
                                [
                                    'column'   => 'string',
                                    'operator' => DocumentFilter::EQ,
                                    'value'    => 'String 1',
                                ],
                            ],
                        ]
                    ,
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 1',
                    'int'    => 1,
                    'float'  => 1.1,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER =>
                        [
                            [
                                [
                                    'column'   => 'int',
                                    'operator' => DocumentFilter::EQ,
                                    'value'    => 2,
                                ],
                            ],
                        ]
                    ,
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 2',
                    'int'    => 2,
                    'float'  => 2.2,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER =>
                        [
                            [
                                [
                                    'column'   => 'float',
                                    'operator' => DocumentFilter::EQ,
                                    'value'    => 3.3,
                                ],
                            ],
                        ]
                    ,
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 3',
                    'int'    => 3,
                    'float'  => 3.3,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER =>
                        [
                            [
                                [
                                    'column'   => 'bool',
                                    'operator' => DocumentFilter::EQ,
                                    'value'    => TRUE,
                                ],
                            ], [
                                [
                                    'column'   => 'string',
                                    'operator' => DocumentFilter::EQ,
                                    'value'    => 'String 4',
                                ],
                            ],
                        ]
                    ,
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 4',
                    'int'    => 4,
                    'float'  => 4.4,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER =>
                        [
                            [
                                [
                                    'column'   => 'date',
                                    'operator' => DocumentFilter::EQ,
                                    'value'    => (clone $this->today)->modify('1 day')->format(self::DATETIME),
                                ],
                            ],
                        ]
                    ,
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 5',
                    'int'    => 5,
                    'float'  => 5.5,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $dto    = new GridRequestDto(
            [
                self::FILTER =>
                    [
                        [
                            [
                                'column'   => 'int',
                                'operator' => DocumentFilter::EQ,
                                'value'    => [6, 7, 8],
                            ],
                        ],
                    ]
                ,
            ],
        );
        $result = (new DocumentFilter($this->dm))->getData($dto)->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 6',
                    'int'    => 6,
                    'float'  => 6.6,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[1]['id'],
                    'string' => 'String 7',
                    'int'    => 7,
                    'float'  => 7.7,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[2]['id'],
                    'string' => 'String 8',
                    'int'    => 8,
                    'float'  => 8.8,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );
        self::assertEquals(3, $dto->getTotal());

        $dto    = new GridRequestDto(
            [
                self::FILTER =>
                    [
                        [
                            [
                                'column'   => 'string',
                                'operator' => DocumentFilter::EQ,
                                'value'    => 'String 9',
                            ],
                        ],
                    ]
                ,
            ],
        );
        $result = (new DocumentFilter($this->dm))->getData($dto)->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 9',
                    'int'    => 9,
                    'float'  => 9.9,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER =>
                        [
                            [
                                [
                                    'column'   => 'int',
                                    'operator' => DocumentFilter::GTE,
                                    'value'    => 8,
                                ],
                            ],
                        ]
                    ,
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 8',
                    'int'    => 8,
                    'float'  => 8.8,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[1]['id'],
                    'string' => 'String 9',
                    'int'    => 9,
                    'float'  => 9.9,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER =>
                        [
                            [
                                [
                                    'column'   => 'int',
                                    'operator' => DocumentFilter::GT,
                                    'value'    => 8,
                                ],
                            ],
                        ]
                    ,
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 9',
                    'int'    => 9,
                    'float'  => 9.9,
                    'bool'   => FALSE,
                    'date'   => $this->today->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER =>
                        [
                            [
                                [
                                    'column'   => 'int',
                                    'operator' => DocumentFilter::LT,
                                    'value'    => 1,
                                ],
                            ],
                        ]
                    ,
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 0',
                    'int'    => 0,
                    'float'  => 0.0,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-9 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER =>
                        [
                            [
                                [
                                    'column'   => 'int',
                                    'operator' => DocumentFilter::LTE,
                                    'value'    => 1,
                                ],
                            ],
                        ]
                    ,
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 0',
                    'int'    => 0,
                    'float'  => 0.0,
                    'bool'   => TRUE,
                    'date'   => $this->today->format(self::DATETIME),
                ], [
                    'id'     => $result[1]['id'],
                    'string' => 'String 1',
                    'int'    => 1,
                    'float'  => 1.1,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER =>
                        [
                            [
                                [
                                    'column'   => 'custom_string',
                                    'operator' => DocumentFilter::EQ,
                                    'value'    => ['String 0'],
                                ],
                            ],
                        ]
                    ,
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 0',
                    'int'    => 0,
                    'float'  => 0.0,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER =>
                        [
                            [
                                [
                                    'column'   => 'string',
                                    'operator' => DocumentFilter::EMPTY,
                                ],
                                [
                                    'column'   => 'string',
                                    'operator' => 'Unknown',
                                    'value'    => 'Unknown',
                                ],
                            ],
                        ]
                    ,
                ],
            ),
        )->toArray();
        self::assertEquals([], $result);

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER =>
                        [
                            [
                                [
                                    'column'   => 'string',
                                    'operator' => DocumentFilter::NEMPTY,
                                ],
                            ],
                        ]
                    ,
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 0',
                    'int'    => 0,
                    'float'  => 0.0,
                    'bool'   => TRUE,
                    'date'   => $this->today->format(self::DATETIME),
                ], [
                    'id'     => $result[1]['id'],
                    'string' => 'String 1',
                    'int'    => 1,
                    'float'  => 1.1,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[2]['id'],
                    'string' => 'String 2',
                    'int'    => 2,
                    'float'  => 2.2,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[3]['id'],
                    'string' => 'String 3',
                    'int'    => 3,
                    'float'  => 3.3,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[4]['id'],
                    'string' => 'String 4',
                    'int'    => 4,
                    'float'  => 4.4,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[5]['id'],
                    'string' => 'String 5',
                    'int'    => 5,
                    'float'  => 5.5,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[6]['id'],
                    'string' => 'String 6',
                    'int'    => 6,
                    'float'  => 6.6,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[7]['id'],
                    'string' => 'String 7',
                    'int'    => 7,
                    'float'  => 7.7,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[8]['id'],
                    'string' => 'String 8',
                    'int'    => 8,
                    'float'  => 8.8,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[9]['id'],
                    'string' => 'String 9',
                    'int'    => 9,
                    'float'  => 9.9,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            (new GridRequestDto(
                [
                    self::FILTER =>
                        [
                            [
                                [
                                    'column'   => 'string',
                                    'operator' => DocumentFilter::NEMPTY,
                                    'value'    => '_MODIFIER_VAL_NOT_NULL',
                                ],
                            ],
                        ],
                ],
            ))->setAdditionalFilters(
                [
                    [
                        [
                            'column'   => 'string',
                            'operator' => 'EMPTY',
                        ],
                    ],
                ],
            ),
        )->toArray();
        self::assertEquals([], $result);

        $dto    = new GridRequestDto(
            [
                self::SEARCH => 'Unknown',
            ],
        );
        $result = (new DocumentFilter($this->dm))->getData($dto)->toArray();
        self::assertEquals([], $result);

        try {
            (new DocumentFilter($this->dm))->getData(
                new GridRequestDto(
                    [
                        self::FILTER =>
                            [
                                [
                                    [
                                        'column'   => 'Unknown',
                                        'operator' => DocumentFilter::EQ,
                                        'value'    => '',
                                    ],
                                ],
                            ]
                        ,
                    ],
                ),
            )->toArray();
            self::assertEquals(TRUE, FALSE);
        } catch (Exception $e) {
            self::assertEquals(GridException::FILTER_COLS_ERROR, $e->getCode());
            self::assertEquals(
                "Column 'Unknown' cannot be used for filtering! Have you forgotten add it to 'MongoDataGridTests\Filter\DocumentFilter::filterCols'?",
                $e->getMessage(),
            );
        }

        $documentFilter = (new DocumentFilter($this->dm));
        $this->setProperty($documentFilter, 'searchableCols', []);
        try {
            $documentFilter->getData(
                new GridRequestDto(
                    [
                        self::FILTER =>
                            [
                                [
                                    [
                                        'column'   => '_MODIFIER_SEARCH',
                                        'operator' => DocumentFilter::EQ,
                                        'value'    => 'Unknown',
                                    ],
                                ],
                            ]
                        ,
                    ],
                ),
            )->toArray();
            self::assertEquals(TRUE, FALSE);
        } catch (Exception $e) {
            self::assertEquals(GridException::FILTER_COLS_ERROR, $e->getCode());
            self::assertEquals(
                "Column '_MODIFIER_SEARCH' cannot be used for filtering! Have you forgotten add it to 'MongoDataGridTests\Filter\DocumentFilter::filterCols'?",
                $e->getMessage(),
            );
        }

        try {
            (new DocumentFilter($this->dm))->getData(
                new GridRequestDto(
                    [
                        self::FILTER =>
                            [
                                [
                                    [
                                        'Unknown' => 'Unknown',
                                    ],
                                ],
                            ]
                        ,
                    ],
                ),
            )->toArray();
            self::assertEquals(TRUE, FALSE);
        } catch (LogicException $e) {
            self::assertEquals("Advanced filter must have 'column', 'operator' and 'value' field!", $e->getMessage());
        }

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER =>
                        [
                            [
                                [
                                    'column'   => 'string',
                                    'operator' => DocumentFilter::EQ,
                                    'value'    => ['String 0', 'String 1'],
                                ],
                            ],
                        ]
                    ,
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 0',
                    'int'    => 0,
                    'float'  => 0.0,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-9 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[1]['id'],
                    'string' => 'String 1',
                    'int'    => 1,
                    'float'  => 1.1,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER =>
                        [
                            [
                                [
                                    'column'   => 'string',
                                    'operator' => DocumentFilter::NEQ,
                                    'value'    => [
                                        'String 0',
                                        'String 1',
                                        'String 3',
                                        'String 4',
                                        'String 5',
                                        'String 6',
                                        'String 7',
                                        'String 8',
                                        'String 9',
                                    ],
                                ],
                            ],
                        ]
                    ,
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 2',
                    'int'    => 2,
                    'float'  => 2.2,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER =>
                        [
                            [
                                [
                                    'column'   => 'string',
                                    'operator' => DocumentFilter::IN,
                                    'value'    => [
                                        'String 0',
                                        'String 1',
                                    ],
                                ],
                            ],
                        ]
                    ,
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 0',
                    'int'    => 0,
                    'float'  => 0.0,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-2 day')->format(self::DATETIME),
                ],
                [
                    'id'     => $result[1]['id'],
                    'string' => 'String 1',
                    'int'    => 1,
                    'float'  => 1.1,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER =>
                        [
                            [
                                [
                                    'column'   => 'string',
                                    'operator' => DocumentFilter::NIN,
                                    'value'    => [
                                        'String 2',
                                        'String 3',
                                        'String 4',
                                        'String 5',
                                        'String 6',
                                        'String 7',
                                        'String 8',
                                        'String 9',
                                    ],
                                ],
                            ],
                        ]
                    ,
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 0',
                    'int'    => 0,
                    'float'  => 0.0,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ],
                [
                    'id'     => $result[1]['id'],
                    'string' => 'String 1',
                    'int'    => 1,
                    'float'  => 1.1,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER =>
                        [
                            [
                                [
                                    'column'   => 'string',
                                    'operator' => DocumentFilter::STARTS,
                                    'value'    => 'St',
                                ],
                            ], [
                                [
                                    'column'   => 'string',
                                    'operator' => DocumentFilter::LIKE,
                                    'value'    => 'ri',
                                ],
                            ], [
                                [
                                    'column'   => 'string',
                                    'operator' => DocumentFilter::ENDS,
                                    'value'    => 'ng 3',
                                ],
                            ],
                        ]
                    ,
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 3',
                    'int'    => 3,
                    'float'  => 3.3,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('2 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER =>
                        [
                            [
                                [
                                    'column'   => 'int',
                                    'operator' => DocumentFilter::BETWEEN,
                                    'value'    => [4, 7],
                                ], [
                                    'column'   => 'int',
                                    'operator' => DocumentFilter::BETWEEN,
                                    'value'    => [5],
                                ],
                            ], [
                                [
                                    'column'   => 'float',
                                    'operator' => DocumentFilter::NBETWEEN,
                                    'value'    => [1.1, 3.3],
                                ],
                                [
                                    'column'   => 'float',
                                    'operator' => DocumentFilter::NBETWEEN,
                                    'value'    => 2.2,
                                ],
                            ], [
                                [
                                    'column'   => 'float',
                                    'operator' => DocumentFilter::NBETWEEN,
                                    'value'    => [6.6, 9.9],
                                ],
                            ],
                        ]
                    ,
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 4',
                    'int'    => 4,
                    'float'  => 4.4,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[1]['id'],
                    'string' => 'String 5',
                    'int'    => 5,
                    'float'  => 5.5,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[2]['id'],
                    'string' => 'String 6',
                    'int'    => 6,
                    'float'  => 6.6,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );

        $result = (new DocumentFilter($this->dm))->getData(
            new GridRequestDto(
                [
                    self::FILTER =>
                        [
                            [
                                [
                                    'column'   => 'string',
                                    'operator' => DocumentFilter::EQ,
                                    'value'    => 'String 5',
                                ], [
                                    'column'   => 'custom_string',
                                    'operator' => DocumentFilter::EQ,
                                    'value'    => ['String 5'],
                                ],
                            ], [
                                [
                                    'column'   => 'int',
                                    'operator' => DocumentFilter::GTE,
                                    'value'    => 5,
                                ], [
                                    'column'   => 'int',
                                    'operator' => DocumentFilter::LTE,
                                    'value'    => 5,
                                ],
                            ],
                        ]
                    ,
                ],
            ),
        )->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 5',
                    'int'    => 5,
                    'float'  => 5.5,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );
    }

    /**
     * @throws Exception
     */
    public function testPagination(): void
    {
        $dto    = new GridRequestDto(
            [
                self::SORTER    => [
                    [
                        'column'    => 'id',
                        'direction' => 'ASC',
                    ],
                ], self::PAGING => [self::PAGE => '3', self::ITEMS_PER_PAGE => '2'],
            ],
        );
        $result = (new DocumentFilter($this->dm))->getData($dto)->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 4',
                    'int'    => 4,
                    'float'  => 4.4,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('4 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[1]['id'],
                    'string' => 'String 5',
                    'int'    => 5,
                    'float'  => 5.5,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );
        self::assertEquals(
            [
                'filter'       => '[]',
                'sorter'       => '[{"column":"id","direction":"ASC"}]',
                'page'         => 3,
                'itemsPerPage' => 2,
                'search'       => NULL,
                'total'        => 10,
            ],
            $dto->getParamsForHeader(),
        );

        $dto    = (new GridRequestDto(
            [
                self::SORTER    => [
                    [
                        'column'    => 'id',
                        'direction' => 'ASC',
                    ],
                ], self::PAGING => [self::PAGE => '3'],
            ],
        ))->setItemsPerPage(2);
        $result = (new DocumentFilter($this->dm))->getData($dto)->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 4',
                    'int'    => 4,
                    'float'  => 4.4,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[1]['id'],
                    'string' => 'String 5',
                    'int'    => 5,
                    'float'  => 5.5,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );
        self::assertEquals(
            [
                'filter'       => '[]',
                'sorter'       => '[{"column":"id","direction":"ASC"}]',
                'page'         => 3,
                'itemsPerPage' => 2,
                'search'       => NULL,
                'total'        => 10,
            ],
            $dto->getParamsForHeader(),
        );

        $document = (new DocumentFilter($this->dm));

        $this->setProperty($document, 'countQuery', NULL);
        $dto    = new GridRequestDto(
            [
                self::SORTER    => [
                    ['direction' => 'ASC', 'column' => 'id'],
                ], self::PAGING => [self::PAGE => '3', self::ITEMS_PER_PAGE => '2'],
            ],
        );
        $result = $document->getData($dto)->toArray();
        self::assertEquals(
            [
                [
                    'id'     => $result[0]['id'],
                    'string' => 'String 4',
                    'int'    => 4,
                    'float'  => 4.4,
                    'bool'   => TRUE,
                    'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
                ], [
                    'id'     => $result[1]['id'],
                    'string' => 'String 5',
                    'int'    => 5,
                    'float'  => 5.5,
                    'bool'   => FALSE,
                    'date'   => $this->today->modify('1 day')->format(self::DATETIME),
                ],
            ],
            $result,
        );
        self::assertEquals(
            [
                'filter'       => '[]',
                'sorter'       => '[{"direction":"ASC","column":"id"}]',
                'page'         => 3,
                'itemsPerPage' => 2,
                'search'       => NULL,
                'total'        => 10,
            ],
            $dto->getParamsForHeader(),
        );
    }

    /**
     * @throws Exception
     */
    public function testSearchCallback(): void
    {
        $dto    = new GridRequestDto(
            [
                self::SEARCH => 'Unknown',
            ],
        );
        $result = (new DocumentFilter($this->dm))->getData($dto)->toArray();
        self::assertEquals([], $result);
        self::assertEquals(
            [
                'filter'       => '[]',
                'page'         => 1,
                'itemsPerPage' => 10,
                'search'       => 'Unknown',
                'total'        => 0,
                'sorter'       => NULL,
            ],
            $dto->getParamsForHeader(),
        );
    }

    /**
     * @throws Exception
     */
    public function testSearchBadSearchFields(): void
    {
        $dto = new GridRequestDto(
            [
                self::SEARCH => 'Unknown',
            ],
        );
        $f   = new DocumentFilter($this->dm);

        $this->setProperty($f, 'filterCols', []);

        self::expectException(GridException::class);
        self::expectExceptionCode(GridException::SEARCHABLE_COLS_ERROR);
        $f->getData($dto)->toArray();
    }

    /**
     * @throws Exception
     */
    public function testGetDataThrow(): void
    {
        $dto = self::createPartialMock(GridRequestDto::class, ['setTotal']);
        $dto->method('setTotal')->willThrowException(new CommandException('', 123));
        $this->setProperty($dto, 'headers', []);

        self::expectException(CommandException::class);
        self::expectExceptionCode(123);
        (new DocumentFilter($this->dm))->getData($dto)->toArray();
    }

    /**
     * @throws Exception
     */
    public function testGetOrderBy(): void
    {
        $dto = new GridRequestDto(
            [
                self::SORTER => [[]],
            ],
        );

        self::expectException(GridException::class);
        $dto->getOrderBy();
    }

    /**
     * @throws Exception
     */
    public function testGetOrderByBadFormat(): void
    {
        $dto = new GridRequestDto(
            [
                self::SORTER => ['a'],
            ],
        );

        self::expectException(GridException::class);
        $dto->getOrderBy();
    }

    /**
     * @throws Exception
     */
    public function testGetOrderByBadDirection(): void
    {
        $dto = new GridRequestDto(
            [
                self::SORTER => [['column' => 'a', 'direction' => 'b']],
            ],
        );

        self::expectException(GridException::class);
        $dto->getOrderBy();
    }

    /**
     * @throws Exception
     */
    public function testGetFilterBadFormat(): void
    {
        $dto = new GridRequestDto(
            [
                self::FILTER => ['a'],
            ],
        );

        self::expectException(GridException::class);
        $dto->getFilter(FALSE);
    }

    /**
     * @throws Exception
     */
    public function testGetFilterBadFormat2(): void
    {
        $dto = new GridRequestDto(
            [
                self::FILTER => [[[]]],
            ],
        );

        self::expectException(GridException::class);
        $dto->getFilter(FALSE);
    }

    /**
     * @throws Exception
     */
    public function testGetFilter(): void
    {
        $dto = new GridRequestDto(
            [
                self::FILTER => [[['column' => 'a', 'operator' => 'b']]],
            ],
        );

        self::assertNotEmpty($dto->getFilter(FALSE));
    }

    /**
     * @throws Exception
     */
    public function testGetDataNoCountQuery(): void
    {
        $dto = new GridRequestDto(
            [
                self::SEARCH => 'Unknown',
            ],
        );
        $f   = $this->getMockBuilder(DocumentFilter::class)
            ->setMethods(['configCustomCountQuery'])
            ->setConstructorArgs([$this->dm])
            ->getMock();
        $f->method('configCustomCountQuery')->willReturn(NULL);
        $this->setProperty($f, 'dm', $this->dm);

        $result = $f->getData($dto)->toArray();
        self::assertEquals([], $result);
        self::assertEquals(
            [
                'filter'       => '[]',
                'page'         => 1,
                'itemsPerPage' => 10,
                'search'       => 'Unknown',
                'total'        => 0,
                'sorter'       => NULL,
            ],
            $dto->getParamsForHeader(),
        );
    }

    /**
     * @throws Exception
     */
    public function testGetGridResponse(): void
    {
        $dto    = new GridRequestDto([]);
        $result = GridFilterAbstract::getGridResponse($dto, (new DocumentFilter($this->dm))->getData($dto)->toArray());

        $result['items'] = [];

        self::assertEquals(
            [
                'items'  => [],
                'filter' => [],
                'sorter' => [],
                'search' => NULL,
                'paging' => [
                    'page'         => 1,
                    'itemsPerPage' => 10,
                    'total'        => 10,
                    'nextPage'     => 1,
                    'lastPage'     => 1,
                    'previousPage' => 1,
                ],
            ],
            $result,
        );
    }

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->today = new DateTime('today', new DateTimeZone('UTC'));

        for ($i = 0; $i < 10; $i++) {
            $this->dm->persist(
                (new Document())
                    ->setString(sprintf('String %s', $i))
                    ->setInt($i)
                    ->setFloat((float) sprintf('%s.%s', $i, $i))
                    ->setBool($i % 2 === 0)
                    ->setDate(new DateTime(sprintf('today +%s day', $i), new DateTimeZone('UTC'))),
            );
        }

        $this->dm->flush();
    }

}
