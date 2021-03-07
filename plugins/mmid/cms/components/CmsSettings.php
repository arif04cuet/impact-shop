<?php namespace Mmid\Cms\Components;

use Mmid\Cms\Models\Settings;
use Cms\Classes\ComponentBase;

class CmsSettings extends ComponentBase{

    public function componentDetails(){
        return [
            'name' => 'CMS Settings Component',
            'description' => 'Load information from backend settings',
        ];
    }

    public function onRun(){
        $base_file_name = $this->getPage()->baseFileName;
		
        if($base_file_name == 'catalog'){
			$populair_courses = Settings::get('populair_courses', []);
			if(is_array($populair_courses) && count($populair_courses) > 0){
				$this->page['populair_courses'] = $populair_courses;
			}
        } 
		
		$this->page['ref_big_name'] = Settings::get('ref_big_name');
		$this->page['ref_big_title'] = Settings::get('ref_big_title');
		$this->page['ref_big_quote'] = Settings::get('ref_big_quote');
		
    }

}