<?php

namespace  Booking\Store;

use Booking\Store\Adapter\JsonToPoint;
use InfluxDB\Client;
use InfluxDB\Database;

class InfluxStore
{
    /**
     * @var JsonToPoint
     */
    private $adapter;

    /** @var  Connection */
    private $connection;

    /**
     * InfluxStore constructor.
     * @param JsonToPoint $adapter
     * @param Connection $connection
     */
    public function __construct(JsonToPoint $adapter, Connection $connection)
    {
        $this->adapter = $adapter;
        $this->connection = $connection;
    }

    public function writeJsonData($jsonData)
    {
        $pointsList = $this->adapter->convert($jsonData);

        if (empty($pointsList)) {
            return;
        }

        $result = $this->getConnect()->writePoints($pointsList, Database::PRECISION_SECONDS);
        if (!$result) {
            throw new \Exception('Fail to insert data to InfluxDB');
        }
    }

    public function writePoints(array $points)
    {
        $result = $this->getConnect()->writePoints($points, Database::PRECISION_SECONDS);
        if (!$result) {
            throw new \Exception('Fail to insert data to InfluxDB');
        }
    }

    private function getConnect()
    {
        return $this->connection->getConnect();
    }
}