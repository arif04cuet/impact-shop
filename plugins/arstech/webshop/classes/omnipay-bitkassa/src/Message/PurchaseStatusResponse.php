<?php

namespace Omnipay\BitKassa\Message;

/**
 * BitKassa Purchase Status Response
 */
class PurchaseStatusResponse extends PurchaseResponse
{
    public function isSuccessful()
    {
        return !$this->getMessage();
    }

    public function isRedirect()
    {
        return false;
    }
}
