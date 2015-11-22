<?php

namespace Booking\Token;


use Booking\Store\InfluxStore;
use InfluxDB\Point;

class Statistics
{
    /**
     * @var InfluxStore
     */
    private $store;


    /**
     * Statistics constructor.
     * @param InfluxStore $store
     */
    public function __construct(InfluxStore $store)
    {
        $this->store = $store;
    }

    public function storeTokenUsage()
    {
        $this->store->writePoints(
            [new Point('tickets.token', 1)]
        );
    }
}