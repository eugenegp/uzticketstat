<?php

namespace Booking\Console\Command;

use Booking\Event\TicketEvent;
use Booking\Schedule\Operator;
use Booking\Queue\RabbitMq;
use Booking\Sale\SalePerson;
use Booking\Store\Adapter\JsonToPoint;
use Booking\Store\InfluxStore;
use Booking\Token\Api\TokenApiClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MonitorCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('monitor:train')
            ->setDescription('Run worker daemon to process events from queue')
            ->addArgument('from', InputArgument::REQUIRED, 'From station')
            ->addArgument('to', InputArgument::REQUIRED, 'To station')
            ->addArgument('date', InputArgument::REQUIRED, 'Travel date');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $salePerson = new SalePerson(
            new Operator(new TokenApiClient(API_URI)),
            new InfluxStore(new JsonToPoint())
        );
        $queue = new RabbitMq(MQ_HOST, MQ_PORT, 'guest', 'guest', $salePerson);
        $event = new TicketEvent(
            $input->getArgument('from'),
            $input->getArgument('to'),
            new \DateTime($input->getArgument('date'))
        );
        $queue->addMessage($event);
    }
}