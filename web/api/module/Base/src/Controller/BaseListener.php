<?php

/**
 * for Restful API
 */

namespace Base\Controller;

use Base\Factory\CacheMemcachedFactory;
use DOMDocument;
use DOMXPath;
use Laminas\ApiTools\Rest\AbstractResourceListener;
use Laminas\Json\Json;

class BaseListener extends AbstractResourceListener
{
    /** @var \Laminas\ServiceManager\ServiceManager $serviceManager */
    private $serviceManager;

    /**
     * 系統 config 設定
     * @var array
     */
    private $config;

    /** @var  StorageInterface */
    protected $_cache;

    /**
     * BaseController constructor.
     * @param $services
     */
    public function __construct($services)
    {
        $this->serviceManager = $services;
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
     * @return \Laminas\ServiceManager\ServiceManager
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
     * 截取內文中的圖片url
     */
    public function getImgUrl($content)
    {
        $dom = new DOMDocument();
        $dom->loadHTML($content);
        $xpath = new DOMXPath($dom);
        $image_url = $this->getServiceManager()->get('config')['file-url'] . "/show-file/";
        $images = $xpath->query("//img[contains(@src, '{$image_url}')]/@src");
        if (strpos($image_url, 'laminasvben-works.test') !== false) {
            // 本機開發環境
            $pattern = '/http:\/\/laminasvben-works\.test:9714\/files\/show-file\/([a-zA-Z0-9]+)/';
        } elseif (strpos($image_url, 'demo.laminasvben-works.com.tw') !== false) {
            // 測試環境
            $pattern = '/https:\/\/demo\.laminasvben-works\.com\.tw\/files\/show-file\/([a-zA-Z0-9]+)/';
        } else {
            // 正式環境
            $pattern = '/https:\/\/\laminasvben-works\.com\.tw\/files\/show-file\/([a-zA-Z0-9]+)/';
        }
        $imgSrcArray = [];
        foreach ($images as $image) {
            if (preg_match($pattern, $image->value, $matches)) {
                $imgSrcArray[] = $matches[1];
            }
        }
        return $imgSrcArray;
    }
}
