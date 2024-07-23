<?php

namespace App\Repositories\Website;

use App\Models\WishList;
use App\Models\WishListItem;
use App\Repositories\Interfaces\Website\WishListRepositoryInterface;

class WishListRepository implements WishListRepositoryInterface
{
    public function getWishList($userId)
    {
        return WishList::with(['items.book'])->where('user_id', $userId)->first();
    }

    public function addItem($userId, $bookId)
    {
        $wishList = WishList::firstOrCreate(['user_id' => $userId]);

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
