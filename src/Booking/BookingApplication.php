<?php

namespace Booking;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

use Symfony\Component\Console\Application;

class BookingApplication extends  Application
{
    private $version = 0.1;

    private $container;

    public function __construct($name = 'booking')
    {
        $this->initContainer();
        parent::__construct($name, $this->version);
    }

    private function initContainer()
    {
        $container = new ContainerBuilder();
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__));
        $loader->load('../../app/config/services.yml');
        $this->container = $container;
    }

    /**
     * @return mixed
     */
    public function getContainer()
    {
        return $this->container;
    }
}