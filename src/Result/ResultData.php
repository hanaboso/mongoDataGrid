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
     * @var Query<mixed>
     */
    private Query $query;

    /**
     * ResultData constructor.
     *
     * @param Query<mixed> $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
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
                    switch (get_class($innerItem)) {
                        case ObjectId::class:
                            /** @var ObjectId $tt */
                            $tt               = $innerItem;
                            $data[$key]['id'] = (string) $tt;
                            unset($data[$key][$innerKey]);

                            break;
                        case UTCDateTime::class:
                            /** @var UTCDateTime $tt */
                            $tt                    = $innerItem;
                            $data[$key][$innerKey] = $tt->toDateTime()->format(DateTimeUtils::DATE_TIME);

                            break;
                    }
                }
            }
        }

        return array_values($data);
    }

}