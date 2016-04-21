<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class BuyerSeller extends Authenticatable
{
    protected $table = 'buyer_seller';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'buyer_id', 'seller_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        
    ];
}
