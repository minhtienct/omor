<?php

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
return array(
    /**
     * Service manager config
     */
    'service_manager' => array(
        'invokables' => array(
            'AuthService' => 'Zend\Authentication\AuthenticationService',
        ),
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'LoginInfoService' => 'Application\Login\Factory\LoginInfoServiceFactory',
            'LoginService' => 'Application\Login\Factory\LoginServiceFactory',
            'HtmlToPdfService' => 'Application\Application\Factory\HtmlToPdfFactory',
            'SystemLogService' => 'Application\Application\Factory\SystemLogFactory'
        ),
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
        )
    ),
    /**
     * Layout config
     */
    'module_layouts' => array(
        'Application' => 'layout/layout_login.phtml',
        'Omolink' => 'layout/layout.phtml',
    ),
    /**
     * View helpers
     */
    'view_helpers' => array(
        'factories' => array(
            'ApplicationHelper' => 'Application\Application\Factory\ApplicationHelperFactory',
            'LoginInfoHelper' => 'Application\Login\Factory\LoginInfoHelperFactory',
        )
    ),
    'wkhtmltopdf' => array(
        'tool_path' => '/usr/local/bin/wkhtmltopdf',
        'cookie_key' => 'omolink',
        'output_path' => '/var/tmp/pdf',
        'cpu' => 'amd64', //; amd64  , i386 , ''-自動チェック
        'encoding' => 'UTF-8',
        'html_debug' => 0,
    ),
    'session_config' => array
        (
        'name' => 'omolink',
        'cache_expire' => 60 * 60 * 8,
        'cookie_lifetime' => 60 * 60 * 8,
        'gc_maxlifetime' => 60 * 60 * 8,
        'cookie_path' => '/',
        'cookie_secure' => false,
        'remember_me_seconds' => 60 * 60 * 8,
        'use_cookies' => true,
        'cookie_httponly' => true
    ),
    /**
     * Session handler config
     */
    'session_handler' => 'default', // default, memcache
);

