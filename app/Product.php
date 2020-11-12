<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'product';
    protected $guard = [];

    public function category(){
        return $this->hasOne(CategoryProduct::class, 'category_id');
    }
}
