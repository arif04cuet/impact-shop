<?php namespace Mmid\Mollie\Models;

use Model;

class Settings extends Model{

	use \October\Rain\Database\Traits\Validation;

    public $implement = ['System.Behaviors.SettingsModel'];
    public $settingsCode = 'mmid_mollie_settings';
    public $settingsFields = 'fields.yaml';

    public $rules = [
    	'test_api_key' => 'required',
		'live_api_key' => 'required',
    ];

}