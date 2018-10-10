<?php declare(strict_types=1);

namespace Hanaboso\MongoDataGrid\Result;

use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ODM\MongoDB\Query\Query;
use MongoDate;
use MongoId;

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
    private $query;

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
        $data = $this->query->execute()->toArray();

        foreach ($data as $key => $item) {
            foreach ($item as $innerKey => $innerItem) {
                if (is_object($innerItem)) {
                    switch (get_class($innerItem)) {
                        case MongoId::class:
                            /** @var MongoId $innerItem */
                            $data[$key][$innerKey] = (string) $innerItem;
                            break;
                        case MongoDate::class:
                            /** @var MongoDate $innerItem */
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