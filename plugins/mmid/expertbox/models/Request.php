<?php namespace Mmid\Expertbox\Models;

use Model;

class Request extends Model{

    use \October\Rain\Database\Traits\Validation;
    
    public $table = 'mmid_expertbox_requests';
    protected $guarded = ['*'];
    protected $fillable = [
        'is_sent',
        'txn_status',
        'txn_id',
        'txn_amount',
        'txn_currency',
        'txn_gateway',
        'company',
        'gender',
        'first_name',
        'last_name',
        'email',
        'phone',
        'question',
        'attachment',
    ];

    public $attachOne = [
        'attachment' => [
            'System\Models\File',
            'delete' => TRUE,
        ],
    ];

    public $rules = [
    ];

}