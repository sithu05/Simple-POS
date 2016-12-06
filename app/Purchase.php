<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $dates = ['purchased_date'];

    public function products()
    {
    	return $this->hasMany(PurchaseDetail::class);
    }
}
