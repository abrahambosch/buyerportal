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
     * get the parent
     */
    public function product_list()
    {
        return $this->belongsTo('App\ProductList', 'product_list_id', 'id');
    }

    /**
     * get the product
     */
    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id', 'product_id');
    }
}
