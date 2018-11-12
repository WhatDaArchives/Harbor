<?php


namespace App\Builders;


use Spyc;

class DockerComposeBuilder
{
    /**
     * @var array
     */
    private $contents = [
        'version' => '3',
        'services' => [
            'nginx' => [
                'image' => 'whatdafox/nginx-php:latest',
                'volumes' => [
                    '.:/var/www/html',
                ],
                'ports' => [
                    '${APP_PORT:-80}:80',
                ],
                'networks' => [
                    'app-net',
                ],
            ],
            'app' => [
                'image' => 'whatdafox/php-fpm:latest',
                'volumes' => [
                    '.:/var/www/html',
                ],
                'env_file' => [
                    '.env',
                ],
                'networks' => [
                    'app-net',
                ],
            ],
        ],
        'networks' => [
            'app-net' => [
                'driver' => 'bridge',
            ],
        ],
        'volumes' => [
            'mysqldata' => [
                'driver' => 'local',
            ],
            'redisdata' => [
                'driver' => 'local',
            ],
        ],
    ];

    /**
     * @return $this
     */
    public function includeMySQL()
    {
        $this->contents['services']['mysql'] = [
            'image' => 'mysql:5.7',
            'volumes' => [
                'mysqldata:/var/lib/mysql',
            ],
            'ports' => [
                '${DB_PORT:-3306}:3306',
            ],
            'networks' => [
                'app-net',
            ],
        ];

        return $this;
    }

    /**
     * @return $this
     */
    public function includeRedis()
    {
        $this->contents['services']['redis'] = [
            'image' => 'redis:latest',
            'volumes' => [
                'redisdata:/data',
            ],
            'ports' => [
                '${REDIS_PORT:-6379}:6379',
            ],
            'networks' => [
                'app-net',
            ],
        ];

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return Spyc::YAMLDump($this->contents, false, false, true);
    }
}
