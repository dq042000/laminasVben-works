<?php

namespace Base\Service;

use Laminas\Http\Client;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Json\Json;
use Psr\Container\ContainerInterface;

class RecaptchaApi
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

    public function sendRequest($reCaptchaToken)
    {
        try {
            if (!isset($this->serviceLocator->get('config')['recaptcha-api'])) {
                $this->success = false;
                return $this;
            }

            // 回傳結果
            $config = $this->serviceLocator->get('config')['recaptcha-api'];
            $data = [
                "secret" => $config['GOOGLE_RECAPTCHA_SECRET_KEY'],
                'response' => $reCaptchaToken
            ];

            $this->httpClient->setUri($config['url'])
                ->setMethod(Request::METHOD_POST)
                ->setParameterPost($data);
            $this->httpClient->send();
            $result = $this->httpClient->getResponse();

            if ($result->getStatusCode() == 200) {
                $_result = json_decode($result->getBody(), true);
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
