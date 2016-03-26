<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductList extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'list_name', 'user_id', 'seller_id'
    ];

    /**
     * Get the items in the list
     */
    public function items()
    {
        return $this->hasMany('App\ProductListItem');
    }

    /**
     * Get the seller of the products in the list
     */
    public function seller()
    {
        return $this->belongsTo('App\Seller', 'seller_id', 'id');
    }

    /**
     * Get the user that owns the product list.
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
    
}
