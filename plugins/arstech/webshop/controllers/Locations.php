<?php

namespace Arstech\Webshop\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Locations extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController'
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'arstech.webshop.manage_locations'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('OFFLINE.Mall', 'mall-catalogue', 'locations');
    }
}
