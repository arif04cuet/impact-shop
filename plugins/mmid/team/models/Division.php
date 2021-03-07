<?php namespace Mmid\Team\Models;

use Model;

/**
 * Model
 */
class Division extends Model
{
    use \October\Rain\Database\Traits\Validation;
	use \October\Rain\Database\Traits\Sortable;
    
    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;
	
    /**
     * @var string The database table used by the model.
     */
    public $table = 'mmid_team_divisions';

    /**
     * @var array Validation rules
     */
	public $rules = [
    	'title' => 'required',
    ];
}
