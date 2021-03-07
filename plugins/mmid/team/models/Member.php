<?php namespace Mmid\Team\Models;

use Model;

/**
 * Model
 */
class Member extends Model
{
    use \October\Rain\Database\Traits\Validation;
	use \October\Rain\Database\Traits\Sortable;
	
    public $timestamps = false;
    public $table = 'mmid_team_members';
    public $rules = [
		'firstname' => 'required',
		'lastname' => 'required',
		'title' => 'required',
		'email' => 'required',
    ];
	
	public $attachOne = [
        'image' => ['System\Models\File'],
    ];
	
	public $belongsToMany = [
		'divisions' => [
			'Mmid\Team\Models\Division',
			'table'      => 'mmid_team_members_divisions',
			'conditions' => 'is_active = 1'
		]
	];
	
	public function getFullNameAttribute(){
		return $this->firstname . " " . $this->lastname;
	}
	
	public function getDivisionClassesAttribute(){
		$html = '';
		$divisions = $this->divisions()->get();
		if($divisions){
			foreach($divisions as $div){
				$html .= 'division_'.md5($div['id']).' ';
			}
		}
		return $html;
	}
}
