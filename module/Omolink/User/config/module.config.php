<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Omolink\User\Controller\User' => 'Omolink\User\Controller\UserController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'user' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/omolink/user[/:action][/:id][/page/:page][/order_by/:order_by][/:order][/valid/:valid][/cnt/:cnt]',
                    'constraints' => array(
                        'action' => '(?!\bpage\b)(?!\border_by\b)(?!\bvalid\b)(?!\bcnt\b)[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                        'page' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'valid' => '[a-zA-Z]*',
                        'cnt' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Omolink\User\Controller\User',
                        'action' => 'listview',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'user' => __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),    
    'head_titles' => array(
        'Omolink\User\ListView' => 'チャネルオーナーの一覧',
        'Omolink\User\Insert' => 'オーナー登録',
        'Omolink\User\Update' => 'ユーザー更新',
    ),
);
