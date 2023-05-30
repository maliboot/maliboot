<?php

declare(strict_types=1);
return [
    // enable false 将不会生成 swagger 文件
    'enable' => env('APP_ENV') !== 'production',
    // swagger 配置的输出文件
    'output_file' => BASE_PATH . '/public/swagger/swagger.json',
    // swagger 的基础配置
    'swagger' => [
        'openapi' => '3.0.1',
        'info' => [
            'description' => 'swagger api desc',
            'version' => '1.0.0',
            'title' => 'API DOC',
        ],
        'host' => 'example.com',
        'schemes' => ['http', 'https'],
    ],
];
