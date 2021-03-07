<?php

namespace Arstech\Webshop\Classes;

use OFFLINE\Mall\Classes\Payments\PaymentProvider;
use OFFLINE\Mall\Classes\Payments\PaymentResult;
use OFFLINE\Mall\Models\PaymentGatewaySettings;
use Omnipay\Omnipay;
use Request;
use Session;
use Throwable;
use Validator;

/**
 * Process the payment via Mollie payment gateway .
 */
class MollieProvider extends PaymentProvider
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'Mollie Payment';
    }

    /**
     * {@inheritdoc}
     */
    public function identifier(): string
    {
        return 'mollie';
    }

    /**
     * {@inheritdoc}
     */
    public function validate(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function process(PaymentResult $result): PaymentResult
    {

        $gateway = $this->getGateway();

        $response = null;
        $options = [
            'amount'    => $this->order->total_in_currency,
            'currency'  => $this->order->currency['code'],
            'description' => "Payment for order#" . $this->order->id,
            'returnUrl' => $this->returnUrl()
        ];

        try {
            $response = $gateway->purchase($options)->send();
        } catch (Throwable $e) {
            return $result->fail([], $e);
        }


        // Mollie has to return a RedirectResponse if everything went well
        if (!$response->isRedirect()) {
            return $result->fail((array)$response->getData(), $response);
        }

        Session::put('mall.payment.callback', self::class);
        Session::put('mall.mollie.transactionReference', $response->getTransactionReference());

        return $result->redirect($response->getRedirectResponse()->getTargetUrl());
    }

    /**
     * PayPal has processed the payment and redirected the user back.
     *
     * @param PaymentResult $result
     *
     * @return PaymentResult
     */
    public function complete(PaymentResult $result): PaymentResult
    {

        $key = Session::pull('mall.mollie.transactionReference');
        if (!$key) {
            return $result->fail([
                'msg'   => 'Missing payment data',
                'key'   => $key
            ], null);
        }

        $this->setOrder($result->order);

        try {
            $response = $this->getGateway()->completePurchase([
                'transactionReference' => $key
            ])->send();
        } catch (Throwable $e) {
            return $result->fail([], $e);
        }

        $data = (array)$response->getData();

        if (!$response->isSuccessful()) {
            return $result->fail($data, $response);
        }

        return $result->success($data, $response);
    }

    /**
     * Build the Omnipay Gateway for PayPal.
     *
     * @return \Omnipay\Common\GatewayInterface
     */
    protected function getGateway()
    {
        $gateway = Omnipay::create('Mollie')->setApiKey($this->getApiKey());

        return $gateway;
    }

    protected function getApiKey()
    {
        return decrypt(PaymentGatewaySettings::get('api_key'));;
    }


    /**
     * {@inheritdoc}
     */
    public function settings(): array
    {
        return [
            'api_key'     => [
                'label'   => 'API-Key',
                'comment' => 'The API Key for the Mollie payment service',
                'span'    => 'left',
                'type'    => 'text',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function encryptedSettings(): array
    {
        return ['api_key'];
    }
}
