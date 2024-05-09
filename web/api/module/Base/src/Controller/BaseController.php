<?php

/**
 * for Rpc API
 */

namespace Base\Controller;

use Base\Factory\CacheMemcachedFactory;
use Laminas\Json\Json;
use Laminas\Mvc\Controller\AbstractActionController;

class BaseController extends AbstractActionController
{
    /** @var \Interop\Container\ContainerInterface */
    private $serviceManager;

    /**
     * 系統 config 設定
     * @var array
     */
    private $config;

    /** @var  StorageInterface */
    protected $_cache;
    protected $_acpuCache;
    protected $_redisCache;

    /**
     * @var DocumentManager
     */
    protected $_mongodbCache;

    /**
     * BaseController constructor.
     * @param $controller
     */
    public function __construct($controller)
    {
        $this->serviceManager = $controller;
        $this->config = $this->serviceManager->get('config');
    }

    /**
     * 依據連線 access_token 取得學校 Doctrine EntityManager
     * @return  \Doctrine\ORM\EntityManager
     * @throws \Exception
     */
    public function getEntityManager()
    {
        return $this->serviceManager->get('doctrine.entitymanager.orm_default');
    }

    /**
     * Undocumented function
     *
     * @return \Interop\Container\ContainerInterface
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * @return \Doctrine\ODM\MongoDB\DocumentManager
     */
    public function getDocumentManager()
    {
        return $this->serviceManager->get('doctrine.documentmanager.odm_default');
    }

    /**
     * 取得 config 設定
     * @return array
     */
    protected function getConfig()
    {
        return $this->config;
    }

    

    /**
     * @return StorageInterface
     */
    public function getCache()
    {
        if (!$this->_cache) {
            $this->_cache = $this->serviceManager->get(CacheMemcachedFactory::class);
        }

        return $this->_cache;
    }

    /**
     * @return StorageInterface
     */
    public function getAcpuCache()
    {
        if (!$this->_acpuCache) {
            $this->_acpuCache = $this->serviceManager->get(CacheApcuFactory::class);
        }

        return $this->_acpuCache;
    }

    /**
     * @return StorageInterface
     */
    public function getRedisCache()
    {
        if (!$this->_redisCache) {
            $this->_redisCache = $this->serviceManager->get(CacheRedisFactory::class);
        }

        return $this->_redisCache;
    }

    /**
     * @return DocumentManager
     */
    public function getMongodbCache()
    {
        if (!$this->_mongodbCache) {
            $this->_mongodbCache = $this->serviceManager->get(CacheMongodb::class);
        }

        return $this->_mongodbCache;
    }

    /**
     * 系統記錄檔
     * @param $kind
     * @param $message
     * @param string $role
     */
    public function systemLogger($kind, $message, $data = [])
    {
        // logger紀錄
        $httpIp = $_SERVER['REMOTE_ADDR'] ?: ($_SERVER['HTTP_X_FORWARDED_FOR'] ?: $_SERVER['HTTP_CLIENT_IP']);
        $logger = $this->getServiceManager()->get('Logger');
        $logger->$kind(
            $message,
            [
                'ip' => $httpIp,
                'data' => $data,
            ]
        );
    }

    /**
     * 取得暫存資料
     *
     * @param string $module 模組名稱
     *  - redis
     *  - mongodb
     * @param string $cacheName 快取名稱
     * @param string $action 執行動作
     *  - get: 取得特定的快取項目
     *  - save: 儲存特定的快取項目
     * @param bool $isRemove 是否清除快取 ($action 需為 get 的情況下才有效)
     *  - false: 不清除
     *  - remove: 清除特定的快取項目
     *  - flush: 清除全部暫存
     * @param mixed $data 儲存的資料
     * @return mixed
     */
    public function getCacheData($module, $cacheName, $action = "get", $isRemove = false, $data = null)
    {
        switch ($module) {
            case "mongodb":
                /** @var \Base\Service\CacheMongodb $cache */
                $cache = $this->getMongodbCache();

                // 快取名稱有 * 字元，則取得所有符合的快取項目
                if (strpos($cacheName, '*') !== false) {
                    $allKeys = $cache->getKeys($cacheName);
                }
                break;
            default:
                /** @var \Base\Service\Factory\CacheRedisFactory $cache */
                $cache = $this->getRedisCache();

                // 快取名稱有 * 字元，則取得所有符合的快取項目
                if (strpos($cacheName, '*') !== false) {
                    $redis = new \Redis();
                    $redis->connect('redis', 6379);
                    $redis->auth($_ENV['REDIS_PASSWORD']);
                    $keys = $redis->keys("*{$cacheName}");
                    $allKeys = array_map(function ($key) {
                        return str_replace('laminascache:', '', $key);
                    }, $keys);
                }
                break;
        }

        // 執行動作
        switch ($action) {
            case "save":
                // 使用 serialize 儲存資料
                $cache->setItem($cacheName, serialize(['resultArr' => $data]));
                return true;
                break;
            case "get": // 取得特定的快取項目
                // 是否清除快取
                switch ($isRemove) {
                    case "remove": // 清除特定的快取項目
                        if (!empty($allKeys)) {
                            foreach ($allKeys as $key) {
                                $cache->removeItem($key);
                            }
                            return true;
                        }

                        if (strpos($cacheName, '*') === false) {
                            $cache->removeItem($cacheName);
                            return true;
                        }
                        break;
                        // case "flush":   // 清除全部暫存
                        //     $cache->flush();
                        //     break;
                }

                // 判斷快取是否存在
                if ($cache->hasItem($cacheName)) {
                    // 使用 unserialize 取得資料
                    $cacheData = unserialize($cache->getItem($cacheName));
                    return $cacheData['resultArr'];
                }
                break;
        }

        return false;
    }
}
