<?php

namespace Base\Service;

use Laminas\Http\Client;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Json\Json;
use Psr\Container\ContainerInterface;

class OAuthApi
{
    protected $serviceLocator;

    /** @var Response */
    public $response;
    public $httpClient;

    protected $success = false;
    protected $message = '';
    protected $result = [];

    public function __construct(ContainerInterface $container)
    {
        $this->serviceLocator = $container;
        $this->httpClient = new Client();
    }

    public function getResult()
    {
        return $this->result;
    }

    public function getSuccess()
    {
        return $this->success;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function sendRequest($type, $post = [])
    {
        try {
            if (!isset($this->serviceLocator->get('config')['horngyang-api'])) {
                $this->success = false;
                return $this;
            }

            // call cs api 檢查 & 回傳結果
            $config = $this->serviceLocator->get('config')['horngyang-api'];
            $apiUrl = $config['url'];

            // 獲取新的訪問令牌
            if ($type === 'client_credentials') {
                $route = $config['route'];
                $data = [
                    "grant_type" => "client_credentials",
                    "client_id" => $post['client_id'],
                    "client_secret" => $post['client_secret'],
                ];

            // 撤銷訪問令牌
            } elseif ($type === 'refresh_token') {
                $route = 'oauth/revoke';
                $data = [
                    "token" => $post['token'],
                    "token_type_hint" => "access_token",
                ];
            } else {
                $this->success = false;
                $this->message = '錯誤';
                return $this;
            }

            $this->httpClient->setUri($apiUrl . $route)
                ->setMethod(Request::METHOD_POST)
                ->setParameterPost($data);
            $this->httpClient->send();
            $result = $this->httpClient->getResponse();

            if ($result->getStatusCode() == 200) {
                $_result = json_decode($result->getBody(), Json::TYPE_ARRAY);
                if ($_result) {
                    $this->success = true;
                    $this->result = $_result;
                } else {
                    $this->success = false;
                    $this->message = '錯誤';
                }

                return $this;
            } else {
                $this->success = false;
                $this->message = $result->getContent();
                return $this;
            }
        } catch (\Exception $e) {
            $this->success = false;
            $this->message = $e->getMessage();
            return $this;
        }
    }
}
