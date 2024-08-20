<?php

namespace App\Repositories\Website;

use App\Models\WishList;
use App\Models\WishListItem;
use App\Repositories\Interfaces\Website\WishListRepositoryInterface;
use Illuminate\Support\Facades\Log;

class WishListRepository implements WishListRepositoryInterface
{
    public function getWishList($userId)
    {
        return WishList::with(['items.book'])
            ->firstOrCreate(
                ['user_id' => $userId],
                ['created_at' => now(), 'updated_at' => now(), 'wishlist_name' => 'default']
            );
    }
    public function addItem($userId, $bookId)
    {
        $wishList = WishList::firstOrCreate(['user_id' => $userId, 'wishlist_name' => 'default']);

        $wishListItem = WishListItem::where('wish_list_id', $wishList->id)
            ->where('book_id', $bookId)
            ->first();

        if (!$wishListItem) {
            WishListItem::create([
                'wish_list_id' => $wishList->id,
                'book_id' => $bookId
            ]);
        }
    }

    public function toggleItem($userId, $bookId)
    {
        try {
            $wishList = WishList::firstOrCreate(
                ['user_id' => $userId],
                ['wishlist_name' => 'default']
            );

            // $wishListItem = WishListItem::firstOrCreate(['wish_list_id' => $userId, 'book_id' => $bookId]);
            // $wishListItem = $wishList->items()->where('book_id', $bookId)->first();
            $wishListItem = $wishList->items()->where('book_id', $bookId)->first();
            Log::notice('this is the wishlist item', [$wishListItem]);
            if (!$wishListItem) {
                $wishList->items()->create(['book_id' => $bookId]);
            } else {
                $wishListItem->delete();
            }
        } catch (\Exception $e) {
            Log::error('Error toggling wishlist item: ' . $e->getMessage());
            throw $e;
        }
    }

    public function removeItem($userId, $bookId)
    {
        $wishList = WishList::where('user_id', $userId)->first();

        if ($wishList) {
            WishListItem::where('wish_list_id', $wishList->id)
                ->where('book_id', $bookId)
                ->delete();
        }
    }

    public function clearWishList($userId)
    {
        $wishList = WishList::where('user_id', $userId)->first();

        if ($wishList) {
            WishListItem::where('wish_list_id', $wishList->id)->delete();
        }
    }

    public function isItemInWishList($userId, $bookId)
    {
        $wishList = WishList::where('user_id', $userId)->first();

        if ($wishList) {
            return WishListItem::where('wish_list_id', $wishList->id)
                ->where('book_id', $bookId)
                ->exists();
        }

        return false;
    }
}
