<?php

namespace Application\Application\Factory;

use Zend\ServiceManager\FactoryInterface;
use Application\Application\Service\HtmlToPdf;
use Zend\ServiceManager\ServiceLocatorInterface;

class HtmlToPdfFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new HtmlToPdf($serviceLocator);
    }
}
