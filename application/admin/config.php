<?php
return [
    'app_trace' => false,
    'menu' => [
        'top' => [
            ['args' => ['admin', 'company'], 'text' => '公司'],
            ['args' => ['admin'], 'text' => '平台管理']
        ]
    ],

    'dispatch_success_tmpl' => APP_PATH . 'admin' . DS . 'view/jump.html',
    'dispatch_error_tmpl' => APP_PATH . 'admin' . DS . 'view/jump.html',
];