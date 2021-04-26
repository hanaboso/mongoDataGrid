<?php declare(strict_types=1);

namespace Hanaboso\MongoDataGrid\Result;

use Doctrine\ODM\MongoDB\Iterator\Iterator;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ODM\MongoDB\Query\Query;
use Hanaboso\Utils\Date\DateTimeUtils;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

/**
 * Class ResultData
 *
 * @package Hanaboso\MongoDataGrid\Result
 */
class ResultData
{

    /**
     * ResultData constructor.
     *
     * @param Query<mixed> $query
     * @param string       $dateFormat
     */
    public function __construct(private Query $query, private string $dateFormat = DateTimeUtils::DATE_TIME)
    {
    }

    /**
     * @return mixed[]
     * @throws MongoDBException
     */
    public function toArray(): array
    {
        /** @var Iterator<mixed> $data */
        $data = $this->query->execute();
        $data = $data->toArray();

        foreach ($data as $key => $item) {
            foreach ($item as $innerKey => $innerItem) {
                if (is_object($innerItem)) {
                    switch ($innerItem::class) {
                        case ObjectId::class:
                            /** @var ObjectId $tt */
                            $tt               = $innerItem;
                            $data[$key]['id'] = (string) $tt;
                            unset($data[$key][$innerKey]);

                            break;
                        case UTCDateTime::class:
                            /** @var UTCDateTime $tt */
                            $tt                    = $innerItem;
                            $data[$key][$innerKey] = $tt->toDateTime()->format($this->dateFormat);

                            break;
                    }
                }
            }
        }

        return array_values($data);
    }

}
