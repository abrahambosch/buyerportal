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
        'list_name', 'user_id', 'supplier_id'
    ];

    /**
     * Get the items in the list
     */
    public function items()
    {
        return $this->hasMany('App\ProductListItem');
    }

    /**
     * Get the supplier of the products in the list
     */
    public function supplier()
    {
        return $this->belongsTo('App\Supplier', 'supplier_id', 'id');
    }

    /**
     * Get the user that owns the product list.
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
    
}
