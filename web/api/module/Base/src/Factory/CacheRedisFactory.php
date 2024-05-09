<?php

/**
 *
 */

namespace Base\Factory;

use Laminas\Cache\Service\StorageAdapterFactoryInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class CacheRedisFactory implements FactoryInterface
{
    /**
     * 取得以 redis 為介面的快取物件
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return \Laminas\Cache\Storage\PluginAwareInterface|\Laminas\Cache\Storage\StorageInterface|\Laminas\EventManager\EventsCapableInterface|object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var StorageAdapterFactoryInterface $storageFactory */
        $storageFactory = $container->get(StorageAdapterFactoryInterface::class);
        return $storageFactory->create(
            'redis',
            [
                'ttl' => 86400, // 24 小時
                'server' => [
                    'host' => 'redis',
                    'port' => $_ENV['REDIS_PORT'],
                    'timeout' => 5,
                ],
                'password' => $_ENV['REDIS_PASSWORD'],
            ],
            [
                [
                    'name' => 'exception_handler',
                    'options' => [
                        'throw_exceptions' => false,
                    ],
                ],
            ]
        );
        /**
         * 測試 redis 是否正常運作
         * 
         * 寫入資料並讀取
         */
        // $cacheAdapter = $storageFactory->create(
        //     'redis',
        //     [
        //         'ttl' => 3600,
        //         'server' => [
        //             'host' => 'redis',
        //             'port' => $_ENV['REDIS_PORT'],
        //             'timeout' => 5,
        //         ],
        //         'password' => $_ENV['REDIS_PASSWORD'],
        //     ],
        //     [
        //         [
        //             'name' => 'exception_handler',
        //             'options' => [
        //                 'throw_exceptions' => false,
        //             ],
        //         ],
        //     ],
        // );
        // $result = $cacheAdapter->setItem('my_key', serialize(['my' => 'value']));
        // if ($result) {
        //     echo "Cache item has been set.<Br>";
        // } else {
        //     echo "Failed to set cache item.<Br>";
        // }
        // $value = $cacheAdapter->getItem('my_key', $success);
        // if ($success) {
        //     $value = unserialize($value);
        //     echo "Cache item: ";
        //     echo "<pre>";
        //     print_r($value);
        //     echo "</pre>";
        //     echo "<Br>";
        // } else {
        //     echo "Failed to get cache item.<Br>";
        // }
        // exit;

        /**
         * 測試 redis 是否正常運作
         * 
         * 使用原生讀取
         */
        // $redis = new \Redis();
        // $redis->connect('redis', 6379);
        // $redis->auth($_ENV['REDIS_PASSWORD']);
        // $sessionId = session_id();  // 獲取當前的 session ID
        // $sessionData = $redis->get('PHPREDIS_SESSION:' . $sessionId);
        // echo $sessionId . "<br>";
        // echo "<pre>";
        // print_r($sessionData);
        // echo "</pre>";
        // $keys = $redis->keys('*');
        // $data = [];
        // foreach ($keys as $key) {
        //     $data[$key] = $redis->get($key);
        // }
        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";
        // exit;
    }
}
