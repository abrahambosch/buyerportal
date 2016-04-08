<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $primaryKey = 'product_id';
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['product_id'];

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

    /**
     * Get the items in the list
     */
    public function images()
    {
        return $this->hasMany('App\MediaItem');
    }

    public function getThumbnail()
    {
        $image = $this->getFeaturedImage();
        if (!empty($image)) {
            return $image->thumbnail;
        }
        return "";
    }

    public function getFeaturedImageUrl()
    {
        $image = $this->getFeaturedImage();
        if (!empty($image)) {
            return $image->url;
        }
        return "";
    }

    public function getFeaturedImage()
    {
        return $this->images()->first();    // todo: change this to check for the featured image.
    }
}
