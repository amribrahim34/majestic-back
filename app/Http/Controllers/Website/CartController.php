<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Http\Resources\Website\CartResource;
use App\Repositories\Interfaces\Website\CartRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    protected $cartRepository;

    public function __construct(CartRepositoryInterface $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    public function index(): JsonResponse
    {
        $userId = auth()->id();
        $cart = $this->cartRepository->getCart($userId);
        $total = $this->cartRepository->getCartTotal($userId);

        return response()->json([
            'message' => __('cart.retrieved'),
            'data' => new CartResource($cart),
            'total' => $total
        ]);
    }

    public function addItem(StoreCartRequest $request): JsonResponse
    {
        $userId = auth()->id();
        $this->cartRepository->addItem($userId, $request->book_id, $request->quantity);
        $cart = $this->cartRepository->getCart($userId);

        return response()->json([
            'message' => __('cart.item_added'),
            'data' => new CartResource($cart)
        ], 201);
    }

    public function updateItem(UpdateCartRequest $request): JsonResponse
    {
        $userId = auth()->id();
        $this->cartRepository->updateItemQuantity($userId, $request->book_id, $request->quantity);
        $cart = $this->cartRepository->getCart($userId);

        return response()->json([
            'message' => __('cart.item_updated'),
            'data' => new CartResource($cart)
        ]);
    }

    public function removeItem(Request $request): JsonResponse
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $userId = auth()->id();
        $this->cartRepository->removeItem($userId, $request->book_id);
        $cart = $this->cartRepository->getCart($userId);

        return response()->json([
            'message' => __('cart.item_removed'),
            'data' => new CartResource($cart)
        ]);
    }

    public function clear(): JsonResponse
    {
        $userId = auth()->id();
        $this->cartRepository->clearCart($userId);

        return response()->json([
            'message' => __('cart.cleared')
        ]);
    }
}
