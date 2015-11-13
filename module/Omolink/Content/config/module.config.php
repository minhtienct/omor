<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Omolink\Content\Controller\Content' => 'Omolink\Content\Controller\ContentController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'content' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/omolink/content[/:action][/:id][/page/:page][/order_by/:order_by][/:order][/valid/:valid][/cnt/:cnt]',
                    'constraints' => array(
                        'action' => '(?!\bpage\b)(?!\border_by\b)(?!\bvalid\b)(?!\bcnt\b)[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                        'page' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'valid' => '[a-zA-Z]*',
                        'cnt' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Omolink\Content\Controller\Content',
                        'action' => 'listview',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'content' => __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),

    ),
    'head_titles' => array(
        'Omolink\Content\ListView' => '自分で登録したコンテンツ一覧',
        'Omolink\Content\Insert' => 'コンテンツ登録',
        'Omolink\Content\Update' => 'コンテンツ更新',
    ),
);
