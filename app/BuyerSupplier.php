<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class BuyerSupplier extends Authenticatable
{
    protected $table = 'buyer_supplier';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'buyer_id', 'supplier_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        
    ];
}
