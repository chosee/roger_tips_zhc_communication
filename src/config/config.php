<?php

namespace ZHC\Config;

class Config {
    private static $config = [];

    public static function init() {
        // Load environment variables
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        self::$config = [
            'app' => [
                'env' => getenv('APP_ENV') ?: 'development',
                'url' => getenv('APP_URL') ?: 'http://localhost',
                'secret' => getenv('APP_SECRET'),
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
                    'endpoint' => 'https://zhc.ch/api/news'
                ],
                'agenda' => [
                    'api_key' => getenv('AGENDA_API_KEY'),
                    'endpoint' => 'https://zhc.ch/api/agenda'
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
