<?php namespace Mmid\Cms\Components;

use Mmid\Cms\Models\Settings;
use Cms\Classes\ComponentBase;
use OFFLINE\Mall\Models\Product;

class Webshop extends ComponentBase{

    public function componentDetails(){
        return [
            'name' => 'CMS Webshop',
            'description' => 'CMS Webshop Component',
        ];
    }

    public function onRun(){
		$base_file_name = $this->getPage()->baseFileName;

        if($base_file_name == 'catalog'){
			
			$search = \Input::get("search");
			
			if(!empty($search)){
				$this->page['searchstring'] = explode("|", $search);

			}

		}
	
		$this->page['shop_count_products'] = Product::where('published', 1)->count();
		
		
		
    }

}