<?php

namespace Application\Application\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Application\Service\SystemLog;

class SystemLogFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new SystemLog($serviceLocator);
    }
}
