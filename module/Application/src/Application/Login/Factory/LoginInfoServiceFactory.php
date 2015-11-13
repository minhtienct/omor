<?php

namespace Application\Login\Factory;

use Zend\ServiceManager\FactoryInterface;
use Application\Login\Service\LoginInfoService;
use Zend\ServiceManager\ServiceLocatorInterface;

class LoginInfoServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new LoginInfoService($serviceLocator);
    }
}
