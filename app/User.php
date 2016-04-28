<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
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
     * The suppliers that belong to the user.
     */
    public function suppliers()
    {
        return $this->belongsToMany('App\Supplier', 'buyer_supplier', 'buyer_id', 'supplier_id');
    }

    /**
     * Get the comments for the blog post.
     */
    public function products()
    {
        return $this->hasMany('App\Product');
    }

    
}
