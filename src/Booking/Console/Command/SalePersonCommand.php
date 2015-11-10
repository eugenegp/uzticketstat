<?php

namespace Booking\Console\Command;

use Booking\Schedule\Operator;
use Booking\Queue\RabbitMq;
use Booking\Sale\SalePerson;
use Booking\Store\Adapter\JsonToPoint;
use Booking\Store\InfluxStore;
use Booking\Token\Api\TokenApiClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SalePersonCommand extends Command
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
        $salePerson = new SalePerson(
            new Operator(new TokenApiClient(API_URI)),
            new InfluxStore(new JsonToPoint())
        );
        $queue = new RabbitMq(MQ_HOST, MQ_PORT, 'guest', 'guest', $salePerson);
        $queue->waitingForCall();
    }


}