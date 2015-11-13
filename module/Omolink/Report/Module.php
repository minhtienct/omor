<?php
namespace Omolink\Report;

class Module
{      
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                  __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    'Omolink\Report' => __DIR__ . '/src/Omolink/Report',
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getServiceConfig() {

    }

}
