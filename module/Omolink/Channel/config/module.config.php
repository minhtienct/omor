<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Omolink\Channel\Controller\Channel' => 'Omolink\Channel\Controller\ChannelController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'channel' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/omolink/channel[/:action][/:id][/page/:page][/order_by/:order_by][/:order][/valid/:valid][/cnt/:cnt]',
                    'constraints' => array(
                        'action' => '(?!\bpage\b)(?!\border_by\b)(?!\bvalid\b)(?!\bcnt\b)[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                        'page' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'valid' => '[a-zA-Z]*',
                        'cnt' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Omolink\Channel\Controller\Channel',
                        'action' => 'listview',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'channel' => __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'head_titles' => array(
        'Omolink\Channel\ListView' => '登録済みチャネルの一覧',
        'Omolink\Channel\Insert' => 'チャネル登録',
        'Omolink\Channel\Update' => 'チャネル更新',
    ),
);
