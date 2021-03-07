<?php namespace Mmid\Expertbox\Classes;

use Mail;
use Flash;
use Redirect;
use Mmid\Expertbox\Models\Request;
use Mmid\Expertbox\Models\Settings;
use Mmid\Mollie\Classes\MollieGateway;

class Mollie{

	private $redirect_url;

	public function __construct(){
		$this->redirect_url = url('/');
	}

	public function returnHandler($request_id){
		$request = Request::where('id', '=', $request_id)->first();
		if(!isset($request->id)){
			Flash::error('Sorry, invalid request.');
		}else{
			$res = $this->paymentHandler($request);
			if($res['status'] === TRUE){
				if($res['data']['status'] == 1){
					Flash::success($res['data']['msg']);
					Flash::success('Bedankt voor het indienen van je vraag, wij nemen zo spoedig mogelijk contact met je op!');
				}else{
					Flash::error($res['data']['msg']);
				}
			}else{
				Flash::error($res['error']);
			}
		}
	    return Redirect::to($this->redirect_url);
	}

	public function webhookHandler(){
		$txn_id = post('id', NULL);
		$request = Request::where('txn_id', '=', $txn_id)->first();
		if(isset($request->id)){
			$res = $this->paymentHandler($request);
		}
		return TRUE;
	}

	private function paymentHandler($request){
		$mollie = new MollieGateway();
		$res = $mollie->getStatus($request->txn_id);
		if($res['status'] === TRUE){
			$request->txn_status = $res['data']['status'];
			if($res['data']['status'] == 1 && $request->is_sent == 0){
				$request->is_sent = 1;
		        $mail_receivers = Settings::get('mail_receivers', []);
		        if(is_array($mail_receivers) && count($mail_receivers) > 0){
		            foreach($mail_receivers as $mail_receiver){
		            	if($request->gender == 'M'){
		            		$gender = 'De heer';
		            	}else{
		            		$gender = 'Mevrouw';
		            	}
		                $param = [
						    'company' => $request->company,
						    'gender' => $gender,
						    'first_name' => $request->first_name,
						    'last_name' => $request->last_name,
						    'email' => $request->email,
						    'phone' => $request->phone,
						    'question' => $request->question,
						];
						if(isset($request->attachment->id)){
							Mail::send('mmid.expertbox::mail.notify-admin-on-request', $param, function($message) use ($request, $mail_receiver){
								$message->attach($request->attachment->getPath());
							    $message->to($mail_receiver['email']);
							});
						}else{
		                	Mail::sendTo($mail_receiver['email'], 'mmid.expertbox::mail.notify-admin-on-request', $param);
						}
		            }
		        }
			}
			$request->save();
		}
		return $res;
	}

}