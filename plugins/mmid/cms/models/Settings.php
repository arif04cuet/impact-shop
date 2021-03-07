<?php namespace Mmid\Cms\Models;

use Model;

class Settings extends Model{

	use \October\Rain\Database\Traits\Validation;

    public $implement = ['System.Behaviors.SettingsModel'];
    public $settingsCode = 'mmid_cms_settings';
    public $settingsFields = 'fields.yaml';

    public $rules = [
    	'mail_receivers.*.email' => 'required|email',
		'populair_courses.*.title' => 'required',
		'populair_courses.*.link' => 'required',
    ];

}