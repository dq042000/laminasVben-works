<?php

/**
 *
 */

namespace Base\Factory;

use Laminas\Cache\Service\StorageAdapterFactoryInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class CacheMemcachedFactory implements FactoryInterface
{

    const SITE_CACHE_NAMESPACE = 'site_cache';
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $cacheServer = $container->get('config')['memcached-server'];
        $cacheServer['options']['namespace'] = self::SITE_CACHE_NAMESPACE;
        /** @var StorageAdapterFactoryInterface $storageFactory */
        $storageFactory = $container->get(StorageAdapterFactoryInterface::class);
        return  $storageFactory->create(
            'memcached',
            ['ttl' => 3600],
            [
                [
                    'name' => 'exception_handler',
                    'options' => [
                        'throw_exceptions' => false
                    ],
                ],
            ]
        );
    }

}
