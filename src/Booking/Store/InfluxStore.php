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

    /** @var  \InfluxDB\Database */
    private $connection;

    /**
     * InfluxStore constructor.
     * @param JsonToPoint $adapter
     */
    public function __construct(JsonToPoint $adapter)
    {
        $this->adapter = $adapter;
    }

    public function writeJsonData($jsonData)
    {
        $pointsList = $this->adapter->convert($jsonData);

        if (empty($pointsList)) {
            return;
        }

        if (!$this->connection) {
            $this->connect();
        }

        $result = $this->connection->writePoints($pointsList, Database::PRECISION_SECONDS);
        if (!$result) {
            throw new \Exception('Fail to insert data to InfluxDB');
        }
    }

    public function writePoints(array $points)
    {
        if (!$this->connection) {
            $this->connect();
        }

        $result = $this->connection->writePoints($points, Database::PRECISION_SECONDS);
        if (!$result) {
            throw new \Exception('Fail to insert data to InfluxDB');
        }
    }

    private function connect()
    {
        $this->connection = Client::fromDSN(sprintf('influxdb://%s:%s@%s:%s/%s', INFDB_USER, INFDB_PASS, INFDB_HOST, INFDB_PORT, INFDB_DB));
    }
}