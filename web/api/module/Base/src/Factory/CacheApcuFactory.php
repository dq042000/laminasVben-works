<?php

/**
 *
 */

namespace Base\Factory;

use Laminas\Cache\Service\StorageAdapterFactoryInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class CacheApcuFactory implements FactoryInterface
{
    /**
     * 取得以 apcu 為介面的快取物件
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return \Laminas\Cache\Storage\PluginAwareInterface|\Laminas\Cache\Storage\StorageInterface|\Laminas\EventManager\EventsCapableInterface|object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var StorageAdapterFactoryInterface $storageFactory */
        $storageFactory = $container->get(StorageAdapterFactoryInterface::class);
        return  $storageFactory->create(
            'apcu',
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
