#!/usr/bin/env php
<?php

define('ROOT_DIR', realpath(__DIR__));

define('INFDB_USER', 'root');
define('INFDB_PASS', 'root');
define('INFDB_HOST', 'influxdb');
define('INFDB_PORT', '8086');
define('INFDB_DB', 'tickets');

define('MQ_HOST', 'amqp');
define('MQ_PORT', 5672);

define('API_URI', 'http://tokenapi:8666');

require __DIR__.'/vendor/autoload.php';

use Booking\Console\Command\BookindLoadCommand;
use Booking\Console\Command\SalePersonCommand;
use Booking\Console\Command\MonitorCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new BookindLoadCommand());
$application->add(new SalePersonCommand());
$application->add(new MonitorCommand());
$application->run();
