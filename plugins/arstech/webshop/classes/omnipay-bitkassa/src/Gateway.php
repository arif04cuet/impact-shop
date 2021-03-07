<?php

namespace Omnipay\BitKassa;

use Omnipay\Common\AbstractGateway;

/**
 * BitKassa Gateway
 *
 * @link https://bitkassa.com/downloads/bitpayApi.pdf
 */
class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'BitKassa';
    }

    public function getDefaultParameters()
    {
        return array(
            'apiKey' => '',
            'testMode' => false,
        );
    }

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


    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\BitKassa\Message\PurchaseRequest', $parameters);
    }

    public function getPurchaseStatus(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\BitKassa\Message\PurchaseStatusRequest', $parameters);
    }
}
