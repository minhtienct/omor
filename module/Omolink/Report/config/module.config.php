<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Omolink\Report\Controller\Report' => 'Omolink\Report\Controller\ReportController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'report' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/omolink/report[/:action][/:id][/page/:page][/order_by/:order_by][/:order][/valid/:valid][/cnt/:cnt]',
                    'constraints' => array(
                        'action' => '(?!\bpage\b)(?!\border_by\b)(?!\bvalid\b)(?!\bcnt\b)[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                        'page' => '[0-9]+',
                        'order_by' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'valid' => '[a-zA-Z]*',
                        'cnt' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Omolink\Report\Controller\Report',
                        'action' => 'listview',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'report' => __DIR__ . '/../view',
        ),
    ),
    'head_titles' => array(
        'Omolink\Report\ListView' => 'コンテンツ毎レポート',
    ),
);
