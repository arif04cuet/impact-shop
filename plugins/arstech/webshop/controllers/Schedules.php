<?php namespace Arstech\Webshop\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Schedules extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'asrtech.webshop.manage_schedules' 
    ];

    public function __construct()
    {
        parent::__construct();
    }
}
