<?php

namespace Booking\Console\Command;

use Booking\Loader\UzLoader;
use Booking\Processor\InfluxDb;
use Booking\Queue\RabbitMq;
use Booking\Worker\MqWorker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsumerCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('worker:start')
            ->setDescription('Run worker daemon to process events from queue')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $worker = new RabbitMq('localhost', 5672, 'guest', 'guest', new MqWorker());
        $worker->consume();
    }


}