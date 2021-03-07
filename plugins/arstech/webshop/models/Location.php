<?php

namespace Arstech\Webshop\Models;

use Model;
use OFFLINE\Mall\Models\Product;

/**
 * Model
 */
class Location extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'arstech_webshop_locations';

    /**
     * @var array Validation rules
     */
    public $rules = [

        'title' => 'required',
        'address' => 'required',
        'zipcode' => 'required',
        'city' => 'required',
        'latitude' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
        'longitude' => ['required', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/']

    ];

    public $hasMany = [
        'schedules' => 'Arstech\Webshop\Models\Schedule'
    ];

    public function isOnline()
    {
        return strtolower($this->title) == 'online' && empty($this->zipcode) ? true : false;
    }
}
