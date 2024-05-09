<?php
namespace Base\Model;

use Laminas\ServiceManager\ServiceManager;

class Db
{
    /** @var ServiceManager */
    protected $_sm;
    public function __construct($sm)
    {
        $this->_sm = $sm;
    }

    /**
     * 舊資料庫
     * @return \Doctrine\DBAL\Connection
     */
    public function oldDb()
    {
        $config = $this->_sm->get('config');
        $params = $config['upgrade']['olddb'];
        return \Doctrine\DBAL\DriverManager::getConnection($params);
    }

    /**
     * 新資料庫
     * @return \Doctrine\DBAL\Connection
     */
    public function newDb()
    {
        $config = $this->_sm->get('config');
        $params = $config['upgrade']['newdb'];
        return \Doctrine\DBAL\DriverManager::getConnection($params);
    }
}
