<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishList extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'wishlist_name'];

    public function items()
    {
        return $this->hasMany(WishListItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
