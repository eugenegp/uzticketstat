<?php

namespace Booking\Console\Command;

use Booking\Event\TicketEvent;
use Booking\Schedule\Operator;
use Booking\Store\Adapter\JsonToPoint;
use Booking\Store\InfluxStore;
use Booking\Sale\SalePerson;
use Booking\Token\Api\TokenApiClient;
use Booking\Token\Statistics;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BookindLoadCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('loader:uz')
            ->setDescription('DEPRECATED. Check tickets on active order')
            ->addArgument('from', InputArgument::REQUIRED, 'From station')
            ->addArgument('to', InputArgument::REQUIRED, 'To station')
            ->addArgument('date', InputArgument::REQUIRED, 'Travel date')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $dateTime = new \DateTime($input->getArgument('date'));
        $event = new TicketEvent($input->getArgument('from'), $input->getArgument('to'), $dateTime);
        $store = new InfluxStore(new JsonToPoint());
        $salePerson = new SalePerson(
            new Operator(new TokenApiClient(API_URI, new Statistics($store))),
            $store);
        $salePerson->checkAvailableTicket($event);
    }

}