<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Base;

use Base\Factory\BaseFactory;

return [
    'doctrine' => [
        'driver' => [
            'base_entities' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity'],
            ],
            'orm_default' => [
                'drivers' => [
                    'Base\Entity' => 'base_entities',
                ],
            ],
        ],
    ],

    'controllers' => [
        'factories' => [],
        'aliases' => [],
    ],

    'controller_plugins' => [
        'invokables' => [],
        'factories' => [],
        'aliases' => [],
    ],

    'service_manager' => [
        'factories' => [
            Factory\LogFactory::class => Factory\LogFactory::class,
            Factory\JwtTokenCheckFactory::class => Factory\JwtTokenCheckFactory::class,
            Factory\TokenCheckFactory::class => Factory\TokenCheckFactory::class,
            Factory\SessionFactory::class => Factory\SessionFactory::class,
            Factory\CacheMemcachedFactory::class => Factory\CacheMemcachedFactory::class,
            Factory\CacheApcuFactory::class => Factory\CacheApcuFactory::class,
            Factory\CacheRedisFactory::class => Factory\CacheRedisFactory::class,
            Command\Install::class => BaseFactory::class,
            Command\SetAdmin::class => BaseFactory::class,
            Service\JsonSchema::class => BaseFactory::class,
            Service\OAuthApi::class => BaseFactory::class,
            Service\RecaptchaApi::class => BaseFactory::class,
        ],
        'aliases' => [
            'Logger' => Factory\LogFactory::class,
            'Session' => Factory\SessionFactory::class,
            'OAuthApi' => Service\OAuthApi::class,
            'RecaptchaApi' => Service\RecaptchaApi::class,
        ],
    ],

    'laminas-cli' => [
        'commands' => [
            'base:install [re-create-database]' => Command\Install::class,
            'base:set-admin <username> <password>' => Command\SetAdmin::class,
        ],
    ],
];
