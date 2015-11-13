<?php
namespace Omolink\Content;

class Module
{      
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                  __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    'Omolink\Content' => __DIR__ . '/src/Omolink/Content',
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
