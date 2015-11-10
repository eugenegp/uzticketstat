<?php

namespace Booking\Processor;


use Booking\Loader\UzLoader;

class TicketProcessor
{
    /**
     * @var UzLoader
     */
    private $loader;
    /**
     * @var InfluxDb
     */
    private $db;

    /**
     * TicketProcessor constructor.
     * @param UzLoader $loader
     * @param InfluxDb $db
     */
    public function __construct(UzLoader $loader, InfluxDb $db)
    {
        $this->loader = $loader;
        $this->db = $db;
    }

    /**
     *
     */
    public function checkAvailableTicket($from, $to, \DateTime $date)
    {
        $loader = new UzLoader();
        $trainsData = $loader->load($from, $to, $date);
        $data = json_decode($trainsData);
        $idbProcessor = new InfluxDb();
        $idbProcessor->write($data);
    }
}