<?php

namespace Booking\Sale;


use Booking\Event\TicketEvent;
use Booking\Schedule\Operator;
use Booking\Store\InfluxStore;
use InfluxDB\Point;

class SalePerson
{
    /**
     * @var Operator
     */
    private $schedule;
    /**
     * @var InfluxStore
     */
    private $store;

    /**
     * TicketProcessor constructor.
     * @param Operator $schedule
     * @param InfluxStore $store
     */
    public function __construct(Operator $schedule, InfluxStore $store)
    {
        $this->schedule = $schedule;
        $this->store = $store;
    }

    /**
     * @param TicketEvent $event
     * @throws \Exception
     */
    public function checkAvailableTicket(TicketEvent $event)
    {
        $trainsData = $this->schedule->search(
            $event->getFromStationId(),
            $event->getToStationId(),
            $event->getDate()
        );
        $this->store->writeJsonData($trainsData);

        $this->store->writePoints(
            [new Point(
                'ticket.uz.request',
                1,
                [
                    'from' => $event->getFromStationId(),
                    'to' => $event->getToStationId(),
                    'date' => $event->getDate()->format('Y-m-d'),
                ]
            )]
        );
    }
}