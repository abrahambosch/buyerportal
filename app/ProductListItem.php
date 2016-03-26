<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductListItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id', 'product_list_id'
    ];

    /**
     * Get the user that owns the phone.
     */
    public function product_list()
    {
        return $this->belongsTo('App\ProductList', 'product_list_id', 'id');
    }

    /**
     * Get the user that owns the phone.
     */
    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id', 'product_id');
    }
}
