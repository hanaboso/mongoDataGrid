<?php declare(strict_types=1);

namespace Hanaboso\MongoDataGrid\Result;

use Doctrine\ODM\MongoDB\Iterator\Iterator;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ODM\MongoDB\Query\Query;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

/**
 * Class ResultData
 *
 * @package Hanaboso\MongoDataGrid\Result
 */
class ResultData
{

    private const DATETIME = 'Y-m-d H:i:s';

    /**
     * @var Query
     */
    private Query $query;

    /**
     * ResultData constructor.
     *
     * @param Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * @return array
     * @throws MongoDBException
     */
    public function toArray(): array
    {
        /** @var Iterator $data */
        $data = $this->query->execute();
        $data = $data->toArray();

        foreach ($data as $key => $item) {
            foreach ($item as $innerKey => $innerItem) {
                if (is_object($innerItem)) {
                    switch (get_class($innerItem)) {
                        case ObjectId::class:
                            /** @var ObjectId $innerItem */
                            $data[$key]['id'] = (string) $innerItem;
                            unset($data[$key][$innerKey]);
                            break;
                        case UTCDateTime::class:
                            /** @var UTCDateTime $innerItem */
                            $data[$key][$innerKey] = $innerItem->toDateTime()->format(self::DATETIME);
                            break;
                        default:
                            break;
                    }
                }
            }
        }

        return array_values($data);
    }

}