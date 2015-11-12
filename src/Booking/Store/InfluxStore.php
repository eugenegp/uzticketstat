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
        $database = Client::fromDSN(sprintf('influxdb://%s:%s@%s:%s/%s', INFDB_USER, INFDB_PASS, INFDB_HOST, INFDB_PORT, INFDB_DB));
        $result = $database->writePoints($pointsList, Database::PRECISION_SECONDS);
        if (!$result) {
            throw new \Exception('Fail to insert data to InfluxDB');
        }
    }
}