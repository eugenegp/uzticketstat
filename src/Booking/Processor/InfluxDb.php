<?php

namespace  Booking\Processor;

use Booking\Adapter\ArrayToPoint;
use InfluxDB\Client;
use InfluxDB\Database;

class InfluxDb
{
    public function write($trainsList)
    {
        $pointsList = ArrayToPoint::convert($trainsList);

        $database = Client::fromDSN(sprintf('influxdb://%s:%s@%s:%s/%s', INFDB_USER, INFDB_PASS, INFDB_HOST, INFDB_PORT, INFDB_DB));
        $result = $database->writePoints($pointsList, Database::PRECISION_SECONDS);
        dump($result);
    }
}