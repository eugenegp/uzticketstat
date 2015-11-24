<?php

namespace Booking\Console\Command;

use Booking\Event\TicketEvent;
use Booking\Queue\RabbitMq;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MonitorCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('monitor:train')
            ->setDescription('Run worker daemon to process events from queue ./console monitor:train 2210800 2200001 2015-12-26')
            ->addArgument('from', InputArgument::REQUIRED, 'From station')
            ->addArgument('to', InputArgument::REQUIRED, 'To station')
            ->addArgument('date', InputArgument::REQUIRED, 'Travel date');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $event = new TicketEvent(
            $input->getArgument('from'),
            $input->getArgument('to'),
            new \DateTime($input->getArgument('date'))
        );
        /** @var RabbitMq $queue */
        $queue = $this->getContainer()->get('queue');
        $queue->addMessage($event);
    }
}