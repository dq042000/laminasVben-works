<?php

/**
 * List of enabled modules for this application.
 */

declare(strict_types=1);

return [
    'Laminas\Session',
    'Laminas\Log',
    'Laminas\Cache',
    'Laminas\Form',
    'Laminas\Mvc\I18n',
    'Laminas\I18n',
    'Laminas\ComposerAutoloading',
    'Laminas\Db',
    'Laminas\Filter',
    'Laminas\Hydrator',
    'Laminas\InputFilter',
    'Laminas\Paginator',
    'Laminas\Router',
    'Laminas\Validator',
    'Laminas\ApiTools',
    'Laminas\ApiTools\Documentation',
    'Laminas\ApiTools\ApiProblem',
    'Laminas\ApiTools\Configuration',
    'Laminas\ApiTools\OAuth2',
    'Laminas\ApiTools\MvcAuth',
    'Laminas\ApiTools\Hal',
    'Laminas\ApiTools\ContentNegotiation',
    'Laminas\ApiTools\ContentValidation',
    'Laminas\ApiTools\Rest',
    'Laminas\ApiTools\Rpc',
    'Laminas\ApiTools\Versioning',
    'Laminas\ZendFrameworkBridge',
    'DoctrineModule',
    'Laminas\Cache\Storage\Adapter\Apcu',
    'Laminas\Cache\Storage\Adapter\Memcached',
    'Laminas\Cache\Storage\Adapter\Redis',
    'DoctrineMongoODMModule',
    'DoctrineORMModule',
    'SlmQueue',
    'SlmQueueDoctrine\Module',
    'Base',
    'Application',
];
