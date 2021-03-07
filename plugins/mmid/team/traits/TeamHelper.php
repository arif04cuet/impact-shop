<?php namespace Mmid\Team\Traits;

use Mmid\Team\Models\Division;
use Mmid\Team\Models\Member;

trait TeamHelper{
	
	public function getMembers(){
		$members = Member::where('is_active', '=', 1)
		->orderBy('sort_order', 'asc')
		->get();
		return $members;
	}

	public function getDivisions(){
		$divisions = Division::where('is_active', '=', 1)
		->orderBy('sort_order', 'asc')
		->get();
		return $divisions;
	}
		
}