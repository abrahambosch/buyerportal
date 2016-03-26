<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $primaryKey = 'product_id'; 
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_name', 'product_description', 'sku', 'upc', 'cost', 'price', 'user_id', 'seller_id'
    ];

    /**
     * Get the seller of this item
     */
    public function seller()
    {
        return $this->belongsTo('App\Seller', 'seller_id', 'id');
    }

    /**
     * Get the user that owns the product.
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }





}
