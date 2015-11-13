<?php

namespace Application\Application\Factory;

use Zend\ServiceManager\FactoryInterface;
use Application\Application\Helper\ApplicationHelper;

class ApplicationHelperFactory implements FactoryInterface
{

    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        return new ApplicationHelper($serviceLocator->getServiceLocator());
    }
}
