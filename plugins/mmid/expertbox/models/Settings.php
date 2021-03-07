<?php namespace Mmid\Expertbox\Models;

use Model;

class Settings extends Model{

	use \October\Rain\Database\Traits\Validation;

    public $implement = ['System.Behaviors.SettingsModel'];
    public $settingsCode = 'mmid_expertbox_settings';
    public $settingsFields = 'fields.yaml';

    public $rules = [
    	'fees' => 'required|numeric',
    	'description' => 'required',
    	'mail_receivers.*.email' => 'required|email',
    ];

}