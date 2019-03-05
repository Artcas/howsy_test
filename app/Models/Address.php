<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{

    protected $fillable = ['address_line_1', 'address_line_2', 'city', 'post_code'];

    public function property()
    {
        return $this->hasOne(Property::class , 'address_id' , 'id');
    }
}
