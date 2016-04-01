<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MediaItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'filename', 'mime', 'original_filename', 'title', 'url', 'thumbnail', 'order_num', 'product_id', 'user_id', 'seller_id'
    ];

    /**
     * Get the user that owns the product.
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    /**
     * Get the seller that owns the product.
     */
    public function seller()
    {
        return $this->belongsTo('App\Seller', 'seller_id', 'id');
    }

    /**
     * Get the user that owns the product.
     */
    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id', 'product_id');
    }
}
