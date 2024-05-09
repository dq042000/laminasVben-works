<?php

/**
 *
 */

namespace Base\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\Container;

class SessionFactory implements FactoryInterface
{
    const SESSION_NAME = 'horngyang_session';
    private $_session;

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (!$this->_session) {
            $this->_session = new Container(self::SESSION_NAME);
        }

        return $this->_session;
    }
}
