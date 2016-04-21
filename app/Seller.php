<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Seller extends Authenticatable
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'middle_name', 'company', 'user_type', 'email', 'password', 'github_id', 'avatar'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    /**
     * The sellers that belong to the user.
     */
    public function users()
    {
        return $this->belongsToMany('App\User', 'buyer_seller', 'seller_id', 'buyer_id');
    }

    /**
     * Get the comments for the blog post.
     */
    public function products()
    {
        return $this->hasMany('App\Product', 'seller_id');
    }
}
