<?php

namespace App\Repositories\Website;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Book;
use App\Repositories\Interfaces\Website\CartRepositoryInterface;

class CartRepository implements CartRepositoryInterface
{
    public function getCart($userId)
    {
        return Cart::with(['items.book'])->firstOrCreate(['user_id' => $userId]);
    }

    public function addItem($userId, $bookId, $quantity)
    {
        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('book_id', $bookId)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'book_id' => $bookId,
                'quantity' => $quantity
            ]);
        }
    }

    public function updateItemQuantity($userId, $bookId, $quantity)
    {
        $cart = Cart::where('user_id', $userId)->first();

        if ($cart) {
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('book_id', $bookId)
                ->first();

            if ($cartItem) {
                $cartItem->quantity = $quantity;
                $cartItem->save();
            }
        }
    }

    public function removeItem($userId, $bookId)
    {
        $cart = Cart::where('user_id', $userId)->first();

        if ($cart) {
            CartItem::where('cart_id', $cart->id)
                ->where('book_id', $bookId)
                ->delete();
        }
    }

    public function clearCart($userId)
    {
        $cart = Cart::where('user_id', $userId)->first();

        if ($cart) {
            CartItem::where('cart_id', $cart->id)->delete();
        }
    }

    public function getCartTotal($userId)
    {
        $cart = Cart::with(['items.book'])->where('user_id', $userId)->first();

        if (!$cart) {
            return 0;
        }

        return $cart->items->sum(function ($item) {
            return $item->quantity * $item->book->price;
        });
    }
}
