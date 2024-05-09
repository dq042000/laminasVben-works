<?php

/**
 *
 * @author    sfs teams <zfsfs.team@gmail.com>
 * @copyright 2010-2016 (http://www.sfs.tw)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://www.sfs.tw
 * Date: 2016/9/12
 * Time: 下午 9:17
 */

namespace Base\Factory;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class TokenCheckFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var \Laminas\ApiTools\ContentNegotiation\Request $request */
        $request = $container->get('Request');
        if (!$request->getHeader('Authorization')) return ['status' => 'error']; // 避免錯誤
        try {
            // 檢查 authorization
            if ($authorization = $request->getHeader('Authorization')->getFieldValue()) {
                $authorizationArr = explode(' ', $authorization);
                if (count($authorizationArr) !== 2) return ['status' => 'error'];
                if ($authorizationArr[0] !== 'Bearer') return ['status' => 'error'];
                $token = $authorizationArr[1];
                $orm = $container->get('doctrine.entitymanager.orm_default');

                $oauthAccessTokens = $orm->getRepository(\Base\Entity\OauthAccessTokens::class);
                $oauthAccessTokensArr = $oauthAccessTokens->findBy(['access_token' => $token]);
                if (count($oauthAccessTokensArr) !== 1) return ['status' => 'error'];
                
                $oauthAccessTokensObj = $oauthAccessTokensArr[0];
                $oauthClients = $orm->getRepository(\Base\Entity\OauthClients::class);
                $oauthClientsArr = $oauthClients->findBy(['client_id' => $oauthAccessTokensObj->getClientId()]);
                if (count($oauthClientsArr) !== 1) return ['status' => 'error'];
                return ['status'=> 'success', 'data' => $token];
            }
            return ['status' => 'error'];
        } catch (\Exception $e) {
            echo $e->getMessage();
            return ['status' => 'error'];
        }
    }
}
