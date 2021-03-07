<?php

namespace Omnipay\BitKassa\Message;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    protected $liveEndpoint = 'https://www.bitkassa.nl/api/v1';
    protected $testEndpoint = 'https://www.bitkassa.nl/api/v1';

    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }

    public function setApiKey($value)
    {
        return $this->setParameter('apiKey', $value);
    }

    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }


    public function getMetaInfo()
    {
        return $this->getParameter('meta_info');
    }

    public function setMetaInfo($value)
    {
        return $this->setParameter('meta_info', $value);
    }


    protected function getHttpMethod()
    {
        return 'POST';
    }

    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    public function sendData($data)
    {

        $merchantId = $this->getMerchantId();;
        $secretApiKey = $this->getApiKey();

        // insert action and merchant_id (required for every API call) into the params array
        //$params['action'] = $action;
        $data['merchant_id'] = $merchantId;

        // create json and authentication
        $jsonData = json_encode($data);
        $t = time();
        $a = hash('sha256', $secretApiKey . $jsonData . $t) . $t; // authentication hash
        $p = base64_encode($jsonData);


        $httpRequest = $this->httpClient->createRequest(
            $this->getHttpMethod(),
            $this->getEndpoint() . "?p=$p&a=$a",
            array('Authorization' => 'Basic ' . base64_encode($this->getApiKey() . ':')),
            $data
        );

        $httpResponse = $httpRequest->send();

        return $this->response = $this->createResponse($httpResponse->json(), $httpResponse->getStatusCode());
    }

    protected function createResponse($data, $statusCode)
    {
        return $this->response = new PurchaseResponse($this, $data, $statusCode);
    }
}
