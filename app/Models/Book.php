<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Builder;

class Book extends Model
{
    use HasFactory;

    protected $appends = ['order_count', 'average_rating'];

    protected $fillable = [
        'title',
        'category_id',
        'publisher_id',
        'publication_date',
        'language_id',
        'isbn10',
        'isbn13',
        'num_pages',
        'dimensions',
        'weight',
        'format',
        'price',
        'stock_quantity',
        'description',
        'img',
    ];


    // Specify which attributes are translatable.
    // public $translatable = ['title', 'description'];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function getImageAttribute()
    {
        if ($this->img) {
            return secure_asset('storage/book_covers/' . $this->img);
        }
        return secure_asset('storage/book_covers/default.png');
    }

    public function getIsWishListedAttribute()
    {
        if (auth('sanctum')->check()) {
            $user = auth('sanctum')->user();
            Log::notice('this is the relation in the book model', [$user->wishlist()->exists(), $user->wishlist?->items()->where('book_id', $this->id)->exists()]);
            return $user->wishlist()->exists() && $user->wishlist->items()->where('book_id', $this->id)->exists();
        }
        return false;
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }


    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }


    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }


    public function getOrderCountAttribute()
    {
        return $this->orderItems()->sum('quantity');
    }

    public function getAverageRatingAttribute()
    {
        $average = $this->ratings()->avg('rating');
        return number_format($average, 1);
    }

    public function scopeSearch(Builder $query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhereHas('authors', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        });
    }
}
