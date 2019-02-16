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
            'app' => [
                'image' => 'webdevops/php-nginx:7.3',
                'volumes' => [
                    '.:/var/www/html:delegated',
                ],
                'ports' => [
                    '${APP_PORT}:80',
                    '${APP_SSL_PORT}:443',
                ],
                'networks' => [
                    'app-net',
                ],
                'environment' => [
                    'WEB_DOCUMENT_ROOT' => '/var/www/html/public',
                ],
                'restart' => 'on-failure'
            ],
            'test' => [
                'image' => 'webdevops/php:7.3',
                'volumes' => [
                    '.:/var/www/html'
                ],
                'networks' => [
                    'app-net',
                ]
            ]
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
            'restart' => 'on-failure',
            'environment' => [
                'MYSQL_ROOT_PASSWORD' => "\${DB_ROOT_PASS}",
                'MYSQL_DATABASE' => "\${DB_DATABASE}",
                'MYSQL_USER' => "\${DB_USERNAME}",
                'MYSQL_PASSWORD' => "\${DB_PASSWORD}",
            ]
        ];

        $this->addDependency('mysql');

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
            'restart' => 'on-failure',
            'ports' => [
                '${REDIS_PORT:-6379}:6379',
            ],
            'environment' => [
                'REDIS_PORT' => '${REDIS_PORT}:6379',
            ],
            'networks' => [
                'app-net',
            ],
        ];

        $this->addDependency('redis');

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return Spyc::YAMLDump($this->contents, false, false, true);
    }

    /**
     *
     */
    private function addDependency($dependency): void
    {
        if (!array_key_exists('depends_on', $this->contents['services']['app'])) {
            $this->contents['services']['app']['depends_on'] = [];
        }

        if (!array_key_exists('depends_on', $this->contents['services']['test'])) {
            $this->contents['services']['test']['depends_on'] = [];
        }

        array_push($this->contents['services']['app']['depends_on'], $dependency);
        array_push($this->contents['services']['test']['depends_on'], $dependency);
    }
}
