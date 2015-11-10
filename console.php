#!/usr/bin/env php
<?php

define('ROOT_DIR', realpath(__DIR__));
define('INFDB_USER', 'root');
define('INFDB_PASS', 'root');
define('INFDB_HOST', 'localhost');
define('INFDB_PORT', '8086');
define('INFDB_DB', 'tickets');
require __DIR__.'/vendor/autoload.php';

use Booking\Console\Command\BookindLoadCommand;
use Booking\Console\Command\ConsumerCommand;
use Booking\Console\Command\ProducerCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new BookindLoadCommand());
$application->add(new ConsumerCommand());
$application->add(new ProducerCommand());
$application->run();
