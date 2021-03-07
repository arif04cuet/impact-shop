<?php namespace Mmid\Team;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents(){
    	return [
    		'Mmid\Team\Components\TeamPage' => 'TeamPage',
        ];
    }

    public function registerSettings()
    {
    }
}
