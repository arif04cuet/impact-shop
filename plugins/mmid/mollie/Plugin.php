<?php namespace Mmid\Mollie;

use System\Classes\PluginBase;

class Plugin extends PluginBase{

    public function registerComponents(){
    }

	public function registerPermissions(){
        return [
            'mmid.mollie.access_settings' => [
                'tab' => 'Mollie Payment Gateway',
                'label' => 'Access Settings',
            ],
        ];
    }

	public function registerSettings(){
    	return [
            'settings' => [
                'label' => 'Mollie Payment Gateway',
                'description' => 'API configuratie',
                'category' => 'IMPACT',
                'icon' => 'oc-icon-credit-card',
                'class' => 'Mmid\Mollie\Models\Settings',
                'permissions' => ['mmid.mollie.access_settings'],
                'keywords' => 'mollie payment gateway',
                'order' => 30,
            ],
        ];
    }

}