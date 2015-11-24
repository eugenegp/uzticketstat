<?php

namespace Booking\Console\Command;

use Booking\Event\TicketEvent;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BookindLoadCommand extends ContainerAwareCommand
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
        $salePerson = $this->getContainer()->get('sale');
        $salePerson->checkAvailableTicket($event);
    }

}