<?php namespace Mmid\Team\Components;

use Flash;
use Request;
use ApplicationException;
use Cms\Classes\ComponentBase;
use RainLab\Pages\Classes\Page as StaticPage;

class TeamPage extends ComponentBase{
	
	use \Mmid\Team\Traits\TeamHelper;

    public function componentDetails(){
        return [
            'name' => 'Team Pagina',
            'description' => 'Impact Team Pagina',
        ];
    }

    public function onRun(){
		$this->page['team_divisions'] = $this->getDivisions();
		$this->page['team_members'] = $this->getMembers();
    }

}