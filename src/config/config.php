<?php

namespace ZHC\Config;

class Config {
    private static $config = [];

    public static function init() {
        // Load environment variables
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $env = getenv('APP_ENV') ?: 'development';
        
        // Base URLs for different environments
        $baseUrls = [
            'development' => 'http://localhost:8000',
            'test' => 'http://localhost:8000',
            'production' => 'https://roger.tips/zhc'
        ];

        self::$config = [
            'app' => [
                'env' => $env,
                'url' => getenv('APP_URL') ?: $baseUrls[$env],
                'secret' => getenv('APP_SECRET'),
            ],
            'design' => [
                'colors' => [
                    'primary' => '#FFDB01',    // ZHC Yellow
                    'secondary' => '#000000',   // ZHC Black
                    'grey' => [
                        '100' => '#F5F5F5',
                        '200' => '#EEEEEE',
                        '300' => '#E0E0E0',
                        '400' => '#BDBDBD',
                        '500' => '#9E9E9E',
                        '600' => '#757575',
                        '700' => '#616161',
                        '800' => '#424242',
                        '900' => '#212121',
                    ]
                ]
            ],
            'upload' => [
                'max_files' => 5,
                'max_size' => 5 * 1024 * 1024, // 5MB
                'allowed_types' => ['image/jpeg', 'image/png', 'image/gif'],
                'path' => __DIR__ . '/../public/uploads/images',
            ],
            'external_services' => [
                'website' => [
                    'api_key' => getenv('WEBSITE_API_KEY'),
                    'endpoint' => $baseUrls[$env] . '/api/news'
                ],
                'agenda' => [
                    'api_key' => getenv('AGENDA_API_KEY'),
                    'endpoint' => $baseUrls[$env] . '/api/agenda'
                ],
                'mobile_app' => [
                    'api_key' => getenv('MOBILE_APP_API_KEY'),
                    'endpoint' => 'https://api.zhc-app.ch/news'
                ]
            ]
        ];
    }

    public static function get($key, $default = null) {
        $keys = explode('.', $key);
        $config = self::$config;

        foreach ($keys as $key) {
            if (!isset($config[$key])) {
                return $default;
            }
            $config = $config[$key];
        }

        return $config;
    }
}
