<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWishListRequest;
use App\Http\Requests\UpdateWishListRequest;
use App\Http\Resources\Website\WishListResource;
use App\Models\WishList;
use App\Repositories\Interfaces\Website\WishListRepositoryInterface;
use Illuminate\Http\Request;

class WishListController extends Controller
{
    protected $wishListRepository;

    public function __construct(WishListRepositoryInterface $wishListRepository)
    {
        $this->wishListRepository = $wishListRepository;
    }


    public function index()
    {
        $userId = auth()->id();
        $wishList = $this->wishListRepository->getWishList($userId);
        return new WishListResource($wishList);
    }

    public function addItem(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $userId = auth()->id();
        $this->wishListRepository->addItem($userId, $request->book_id);

        return response()->json(['message' => __('wishlist.item_added')], 201);
    }

    public function toggleItem(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);
        $userId = auth()->id();
        $this->wishListRepository->toggleItem($userId, $request->book_id);
        return response()->json(['message' => __('wishlist.item_toggled')], 201);
    }

    public function removeItem(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $userId = auth()->id();
        $this->wishListRepository->removeItem($userId, $request->book_id);

        return response()->json(['message' => __('wishlist.item_removed')], 200);
    }

    public function clear()
    {
        $userId = auth()->id();
        $this->wishListRepository->clearWishList($userId);

        return response()->json(['message' => __('wishlist.cleared')], 200);
    }

    public function checkItem(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $userId = auth()->id();
        $isInWishList = $this->wishListRepository->isItemInWishList($userId, $request->book_id);

        return response()->json([
            'in_wish_list' => $isInWishList,
            'message' => $isInWishList ? __('wishlist.item_exists') : __('wishlist.item_not_exists')
        ], 200);
    }
}
