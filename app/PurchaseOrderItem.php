<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];


    /**
     * 
     */
    public function purchase_order()
    {
        return $this->belongsTo('App\PurchaseOrder', 'purchase_order_id', 'id');
    }

    /**
     * 
     */
    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id', 'product_id');
    }
}
