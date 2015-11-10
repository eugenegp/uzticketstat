<?php

namespace Booking\Event;

class TicketEvent
{
    private $fromStationId;

    private $toStationId;

    private $date;

    /**
     * TicketEvent constructor.
     * @param $fromStationId
     * @param $toStationId
     * @param $date
     */
    public function __construct($fromStationId, $toStationId, \DateTime $date)
    {
        $this->fromStationId = $fromStationId;
        $this->toStationId = $toStationId;
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getFromStationId()
    {
        return $this->fromStationId;
    }

    /**
     * @return mixed
     */
    public function getToStationId()
    {
        return $this->toStationId;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

 }