<?php namespace Mmid\Team\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Divisions extends Controller
{
    public $implement = [        
		'Backend\Behaviors\ListController',        
		'Backend\Behaviors\FormController',        
		'Backend\Behaviors\ReorderController'    
	];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $reorderConfig = 'config_reorder.yaml';

    public $requiredPermissions = [
        'mmid.team.manage' 
    ];

    public function __construct()
    {
        parent::__construct();
		BackendMenu::setContext('Mmid.Team', 'team', 'team-divisions');
    }
}
