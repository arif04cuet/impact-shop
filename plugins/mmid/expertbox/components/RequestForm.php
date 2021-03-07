<?php namespace Mmid\Expertbox\Components;

use Input;
use Flash;
use Redirect;
use Validator;
use ValidationException;
use ApplicationException;
use Cms\Classes\ComponentBase;
use Mmid\Expertbox\Models\Request;
use Mmid\Expertbox\Models\Settings;

class RequestForm extends ComponentBase{

    public function componentDetails(){
        return [
            'name' => 'Expertbox Request Form Component',
            'description' => 'Expertbox Request Form Component',
        ];
    }

    public function onRun(){
        $this->page['fees'] = Settings::get('fees', 0);
    }

    public function onSubmit(){
        $fees = Settings::get('fees', 0);
        $description = Settings::get('description', 'Expertbox');
        $rules = [
            'gender' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'question' => 'required',
        ];
        $validation = Validator::make(Input::all(), $rules);
        if($validation->fails()){
            throw new ValidationException($validation);
        }
        $create = [
            'is_sent' => 0,
            'txn_status' => 0,
            'txn_id' => '',
            'txn_amount' => $fees,
            'txn_currency' => 'EUR',
            'txn_gateway' => 'MOLLIE',
            'company' => post('company'),
            'gender' => post('gender'),
            'first_name' => post('first_name'),
            'last_name' => post('last_name'),
            'email' => post('email'),
            'phone' => post('phone'),
            'question' => post('question'),
        ];
        if(Input::hasFile('attachment')){
            $create['attachment'] = Input::file('attachment');
        }
        $request = Request::create($create);
        $mollie = new \Mmid\Mollie\Classes\MollieGateway();
        $redirect_url = url('/mmid/expertbox/mollie/handler/return/'.$request->id);
        $webhook_url = url('/mmid/expertbox/mollie/handler/webhook');
        $res = $mollie->createPayment($fees, $description, $redirect_url, $webhook_url);
        if($res['status'] === TRUE){
            $request->txn_id = $res['data']['txn_id'];
            $request->save();
            return Redirect::to($res['data']['checkout_url']);
        }else{
            $request->delete();
            throw new ApplicationException($res['error']);
        }
    }

}