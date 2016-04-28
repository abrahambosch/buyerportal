<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the items in the list
     */
    public function items()
    {
        return $this->hasMany('App\PurchaseOrderItem');
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
    public function buyer()
    {
        return $this->belongsTo('App\User', 'buyer_id', 'id');
    }
}
