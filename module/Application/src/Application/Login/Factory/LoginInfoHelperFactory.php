<?php

namespace Application\Login\Factory;

use Zend\ServiceManager\FactoryInterface;
use Application\Login\Helper\LoginInfoHelper;
use Zend\ServiceManager\ServiceLocatorInterface;

class LoginInfoHelperFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new LoginInfoHelper($serviceLocator->getServiceLocator());
    }
}
