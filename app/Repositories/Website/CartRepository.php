<?php

namespace App\Repositories\Website;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Book;
use App\Repositories\Interfaces\Website\CartRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CartRepository implements CartRepositoryInterface
{
    private function isGuest($userIdentifier)
    {
        return !is_numeric($userIdentifier);
    }

    public function getCart($userIdentifier)
    {
        return $this->isGuest($userIdentifier)
            ? $this->getGuestCart()
            : $this->getUserCart($userIdentifier);
    }

    private function getGuestCart()
    {
        return session()->get('cart', ['items' => []]);
    }

    private function getUserCart($userId)
    {
        return Cart::with(['items.book'])->firstOrCreate(['user_id' => $userId]);
    }

    public function addItem($userIdentifier, $bookId, $quantity)
    {
        $this->isGuest($userIdentifier)
            ? $this->addItemToGuestCart($bookId, $quantity)
            : $this->addItemToUserCart($userIdentifier, $bookId, $quantity);
    }

    private function addItemToGuestCart($bookId, $quantity)
    {
        $cart = $this->getGuestCart();
        $existingItem = $this->findItemInGuestCart($cart, $bookId);

        if ($existingItem) {
            $existingItem['quantity'] += $quantity;
        } else {
            $cart['items'][] = [
                'book_id' => $bookId,
                'quantity' => $quantity,
                'book' => Book::find($bookId)
            ];
        }

        session()->put('cart', $cart);
    }

    private function findItemInGuestCart($cart, $bookId)
    {
        foreach ($cart['items'] as &$item) {
            if ($item['book_id'] == $bookId) {
                return $item;
            }
        }
        return null;
    }

    private function addItemToUserCart($userId, $bookId, $quantity)
    {
        $cart = $this->getUserCart($userId);
        $cartItem = $cart->items->where('book_id', $bookId)->first();

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

    public function updateItemQuantity($userIdentifier, $bookId, $quantity)
    {
        $this->isGuest($userIdentifier)
            ? $this->updateGuestItemQuantity($bookId, $quantity)
            : $this->updateUserItemQuantity($userIdentifier, $bookId, $quantity);
    }

    // private function updateGuestItemQuantity($bookId, $quantity)
    // {
    //     $cart = $this->getGuestCart();
    //     $item = $this->findItemInGuestCart($cart, $bookId);

    //     if ($item) {
    //         $item['quantity'] = $quantity;
    //         session()->put('cart', $cart);
    //     }
    // }

    private function updateGuestItemQuantity($bookId, $quantity)
    {
        $cart = $this->getGuestCart();
        foreach ($cart['items'] as &$item) {
            if ($item['book_id'] == $bookId) {
                $item['quantity'] = $quantity;
                break;
            }
        }
        session()->put('cart', $cart);
    }

    private function updateUserItemQuantity($userId, $bookId, $quantity)
    {
        $cart = $this->getUserCart($userId);
        $cartItem = $cart->items->where('book_id', $bookId)->first();

        if ($cartItem) {
            $cartItem->quantity = $quantity;
            $cartItem->save();
        }
    }

    public function removeItem($userIdentifier, $bookId)
    {
        $this->isGuest($userIdentifier)
            ? $this->removeItemFromGuestCart($bookId)
            : $this->removeItemFromUserCart($userIdentifier, $bookId);
    }

    private function removeItemFromGuestCart($bookId)
    {
        $cart = $this->getGuestCart();
        $cart['items'] = array_filter($cart['items'], function ($item) use ($bookId) {
            return $item['book_id'] != $bookId;
        });
        session()->put('cart', $cart);
    }

    private function removeItemFromUserCart($userId, $bookId)
    {
        $cart = $this->getUserCart($userId);
        $cart->items()->where('book_id', $bookId)->delete();
    }

    public function clearCart($userIdentifier)
    {
        $this->isGuest($userIdentifier)
            ? session()->forget('cart')
            : $this->clearUserCart($userIdentifier);
    }

    private function clearUserCart($userId)
    {
        $cart = $this->getUserCart($userId);
        $cart->items()->delete();
    }

    public function getCartTotal($userIdentifier)
    {
        $cart = $this->getCart($userIdentifier);
        return $this->isGuest($userIdentifier)
            ? $this->getGuestCartTotal($cart)
            : $this->getUserCartTotal($cart);
    }

    private function getGuestCartTotal($cart)
    {
        return array_reduce($cart['items'], function ($total, $item) {
            return $total + ($item['quantity'] * $item['book']->price);
        }, 0);
    }

    private function getUserCartTotal($cart)
    {
        return $cart->items->sum(function ($item) {
            return $item->quantity * $item->book->price;
        });
    }

    public function transferGuestCart($sessionId, $userId)
    {
        $guestCart = $this->getGuestCart();
        $userCart = $this->getUserCart($userId);

        foreach ($guestCart['items'] as $item) {
            $this->addItemToUserCart($userId, $item['book_id'], $item['quantity']);
        }

        session()->forget('cart');
    }
}
