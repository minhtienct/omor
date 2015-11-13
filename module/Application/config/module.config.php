<?php

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/[:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Login\Controller\Login',
                        'action' => 'loginView',
                    ),
                ),
            ),
            'password' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/password/[:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Login\Controller\Password',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'omolink',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Login\Controller\Login' => 'Application\Login\Controller\LoginController',
            'Application\Login\Controller\Password' => 'Application\Login\Controller\PasswordController'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/production/404' => __DIR__ . '/../view/error/production/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
            'error/production/index' => __DIR__ . '/../view/error/production/index.phtml',
            'paginator-slide' => __DIR__ . '/../view/layout/slidePaginator.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
    //--------View helper
    'view_helpers' => array(
        'invokables' => array(
            'formelementerrors' => 'Application\Application\Helper\FormElementErrors',
        ),
    ),
    //-------- Navigation
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'ホーム',
                'route' => 'home',
                'pages' => array(
                    array(
                        'label' => 'パスワード変更',
                        'route' => 'password',
                        'action' => 'changePassword',
                    ),
                    array(
                        'label' => '閲覧パスワード変更',
                        'route' => 'password',
                        'action' => 'changeBrowse',
                    ),
                ),
            ),
        ),
    ),
    //------- Head title
    'head_titles' => array(
        'Application\Login\LoginView' => 'ログイン',
        'Application\Password\ChangePassword' => 'パスワード変更',
        'Application\Password\PasswordSetting' => 'パスワード再設定',
        'Application\Password\PasswordReset' => 'パスワードリセット',
        'Application\Password\ChangeBrowse' => '閲覧パスワード変更',
    ),

    'time_manager' => array(
         'time_password_expired' => 30, //単位：分
    )
);
