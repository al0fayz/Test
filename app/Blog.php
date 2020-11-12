<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $table = 'blogs';
    protected $guard = [];

    public function user(){
        return $this->belongsTo('App\User');
    }
    public function category(){
        return $this->hasOne('App\BlogCategory', 'id', 'category_id');
    }
}
