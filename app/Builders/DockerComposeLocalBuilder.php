<?php


namespace App\Builders;


use Spyc;

class DockerComposeLocalBuilder
{
    /**
     * @var array
     */
    private $contents = [
        'version' => 2,
        'services' => [
            'app' => [
                'image' => 'whatdafox/php-fpm:latest',
                'volumes' => [
                    '.:/var/www/html',
                ],
                'ports' => [
                    '${APP_PORT}:80'
                ],
                'networks' => [
                    'app-net',
                ],
            ],
        ],
        'networks' => [
            'app-net' => [
                'driver' => 'local',
            ],
        ],
        'volumes' => [
            'mysqldata' => [
                'driver' => 'local',
            ],
            'redisdata' => [
                'driver' => 'local',
            ],
        ]
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
                '${DB_PORT}:3306'
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
