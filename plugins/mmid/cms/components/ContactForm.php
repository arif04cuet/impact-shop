<?php namespace Mmid\Cms\Components;

use Mail;
use Input;
use Flash;
use Redirect;
use Validator;
use ValidationException;
use Mmid\Cms\Models\Settings;
use Cms\Classes\ComponentBase;

class ContactForm extends ComponentBase{

    public function componentDetails(){
        return [
            'name' => 'Contact Form Component',
            'description' => 'Contact Form Component',
        ];
    }

    public function onSubmit(){
        $rules = [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'message' => 'required',
        ];
        $validation = Validator::make(Input::all(), $rules);
        if($validation->fails()){
            throw new ValidationException($validation);
        }
        $param = [
            'name' => post('name'),
            'email' => post('email'),
            'phone' => post('phone'),
            'message_body' => post('message'),
        ];
        $mail_receivers = Settings::get('mail_receivers', []);
        if(is_array($mail_receivers) && count($mail_receivers) > 0){
            foreach($mail_receivers as $mail_receiver){
                Mail::sendTo($mail_receiver['email'], 'mmid.cms::mail.contact-form', $param);
            }
        }
        Flash::success('Bedankt voor je bericht, wij nemen zo spoedig mogelijk contact met je op.');
        return Redirect::refresh();
    }

}