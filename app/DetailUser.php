<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailUser extends Model
{
    use SoftDeletes;
    
    protected $table = 'detail_user';
    protected $guard = [];

    protected $attributes = [
        'organization'      => 'Hacker',
        'country_id'        => 102,         //indonesia
        'state'             => 'Jawa Barat',
        'city'              => 'Tangerang',
        'street'            => 'Jln Saja deh, no 88 Tangerang Selatan',
        'postal_code'       => '15345',
    ];

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function country(){
        return $this->hasOne(Country::class, 'id', 'country_id');
    }
}
