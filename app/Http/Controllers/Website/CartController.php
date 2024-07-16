<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Http\Resources\Website\CartResource;
use App\Repositories\Interfaces\Website\CartRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    protected $cartRepository;

    public function __construct(CartRepositoryInterface $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    private function getUserIdentifier()
    {
        return Auth::check() ? Auth::id() : session()->getId();
    }

    public function index(): JsonResponse
    {
        $userIdentifier = $this->getUserIdentifier();
        $cart = $this->cartRepository->getCart($userIdentifier);
        $total = $this->cartRepository->getCartTotal($userIdentifier);

        return response()->json([
            'message' => __('cart.retrieved'),
            'data' => new CartResource($cart),
            'total' => $total
        ]);
    }

    public function addItem(StoreCartRequest $request): JsonResponse
    {
        $userIdentifier = $this->getUserIdentifier();
        $this->cartRepository->addItem($userIdentifier, $request->book_id, $request->quantity);
        $cart = $this->cartRepository->getCart($userIdentifier);

        return response()->json([
            'message' => __('cart.item_added'),
            'data' => new CartResource($cart)
        ], 201);
    }

    public function updateItem(UpdateCartRequest $request): JsonResponse
    {
        $userIdentifier = $this->getUserIdentifier();
        $this->cartRepository->updateItemQuantity($userIdentifier, $request->book_id, $request->quantity);
        $cart = $this->cartRepository->getCart($userIdentifier);

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

        $userIdentifier = $this->getUserIdentifier();
        $this->cartRepository->removeItem($userIdentifier, $request->book_id);
        $cart = $this->cartRepository->getCart($userIdentifier);

        return response()->json([
            'message' => __('cart.item_removed'),
            'data' => new CartResource($cart)
        ]);
    }

    public function clear(): JsonResponse
    {
        $userIdentifier = $this->getUserIdentifier();
        $this->cartRepository->clearCart($userIdentifier);

        return response()->json([
            'message' => __('cart.cleared')
        ]);
    }
}
