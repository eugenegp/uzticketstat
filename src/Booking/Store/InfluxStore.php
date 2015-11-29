<?php

namespace Booking\Store;

use InfluxDB\Client;
use InfluxDB\Database;

class InfluxStore
{

    /** @var  Connection */
    private $connection;

    /**
     * InfluxStore constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function write(array $points)
    {
        $result = $this->getConnect()->writePoints($points, Database::PRECISION_SECONDS);
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

    public function query($query)
    {
        return $this->getConnect()->query($query);
    }


    private function getConnect()
    {
        return $this->connection->getConnect();
    }

}