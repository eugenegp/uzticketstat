<?php

namespace Booking\Store;


use InfluxDB\Client;

class Connection
{
    private $host;
    private $port;
    private $user;
    private $password;
    private $db;
    private $connection;


    /**
     * Connection constructor.
     * @param $host
     * @param $port
     * @param $user
     * @param $password
     * @param $db
     */
    public function __construct($host, $port, $user, $password, $db)
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
        $this->db = $db;
    }

    public function getConnect()
    {
        if (!$this->connection) {
            $this->connection = Client::fromDSN(sprintf('influxdb://%s:%s@%s:%s/%s', $this->user, $this->password, $this->host, $this->port, $this->db));
        }
        return $this->connection;
    }
}