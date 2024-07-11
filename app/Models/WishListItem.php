<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishListItem extends Model
{
    use HasFactory;


    protected $fillable = ['wish_list_id', 'book_id'];

    public function wishList()
    {
        return $this->belongsTo(WishList::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
