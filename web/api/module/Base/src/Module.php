<?php

declare (strict_types = 1);

namespace Base;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\SessionManager;

class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        $app = $e->getApplication();
        $sm = $app->getServiceManager();

        // 在執行 phpunit 時需要隱藏
        if (php_sapi_name() !== 'cli') {
            $config = $sm->get('Configuration');
            if (isset($config['phpSettings'])) {
                $phpSettings = $config['phpSettings'];
                foreach ($phpSettings as $key => $value) {
                    ini_set($key, $value);
                }
            }
        }

        $sessionManager = $app->getServiceManager()->get(SessionManager::class);
        $this->forgetInvalidSession($sessionManager);

        // Add Authorization
        $eventManager = $app->getEventManager();
        // $eventManager->attach(\Laminas\Mvc\MvcEvent::EVENT_ROUTE, [
        //     $this,
        //     'checkUserIsLogin',
        // ], 100);
    }

    protected function forgetInvalidSession($sessionManager)
    {
        try {
            $sessionManager->start();
            return;
        } catch (\Exception $e) {}
        session_unset();
    }

    /**
     * @param $e MvcEvent
     */
    public function checkUserIsLogin($e)
    {
        // if (!($e->getRequest() instanceof \Laminas\Console\Request)) {
        //     $application = $e->getApplication();
        //     $sm = $application->getServiceManager();
        //     $sm->get('ControllerPluginManager')
        //         ->get('AclPlugin')
        //         ->doAuthorization($e);
        // }
    }
}
