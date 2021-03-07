<?php namespace Mmid\Expertbox;

use System\Classes\PluginBase;

class Plugin extends PluginBase{

    public $require = [
        'Mmid.Mollie',
    ];

	public function registerComponents(){
    	return [
    		'Mmid\Expertbox\Components\RequestForm' => 'ExpertboxRequestForm',
    	];
    }

	public function registerMailTemplates(){
	    return [
	        'mmid.expertbox::mail.notify-admin-on-request' => 'Expertbox - Notify admin on request',
	    ];
	}

    public function registerSettings(){
		return [
            'settings' => [
                'label' => 'Expertbox',
                'description' => 'Beheer fee en ontvangers',
                'category' => 'IMPACT',
                'icon' => 'oc-icon-lightbulb-o',
                'class' => 'Mmid\Expertbox\Models\Settings',
                'order' => 10,
            ],
        ];
    }

}