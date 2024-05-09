<?php

namespace Base\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbeddedDocument;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Field;

/** @EmbeddedDocument */
class SystemLogExtra
{
    /** @Field(type="string")*/
    private $kind;

    /** @Field(type="string")*/
    private $ip;

    /** @Field(type="string")*/
    private $state;

    /** @Field(type="int")*/
    private $isHorngyangManager;

    /** @Field(type="string")*/
    private $role;

    /**
     * @return mixed
     */
    public function getKind()
    {
        return $this->kind;
    }

    /**
     * @param mixed $kind
     */
    public function setKind($kind)
    {
        $this->kind = $kind;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param mixed $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getIsHorngyangManager()
    {
        return $this->isHorngyangManager;
    }

    /**
     * @param mixed $isHorngyangManager
     */
    public function setIsHorngyangManager($isHorngyangManager)
    {
        $this->isHorngyangManager = $isHorngyangManager;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

}