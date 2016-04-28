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
        'filename', 'mime', 'original_filename', 'title', 'url', 'thumbnail', 'order_num', 'product_id', 'user_id', 'supplier_id'
    ];

    /**
     * Get the user that owns the product.
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    /**
     * Get the supplier that owns the product.
     */
    public function supplier()
    {
        return $this->belongsTo('App\Supplier', 'supplier_id', 'id');
    }

    /**
     * Get the user that owns the product.
     */
    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id', 'product_id');
    }
}
