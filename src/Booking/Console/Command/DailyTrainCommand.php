<?php

namespace Booking\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DailyTrainCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('monitor:daily')
            ->setDescription('Add new job to queue before 45 day.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}