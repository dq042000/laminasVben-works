<?php
/**
 *
 */

namespace Base\Factory;

use Base\Service\HyLogger;
use Interop\Container\ContainerInterface;
use Laminas\Log\Logger;
use Laminas\Log\Writer\MongoDB;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\Container;

class LogFactory implements FactoryInterface
{
    /**
     * 回傳 log 存取物件
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|Logger
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $masterSession = new Container('masterSession');
        $log = new HyLogger($masterSession->isHorngyangManager);

        /** @var \Doctrine\ODM\MongoDB\DocumentManager $odm */
        $odm = $container->get('doctrine.documentmanager.odm_default');
        $databaseName = $odm->getConfiguration()->getDefaultDB();
        $writeConcern = ['journal' => false, 'wtimeout' => 100, 'wstring' => 1];
        $manager = $odm->getClient()->getManager();
        $writer = new MongoDB($manager, $databaseName, 'SystemLog', $writeConcern);
        $log->addWriter($writer);
        return $log;
    }
}
