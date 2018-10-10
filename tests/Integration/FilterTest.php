<?php declare(strict_types=1);

namespace Tests\Integration;

use DateTime;
use DateTimeZone;
use Exception;
use Hanaboso\MongoDataGrid\Exception\GridException;
use Hanaboso\MongoDataGrid\GridRequestDto;
use Tests\Document\Document;
use Tests\Filter\DocumentFilter;
use Tests\PrivateTrait;
use Tests\TestCaseAbstract;

/**
 * Class FilterTest
 *
 * @package Tests\Integration
 */
final class FilterTest extends TestCaseAbstract
{

    use PrivateTrait;

    private const DATETIME = 'Y-m-d H:i:s';

    private const ORDER  = 'orderBy';
    private const FILTER = 'filter';
    private const PAGE   = 'page';
    private const LIMIT  = 'limit';

    /**
     * @var DateTime
     */
    private $today;

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
                    ->setDate(new DateTime(sprintf('today +%s day', $i), new DateTimeZone('UTC')))
            );
        }

        $this->dm->flush();
    }

    /**
     * @throws Exception
     */
    public function testBasic(): void
    {
        $result = (new DocumentFilter($this->dm))->getData(new GridRequestDto([]))->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 0',
                'int'    => 0,
                'float'  => 0.0,
                'bool'   => TRUE,
                'date'   => $this->today->format(self::DATETIME),
            ], [
                '_id'    => $result[1]['_id'],
                'string' => 'String 1',
                'int'    => 1,
                'float'  => 1.1,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[2]['_id'],
                'string' => 'String 2',
                'int'    => 2,
                'float'  => 2.2,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[3]['_id'],
                'string' => 'String 3',
                'int'    => 3,
                'float'  => 3.3,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[4]['_id'],
                'string' => 'String 4',
                'int'    => 4,
                'float'  => 4.4,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[5]['_id'],
                'string' => 'String 5',
                'int'    => 5,
                'float'  => 5.5,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[6]['_id'],
                'string' => 'String 6',
                'int'    => 6,
                'float'  => 6.6,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[7]['_id'],
                'string' => 'String 7',
                'int'    => 7,
                'float'  => 7.7,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[8]['_id'],
                'string' => 'String 8',
                'int'    => 8,
                'float'  => 8.8,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[9]['_id'],
                'string' => 'String 9',
                'int'    => 9,
                'float'  => 9.9,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ],
        ], $result);
    }

    /**
     * @throws Exception
     */
    public function testSortations(): void
    {
        $result = (new DocumentFilter($this->dm))->getData(new GridRequestDto([self::ORDER => '+id']))->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 0',
                'int'    => 0,
                'float'  => 0.0,
                'bool'   => TRUE,
                'date'   => $this->today->format(self::DATETIME),
            ], [
                '_id'    => $result[1]['_id'],
                'string' => 'String 1',
                'int'    => 1,
                'float'  => 1.1,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[2]['_id'],
                'string' => 'String 2',
                'int'    => 2,
                'float'  => 2.2,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[3]['_id'],
                'string' => 'String 3',
                'int'    => 3,
                'float'  => 3.3,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[4]['_id'],
                'string' => 'String 4',
                'int'    => 4,
                'float'  => 4.4,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[5]['_id'],
                'string' => 'String 5',
                'int'    => 5,
                'float'  => 5.5,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[6]['_id'],
                'string' => 'String 6',
                'int'    => 6,
                'float'  => 6.6,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[7]['_id'],
                'string' => 'String 7',
                'int'    => 7,
                'float'  => 7.7,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[8]['_id'],
                'string' => 'String 8',
                'int'    => 8,
                'float'  => 8.8,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[9]['_id'],
                'string' => 'String 9',
                'int'    => 9,
                'float'  => 9.9,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ],
        ], $result);

        $result = (new DocumentFilter($this->dm))->getData(new GridRequestDto([self::ORDER => '-id']))->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 9',
                'int'    => 9,
                'float'  => 9.9,
                'bool'   => FALSE,
                'date'   => $this->today->format(self::DATETIME),
            ], [
                '_id'    => $result[1]['_id'],
                'string' => 'String 8',
                'int'    => 8,
                'float'  => 8.8,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[2]['_id'],
                'string' => 'String 7',
                'int'    => 7,
                'float'  => 7.7,
                'bool'   => FALSE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[3]['_id'],
                'string' => 'String 6',
                'int'    => 6,
                'float'  => 6.6,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[4]['_id'],
                'string' => 'String 5',
                'int'    => 5,
                'float'  => 5.5,
                'bool'   => FALSE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[5]['_id'],
                'string' => 'String 4',
                'int'    => 4,
                'float'  => 4.4,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[6]['_id'],
                'string' => 'String 3',
                'int'    => 3,
                'float'  => 3.3,
                'bool'   => FALSE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[7]['_id'],
                'string' => 'String 2',
                'int'    => 2,
                'float'  => 2.2,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[8]['_id'],
                'string' => 'String 1',
                'int'    => 1,
                'float'  => 1.1,
                'bool'   => FALSE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[9]['_id'],
                'string' => 'String 0',
                'int'    => 0,
                'float'  => 0.0,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ],
        ], $result);

        $result = (new DocumentFilter($this->dm))->getData(new GridRequestDto([self::ORDER => '+string']))->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 0',
                'int'    => 0,
                'float'  => 0.0,
                'bool'   => TRUE,
                'date'   => $this->today->format(self::DATETIME),
            ], [
                '_id'    => $result[1]['_id'],
                'string' => 'String 1',
                'int'    => 1,
                'float'  => 1.1,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[2]['_id'],
                'string' => 'String 2',
                'int'    => 2,
                'float'  => 2.2,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[3]['_id'],
                'string' => 'String 3',
                'int'    => 3,
                'float'  => 3.3,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[4]['_id'],
                'string' => 'String 4',
                'int'    => 4,
                'float'  => 4.4,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[5]['_id'],
                'string' => 'String 5',
                'int'    => 5,
                'float'  => 5.5,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[6]['_id'],
                'string' => 'String 6',
                'int'    => 6,
                'float'  => 6.6,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[7]['_id'],
                'string' => 'String 7',
                'int'    => 7,
                'float'  => 7.7,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[8]['_id'],
                'string' => 'String 8',
                'int'    => 8,
                'float'  => 8.8,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[9]['_id'],
                'string' => 'String 9',
                'int'    => 9,
                'float'  => 9.9,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ],
        ], $result);

        $result = (new DocumentFilter($this->dm))->getData(new GridRequestDto([self::ORDER => '-string']))->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 9',
                'int'    => 9,
                'float'  => 9.9,
                'bool'   => FALSE,
                'date'   => $this->today->format(self::DATETIME),
            ], [
                '_id'    => $result[1]['_id'],
                'string' => 'String 8',
                'int'    => 8,
                'float'  => 8.8,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[2]['_id'],
                'string' => 'String 7',
                'int'    => 7,
                'float'  => 7.7,
                'bool'   => FALSE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[3]['_id'],
                'string' => 'String 6',
                'int'    => 6,
                'float'  => 6.6,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[4]['_id'],
                'string' => 'String 5',
                'int'    => 5,
                'float'  => 5.5,
                'bool'   => FALSE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[5]['_id'],
                'string' => 'String 4',
                'int'    => 4,
                'float'  => 4.4,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[6]['_id'],
                'string' => 'String 3',
                'int'    => 3,
                'float'  => 3.3,
                'bool'   => FALSE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[7]['_id'],
                'string' => 'String 2',
                'int'    => 2,
                'float'  => 2.2,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[8]['_id'],
                'string' => 'String 1',
                'int'    => 1,
                'float'  => 1.1,
                'bool'   => FALSE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[9]['_id'],
                'string' => 'String 0',
                'int'    => 0,
                'float'  => 0.0,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ],
        ], $result);

        $result = (new DocumentFilter($this->dm))->getData(new GridRequestDto([self::ORDER => '+int']))->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 0',
                'int'    => 0,
                'float'  => 0.0,
                'bool'   => TRUE,
                'date'   => $this->today->format(self::DATETIME),
            ], [
                '_id'    => $result[1]['_id'],
                'string' => 'String 1',
                'int'    => 1,
                'float'  => 1.1,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[2]['_id'],
                'string' => 'String 2',
                'int'    => 2,
                'float'  => 2.2,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[3]['_id'],
                'string' => 'String 3',
                'int'    => 3,
                'float'  => 3.3,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[4]['_id'],
                'string' => 'String 4',
                'int'    => 4,
                'float'  => 4.4,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[5]['_id'],
                'string' => 'String 5',
                'int'    => 5,
                'float'  => 5.5,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[6]['_id'],
                'string' => 'String 6',
                'int'    => 6,
                'float'  => 6.6,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[7]['_id'],
                'string' => 'String 7',
                'int'    => 7,
                'float'  => 7.7,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[8]['_id'],
                'string' => 'String 8',
                'int'    => 8,
                'float'  => 8.8,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[9]['_id'],
                'string' => 'String 9',
                'int'    => 9,
                'float'  => 9.9,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ],
        ], $result);

        $result = (new DocumentFilter($this->dm))->getData(new GridRequestDto([self::ORDER => '-int']))->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 9',
                'int'    => 9,
                'float'  => 9.9,
                'bool'   => FALSE,
                'date'   => $this->today->format(self::DATETIME),
            ], [
                '_id'    => $result[1]['_id'],
                'string' => 'String 8',
                'int'    => 8,
                'float'  => 8.8,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[2]['_id'],
                'string' => 'String 7',
                'int'    => 7,
                'float'  => 7.7,
                'bool'   => FALSE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[3]['_id'],
                'string' => 'String 6',
                'int'    => 6,
                'float'  => 6.6,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[4]['_id'],
                'string' => 'String 5',
                'int'    => 5,
                'float'  => 5.5,
                'bool'   => FALSE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[5]['_id'],
                'string' => 'String 4',
                'int'    => 4,
                'float'  => 4.4,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[6]['_id'],
                'string' => 'String 3',
                'int'    => 3,
                'float'  => 3.3,
                'bool'   => FALSE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[7]['_id'],
                'string' => 'String 2',
                'int'    => 2,
                'float'  => 2.2,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[8]['_id'],
                'string' => 'String 1',
                'int'    => 1,
                'float'  => 1.1,
                'bool'   => FALSE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[9]['_id'],
                'string' => 'String 0',
                'int'    => 0,
                'float'  => 0.0,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ],
        ], $result);

        $result = (new DocumentFilter($this->dm))->getData(new GridRequestDto([self::ORDER => '+float']))->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 0',
                'int'    => 0,
                'float'  => 0.0,
                'bool'   => TRUE,
                'date'   => $this->today->format(self::DATETIME),
            ], [
                '_id'    => $result[1]['_id'],
                'string' => 'String 1',
                'int'    => 1,
                'float'  => 1.1,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[2]['_id'],
                'string' => 'String 2',
                'int'    => 2,
                'float'  => 2.2,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[3]['_id'],
                'string' => 'String 3',
                'int'    => 3,
                'float'  => 3.3,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[4]['_id'],
                'string' => 'String 4',
                'int'    => 4,
                'float'  => 4.4,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[5]['_id'],
                'string' => 'String 5',
                'int'    => 5,
                'float'  => 5.5,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[6]['_id'],
                'string' => 'String 6',
                'int'    => 6,
                'float'  => 6.6,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[7]['_id'],
                'string' => 'String 7',
                'int'    => 7,
                'float'  => 7.7,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[8]['_id'],
                'string' => 'String 8',
                'int'    => 8,
                'float'  => 8.8,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[9]['_id'],
                'string' => 'String 9',
                'int'    => 9,
                'float'  => 9.9,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ],
        ], $result);

        $result = (new DocumentFilter($this->dm))->getData(new GridRequestDto([self::ORDER => '-float']))->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 9',
                'int'    => 9,
                'float'  => 9.9,
                'bool'   => FALSE,
                'date'   => $this->today->format(self::DATETIME),
            ], [
                '_id'    => $result[1]['_id'],
                'string' => 'String 8',
                'int'    => 8,
                'float'  => 8.8,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[2]['_id'],
                'string' => 'String 7',
                'int'    => 7,
                'float'  => 7.7,
                'bool'   => FALSE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[3]['_id'],
                'string' => 'String 6',
                'int'    => 6,
                'float'  => 6.6,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[4]['_id'],
                'string' => 'String 5',
                'int'    => 5,
                'float'  => 5.5,
                'bool'   => FALSE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[5]['_id'],
                'string' => 'String 4',
                'int'    => 4,
                'float'  => 4.4,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[6]['_id'],
                'string' => 'String 3',
                'int'    => 3,
                'float'  => 3.3,
                'bool'   => FALSE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[7]['_id'],
                'string' => 'String 2',
                'int'    => 2,
                'float'  => 2.2,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[8]['_id'],
                'string' => 'String 1',
                'int'    => 1,
                'float'  => 1.1,
                'bool'   => FALSE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[9]['_id'],
                'string' => 'String 0',
                'int'    => 0,
                'float'  => 0.0,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ],
        ], $result);

        $result = (new DocumentFilter($this->dm))->getData(new GridRequestDto([self::ORDER => '+bool']))->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 1',
                'int'    => 1,
                'float'  => 1.1,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[1]['_id'],
                'string' => 'String 3',
                'int'    => 3,
                'float'  => 3.3,
                'bool'   => FALSE,
                'date'   => $this->today->modify('2 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[2]['_id'],
                'string' => 'String 5',
                'int'    => 5,
                'float'  => 5.5,
                'bool'   => FALSE,
                'date'   => $this->today->modify('2 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[3]['_id'],
                'string' => 'String 7',
                'int'    => 7,
                'float'  => 7.7,
                'bool'   => FALSE,
                'date'   => $this->today->modify('2 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[4]['_id'],
                'string' => 'String 9',
                'int'    => 9,
                'float'  => 9.9,
                'bool'   => FALSE,
                'date'   => $this->today->modify('2 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[5]['_id'],
                'string' => 'String 0',
                'int'    => 0,
                'float'  => 0.0,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-9 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[6]['_id'],
                'string' => 'String 2',
                'int'    => 2,
                'float'  => 2.2,
                'bool'   => TRUE,
                'date'   => $this->today->modify('2 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[7]['_id'],
                'string' => 'String 4',
                'int'    => 4,
                'float'  => 4.4,
                'bool'   => TRUE,
                'date'   => $this->today->modify('2 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[8]['_id'],
                'string' => 'String 6',
                'int'    => 6,
                'float'  => 6.6,
                'bool'   => TRUE,
                'date'   => $this->today->modify('2 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[9]['_id'],
                'string' => 'String 8',
                'int'    => 8,
                'float'  => 8.8,
                'bool'   => TRUE,
                'date'   => $this->today->modify('2 day')->format(self::DATETIME),
            ],
        ], $result);

        $result = (new DocumentFilter($this->dm))->getData(new GridRequestDto([self::ORDER => '-bool']))->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 0',
                'int'    => 0,
                'float'  => 0.0,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-8 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[1]['_id'],
                'string' => 'String 2',
                'int'    => 2,
                'float'  => 2.2,
                'bool'   => TRUE,
                'date'   => $this->today->modify('2 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[2]['_id'],
                'string' => 'String 4',
                'int'    => 4,
                'float'  => 4.4,
                'bool'   => TRUE,
                'date'   => $this->today->modify('2 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[3]['_id'],
                'string' => 'String 6',
                'int'    => 6,
                'float'  => 6.6,
                'bool'   => TRUE,
                'date'   => $this->today->modify('2 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[4]['_id'],
                'string' => 'String 8',
                'int'    => 8,
                'float'  => 8.8,
                'bool'   => TRUE,
                'date'   => $this->today->modify('2 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[5]['_id'],
                'string' => 'String 1',
                'int'    => 1,
                'float'  => 1.1,
                'bool'   => FALSE,
                'date'   => $this->today->modify('-7 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[6]['_id'],
                'string' => 'String 3',
                'int'    => 3,
                'float'  => 3.3,
                'bool'   => FALSE,
                'date'   => $this->today->modify('2 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[7]['_id'],
                'string' => 'String 5',
                'int'    => 5,
                'float'  => 5.5,
                'bool'   => FALSE,
                'date'   => $this->today->modify('2 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[8]['_id'],
                'string' => 'String 7',
                'int'    => 7,
                'float'  => 7.7,
                'bool'   => FALSE,
                'date'   => $this->today->modify('2 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[9]['_id'],
                'string' => 'String 9',
                'int'    => 9,
                'float'  => 9.9,
                'bool'   => FALSE,
                'date'   => $this->today->modify('2 day')->format(self::DATETIME),
            ],
        ], $result);

        $result = (new DocumentFilter($this->dm))->getData(new GridRequestDto([self::ORDER => '+date']))->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 0',
                'int'    => 0,
                'float'  => 0.0,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-9 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[1]['_id'],
                'string' => 'String 1',
                'int'    => 1,
                'float'  => 1.1,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[2]['_id'],
                'string' => 'String 2',
                'int'    => 2,
                'float'  => 2.2,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[3]['_id'],
                'string' => 'String 3',
                'int'    => 3,
                'float'  => 3.3,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[4]['_id'],
                'string' => 'String 4',
                'int'    => 4,
                'float'  => 4.4,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[5]['_id'],
                'string' => 'String 5',
                'int'    => 5,
                'float'  => 5.5,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[6]['_id'],
                'string' => 'String 6',
                'int'    => 6,
                'float'  => 6.6,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[7]['_id'],
                'string' => 'String 7',
                'int'    => 7,
                'float'  => 7.7,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[8]['_id'],
                'string' => 'String 8',
                'int'    => 8,
                'float'  => 8.8,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[9]['_id'],
                'string' => 'String 9',
                'int'    => 9,
                'float'  => 9.9,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ],
        ], $result);

        $result = (new DocumentFilter($this->dm))->getData(new GridRequestDto([self::ORDER => '-date']))->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 9',
                'int'    => 9,
                'float'  => 9.9,
                'bool'   => FALSE,
                'date'   => $this->today->format(self::DATETIME),
            ], [
                '_id'    => $result[1]['_id'],
                'string' => 'String 8',
                'int'    => 8,
                'float'  => 8.8,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[2]['_id'],
                'string' => 'String 7',
                'int'    => 7,
                'float'  => 7.7,
                'bool'   => FALSE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[3]['_id'],
                'string' => 'String 6',
                'int'    => 6,
                'float'  => 6.6,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[4]['_id'],
                'string' => 'String 5',
                'int'    => 5,
                'float'  => 5.5,
                'bool'   => FALSE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[5]['_id'],
                'string' => 'String 4',
                'int'    => 4,
                'float'  => 4.4,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[6]['_id'],
                'string' => 'String 3',
                'int'    => 3,
                'float'  => 3.3,
                'bool'   => FALSE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[7]['_id'],
                'string' => 'String 2',
                'int'    => 2,
                'float'  => 2.2,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[8]['_id'],
                'string' => 'String 1',
                'int'    => 1,
                'float'  => 1.1,
                'bool'   => FALSE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[9]['_id'],
                'string' => 'String 0',
                'int'    => 0,
                'float'  => 0.0,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ],
        ], $result);

        try {
            (new DocumentFilter($this->dm))->getData(new GridRequestDto([self::ORDER => '+Unknown']))->toArray();
            self::assertEquals(TRUE, FALSE);
        } catch (GridException $e) {
            $this->assertEquals(GridException::ORDER_COLS_ERROR, $e->getCode());
            $this->assertEquals(
                "Column 'Unknown' cannot be used for sorting! Have you forgotten add it to 'Tests\Filter\DocumentFilter::orderCols'?",
                $e->getMessage()
            );
        }
    }

    /**
     * @throws Exception
     */
    public function testConditions(): void
    {
        $result = (new DocumentFilter($this->dm))->getData(new GridRequestDto([
            self::FILTER => '{"string": "String 1"}',
        ]))->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 1',
                'int'    => 1,
                'float'  => 1.1,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ],
        ], $result);

        $result = (new DocumentFilter($this->dm))->getData(new GridRequestDto([
            self::FILTER => '{"int": 2}',
        ]))->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 2',
                'int'    => 2,
                'float'  => 2.2,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ],
        ], $result);

        $result = (new DocumentFilter($this->dm))->getData(new GridRequestDto([
            self::FILTER => '{"float": 3.3}',
        ]))->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 3',
                'int'    => 3,
                'float'  => 3.3,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ],
        ], $result);

        $result = (new DocumentFilter($this->dm))->getData(new GridRequestDto([
            self::FILTER => '{"bool": true, "string": "String 4"}',
        ]))->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 4',
                'int'    => 4,
                'float'  => 4.4,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ],
        ], $result);

        $result = (new DocumentFilter($this->dm))->getData(new GridRequestDto([
            self::FILTER => sprintf('{"date": "%s"}', (clone $this->today)->modify('1 day')->format(self::DATETIME)),
        ]))->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 5',
                'int'    => 5,
                'float'  => 5.5,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ],
        ], $result);

        $dto    = new GridRequestDto([self::FILTER => ['{"int": [6, 7, 8]}']]);
        $result = (new DocumentFilter($this->dm))->getData($dto)->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 6',
                'int'    => 6,
                'float'  => 6.6,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[1]['_id'],
                'string' => 'String 7',
                'int'    => 7,
                'float'  => 7.7,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[2]['_id'],
                'string' => 'String 8',
                'int'    => 8,
                'float'  => 8.8,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ],
        ], $result);
        self::assertEquals([
            'filter'  => ['int' => '6,7,8'],
            'page'    => 0,
            'limit'   => 10,
            'total'   => 10,
            'orderby' => NULL,
        ], $dto->getParamsForHeader());

        $dto    = new GridRequestDto([self::FILTER => '{"_MODIFIER_SEARCH": "9"}']);
        $result = (new DocumentFilter($this->dm))->getData($dto)->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 9',
                'int'    => 9,
                'float'  => 9.9,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),

            ],
        ], $result);
        self::assertEquals([
            'filter'  => ['search' => '9'],
            'page'    => 0,
            'limit'   => 10,
            'total'   => 10,
            'orderby' => NULL,
        ], $dto->getParamsForHeader());

        $result = (new DocumentFilter($this->dm))->getData(new GridRequestDto([
            self::FILTER => '{"int_gte": 8}',
        ]))->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 8',
                'int'    => 8,
                'float'  => 8.8,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),

            ], [
                '_id'    => $result[1]['_id'],
                'string' => 'String 9',
                'int'    => 9,
                'float'  => 9.9,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),

            ],
        ], $result);

        $result = (new DocumentFilter($this->dm))->getData(new GridRequestDto([
            self::FILTER => '{"int_gt": 8}',
        ]))->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 9',
                'int'    => 9,
                'float'  => 9.9,
                'bool'   => FALSE,
                'date'   => $this->today->format(self::DATETIME),

            ],
        ], $result);

        $result = (new DocumentFilter($this->dm))->getData(new GridRequestDto([
            self::FILTER => '{"int_lt": 1}',
        ]))->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 0',
                'int'    => 0,
                'float'  => 0.0,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-9 day')->format(self::DATETIME),

            ],
        ], $result);

        $result = (new DocumentFilter($this->dm))->getData(new GridRequestDto([
            self::FILTER => '{"int_lte": 1}',
        ]))->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 0',
                'int'    => 0,
                'float'  => 0.0,
                'bool'   => TRUE,
                'date'   => $this->today->format(self::DATETIME),

            ], [
                '_id'    => $result[1]['_id'],
                'string' => 'String 1',
                'int'    => 1,
                'float'  => 1.1,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),

            ],
        ], $result);

        $result = (new DocumentFilter($this->dm))->getData(new GridRequestDto([
            self::FILTER => '{"custom_string": "String 0"}',
        ]))->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 0',
                'int'    => 0,
                'float'  => 0.0,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ],
        ], $result);

        $result = (new DocumentFilter($this->dm))->getData(new GridRequestDto([
            self::FILTER => '{"string": null}',
        ]))->toArray();
        self::assertEquals([], $result);

        $result = (new DocumentFilter($this->dm))->getData(new GridRequestDto([
            self::FILTER => '{"string": "_MODIFIER_VAL_NOT_NULL"}',
        ]))->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 0',
                'int'    => 0,
                'float'  => 0.0,
                'bool'   => TRUE,
                'date'   => $this->today->format(self::DATETIME),
            ], [
                '_id'    => $result[1]['_id'],
                'string' => 'String 1',
                'int'    => 1,
                'float'  => 1.1,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[2]['_id'],
                'string' => 'String 2',
                'int'    => 2,
                'float'  => 2.2,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[3]['_id'],
                'string' => 'String 3',
                'int'    => 3,
                'float'  => 3.3,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[4]['_id'],
                'string' => 'String 4',
                'int'    => 4,
                'float'  => 4.4,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[5]['_id'],
                'string' => 'String 5',
                'int'    => 5,
                'float'  => 5.5,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[6]['_id'],
                'string' => 'String 6',
                'int'    => 6,
                'float'  => 6.6,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[7]['_id'],
                'string' => 'String 7',
                'int'    => 7,
                'float'  => 7.7,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[8]['_id'],
                'string' => 'String 8',
                'int'    => 8,
                'float'  => 8.8,
                'bool'   => TRUE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[9]['_id'],
                'string' => 'String 9',
                'int'    => 9,
                'float'  => 9.9,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ],
        ], $result);

        $result = (new DocumentFilter($this->dm))->getData((new GridRequestDto([
            self::FILTER => '{"string": "_MODIFIER_VAL_NOT_NULL"}',
        ]))->setAdditionalFilters(['string' => NULL]))->toArray();
        self::assertEquals([], $result);

        $dto    = new GridRequestDto([self::FILTER => '{"search": "Unknown"}']);
        $result = (new DocumentFilter($this->dm))->getData($dto)->toArray();
        self::assertEquals([], $result);
        self::assertEquals([
            'filter'  => ['search' => 'Unknown'],
            'page'    => 0,
            'limit'   => 10,
            'total'   => 10,
            'orderby' => NULL,
        ], $dto->getParamsForHeader());

        try {
            (new DocumentFilter($this->dm))->getData(new GridRequestDto([
                self::FILTER => '{"Unknown": ""}',
            ]))->toArray();
            self::assertEquals(TRUE, FALSE);
        } catch (GridException $e) {
            $this->assertEquals(GridException::FILTER_COLS_ERROR, $e->getCode());
            $this->assertEquals(
                "Column 'Unknown' cannot be used for filtering! Have you forgotten add it to 'Tests\Filter\DocumentFilter::filterCols'?",
                $e->getMessage()
            );
        }

        $documentFilter = (new DocumentFilter($this->dm));
        $this->setProperty($documentFilter, 'searchableCols', []);
        try {
            $documentFilter->getData(new GridRequestDto([
                self::FILTER => '{"_MODIFIER_SEARCH": "Unknown"}',
            ]))->toArray();
            self::assertEquals(TRUE, FALSE);
        } catch (GridException $e) {
            $this->assertEquals(GridException::SEARCHABLE_COLS_ERROR, $e->getCode());
            $this->assertEquals(
                "Column cannot be used for searching! Have you forgotten add it to 'Tests\Filter\DocumentFilter::searchableCols'?",
                $e->getMessage()
            );
        }
    }

    /**
     * @throws Exception
     */
    public function testPagination(): void
    {
        $dto    = new GridRequestDto([self::ORDER => '+id', self::PAGE => '3', self::LIMIT => '2']);
        $result = (new DocumentFilter($this->dm))->getData($dto)->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 4',
                'int'    => 4,
                'float'  => 4.4,
                'bool'   => TRUE,
                'date'   => $this->today->modify('4 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[1]['_id'],
                'string' => 'String 5',
                'int'    => 5,
                'float'  => 5.5,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ],
        ], $result);
        self::assertEquals([
            'filter'  => [],
            'orderby' => '+id',
            'page'    => 3,
            'limit'   => 2,
            'total'   => 10,
        ], $dto->getParamsForHeader());

        $dto    = (new GridRequestDto([self::ORDER => '+id', self::PAGE => '3']))->setLimit(2);
        $result = (new DocumentFilter($this->dm))->getData($dto)->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 4',
                'int'    => 4,
                'float'  => 4.4,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[1]['_id'],
                'string' => 'String 5',
                'int'    => 5,
                'float'  => 5.5,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ],
        ], $result);
        self::assertEquals([
            'filter'  => [],
            'orderby' => '+id',
            'page'    => 3,
            'limit'   => 2,
            'total'   => 10,
        ], $dto->getParamsForHeader());

        $document = (new DocumentFilter($this->dm));
        $this->setProperty($document, 'countQuery', NULL);
        $dto    = new GridRequestDto([self::ORDER => '+id', self::PAGE => '3', self::LIMIT => '2']);
        $result = $document->getData($dto)->toArray();
        self::assertEquals([
            [
                '_id'    => $result[0]['_id'],
                'string' => 'String 4',
                'int'    => 4,
                'float'  => 4.4,
                'bool'   => TRUE,
                'date'   => $this->today->modify('-1 day')->format(self::DATETIME),
            ], [
                '_id'    => $result[1]['_id'],
                'string' => 'String 5',
                'int'    => 5,
                'float'  => 5.5,
                'bool'   => FALSE,
                'date'   => $this->today->modify('1 day')->format(self::DATETIME),
            ],
        ], $result);
        self::assertEquals([
            'filter'  => [],
            'orderby' => '+id',
            'page'    => 3,
            'limit'   => 2,
            'total'   => 10,
        ], $dto->getParamsForHeader());
    }

}