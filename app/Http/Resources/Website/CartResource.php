<?php

namespace App\Http\Resources\Website;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Check if $this->resource is an array (guest cart) or an object (user cart)
        $isGuestCart = is_array($this->resource);

        return [
            'id' => $isGuestCart ? null : $this->id,
            'user_id' => $isGuestCart ? null : $this->user_id,
            'items' => $isGuestCart
                ? CartItemResource::collection($this->resource['items'] ?? [])
                : CartItemResource::collection($this->items),
            'total' => $this->calculateTotal($isGuestCart),
        ];
    }

    private function calculateTotal($isGuestCart)
    {
        if ($isGuestCart) {
            return collect($this->resource['items'] ?? [])->sum(function ($item) {
                return $item['quantity'] * ($item['book']->price ?? 0);
            });
        } else {
            return $this->items->sum(function ($item) {
                return $item->quantity * $item->book->price;
            });
        }
    }
}
