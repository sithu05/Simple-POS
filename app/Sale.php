<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    public function products()
    {
    	return $this->hasMany(SaleDetail::class);
    }

    public function customer()
    {
    	return $this->belongsTo(Customer::class);
    }

    public function car()
    {
    	return $this->belongsTo(Car::class);
    }
}
