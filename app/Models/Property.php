<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{

    protected $fillable = ['latitude', 'longitude'];


    public function address()
    {
        return $this->belongsTo(Address::class , 'id' , 'address_id');
    }
}
