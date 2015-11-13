<?php

namespace Application\Login\Factory;

use Zend\ServiceManager\FactoryInterface;
use Application\Login\Service\LoginService;
use Zend\ServiceManager\ServiceLocatorInterface;

class LoginServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new LoginService($serviceLocator);
    }
}
