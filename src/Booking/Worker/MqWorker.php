<?php

namespace Booking\Worker;


use Booking\Event\TicketEvent;
use Booking\Loader\UzLoader;
use Booking\Processor\InfluxDb;
use Booking\Processor\TicketProcessor;

class MqWorker
{

    public function process(TicketEvent $event)
    {
        $processor = new TicketProcessor(new UzLoader(), new InfluxDb());
        $processor->checkAvailableTicket($event->getFromStationId(), $event->getToStationId(), $event->getDate());
    }
}