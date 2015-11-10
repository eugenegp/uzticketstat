<?php

namespace Booking\Console\Command;

use Booking\Event\TicketEvent;
use Booking\Loader\UzLoader;
use Booking\Processor\InfluxDb;
use Booking\Queue\RabbitMq;
use Booking\Worker\MqWorker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProducerCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('produce:train')
            ->setDescription('Run worker daemon to process events from queue')
            ->addArgument('from', InputArgument::REQUIRED, 'From station')
            ->addArgument('to', InputArgument::REQUIRED, 'To station')
            ->addArgument('date', InputArgument::REQUIRED, 'Travel date');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $worker = new RabbitMq('localhost', 5672, 'guest', 'guest', new MqWorker());
        $event = new TicketEvent($input->getArgument('from'), $input->getArgument('to'), new \DateTime($input->getArgument('date')));
        $worker->addMessage($event);
    }
}