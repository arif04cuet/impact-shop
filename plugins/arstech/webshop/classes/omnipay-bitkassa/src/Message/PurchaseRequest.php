<?php

namespace Omnipay\BitKassa\Message;

/**
 * BitKassa Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('amount', 'currency');

        $data = array();

        $data['amount'] = $this->getAmount();
        $data['currency'] = $this->getCurrency();
        $data['action'] = 'start_payment';

        $data['meta_info'] = $this->getTransactionId();
        $data['description'] = $this->getDescription();
        $data['update_url'] = $this->getNotifyUrl();
        $data['return_url'] = $this->getReturnUrl();

        return $data;
    }
}
