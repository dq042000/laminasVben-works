<?php
namespace Base\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations\EmbedOne;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Field;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Id;

/** @Document */
class SystemLog
{
    /** @Id */
    private $id;

    /** @Field(type="integer")*/
    private $priority;

    /** @Field(type="string") */
    private $priorityName;

    /** @Field(name="timestamp", type="date") */
    private $timestamp;

    /** @Field(type="string") */
    private $message;

    /** @EmbedOne (targetDocument=SystemLogExtra::class)  */
    private $extra;

    public function __construct()
    {
        $this->extra = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param mixed $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return mixed
     */
    public function getPriorityName()
    {
        return $this->priorityName;
    }

    /**
     * @param mixed $priorityName
     */
    public function setPriorityName($priorityName)
    {
        $this->priorityName = $priorityName;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param mixed $extra
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;
    }

}
