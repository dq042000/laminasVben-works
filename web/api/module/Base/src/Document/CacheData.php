<?php
namespace Base\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\Document */
class CacheData
{
    /** @ODM\Id */
    private $id;

    /** @ODM\Field(type="string") */
    private $cacheName;

    /** @ODM\Field(type="string") */
    private $cacheData;

    /** @ODM\Field(name="timestamp", type="date") */
    private $timestamp;

    public function __construct()
    {

    }

    public function getId()
    {
        return $this->id;
    }

    public function setCacheName($cacheName)
    {
        $this->cacheName = $cacheName;
    }

    public function getCacheName()
    {
        return $this->cacheName;
    }

    public function setCacheData($cacheData)
    {
        $this->cacheData = $cacheData;
    }

    public function getCacheData()
    {
        return $this->cacheData;
    }

    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
