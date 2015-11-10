<?php

namespace Booking\Console\Command;

use Booking\Loader\UzLoader;
use Booking\Processor\InfluxDb;
use Booking\Processor\TicketProcessor;
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
            ->setDescription('Check tickets on active order')
            ->addArgument('from', InputArgument::REQUIRED, 'From station')
            ->addArgument('to', InputArgument::REQUIRED, 'To station')
            ->addArgument('date', InputArgument::REQUIRED, 'Travel date')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $dateTime = new \DateTime($input->getArgument('date'));
        $processor = new TicketProcessor(new UzLoader(), new InfluxDb());
        $processor->checkAvailableTicket($input->getArgument('from'), $input->getArgument('to'), $dateTime);
    }

}