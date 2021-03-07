<?php namespace Mmid\Mollie\Classes;

use Mmid\Mollie\Models\Settings;
use Mollie\Api\Exceptions\ApiException;

class MollieGateway{

	private $mollie;
	private $payment_mode;
	private $test_api_key;
	private $live_api_key;

	public function __construct(){
		$this->payment_mode = Settings::get('payment_mode', 0);
		$this->test_api_key = Settings::get('test_api_key', NULL);
		$this->live_api_key = Settings::get('live_api_key', NULL);
		$this->mollie = new \Mollie\Api\MollieApiClient();
		if($this->payment_mode == 1){
			$this->mollie->setApiKey($this->live_api_key);
		}else{
			$this->mollie->setApiKey($this->test_api_key);
		}
	}

	public function createPayment($amount, $description, $redirect_url, $webhook_url){
		$amount = number_format($amount, 2, '.', '');
		$data = [
			'amount' => [
		        'currency' => 'EUR',
		        'value' => (string) $amount,
		    ],
		    'description' => $description,
		    'redirectUrl' => $redirect_url,
		    'webhookUrl'  => $webhook_url,
		    'metadata' => [
	            'uid'=> uniqid(),
	            'name' => 'Dev mmid',
	        ],
		];
		try{
		    $payment = $this->mollie->payments->create($data);
		    return [
		    	'status' => TRUE,
		    	'data' => [
		    		'txn_id' => $payment->id,
		    		'checkout_url' => $payment->getCheckoutUrl(),
		    	],
		    ];
		}catch(ApiException $e){
		    return [
		    	'status' => FALSE,
		    	'error' => htmlspecialchars($e->getMessage()),
		    ];
		}
	}

    public function getStatus($txn_id=''){
    	try{
		    $payment = $this->mollie->payments->get($txn_id);
		    if($payment->isPaid() && !$payment->hasRefunds() && !$payment->hasChargebacks()){
		    	$status = 1;
		    	$msg = 'Order has been done successfully!';
		    }elseif($payment->isOpen()){
		    	$status = 2;
		    	$msg = 'Order is open for making payment!';
		    }elseif($payment->isPending()){
		    	$status = 3;
		    	$msg = 'Order is pending!';
		    }elseif($payment->isFailed()){
		    	$status = 4;
		    	$msg = 'Order has been failed!';
		    }elseif($payment->isExpired()){
		    	$status = 5;
		    	$msg = 'Order has been expired!';
		    }elseif($payment->isCanceled()){
		    	$status = 6;
		    	$msg = 'Order has been canceled!';
		    }elseif($payment->hasRefunds()){
		    	$status = 7;
		    	$msg = 'Order has been refunded!';
		    }elseif($payment->hasChargebacks()){
		    	$status = 8;
		    	$msg = 'Order has been charged back!';
		    }else{
		    	$status = 9;
		    	$msg = 'Unknown error!';
		    }
			return [
		    	'status' => TRUE,
		    	'data' => [
		    		'status' => $status,
		    		'msg' => $msg,
		    	],
		    ];
		}catch(ApiException $e){
			return [
		    	'status' => FALSE,
		    	'error' => htmlspecialchars($e->getMessage()),
		    ];
		}
    }

}