<?php

namespace Booking\Console\Command;

use Booking\Queue\RabbitMq;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SalePersonCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sales:start')
            ->setDescription('Run worker daemon to process messages from queue')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var RabbitMq $queue */
        $queue = $this->getContainer()->get('queue');
        $queue->waitingForCall();
    }


}