<?php

namespace App\Http\Resources\Website;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'book_id' => $this->book_id,
            'quantity' => $this->quantity,
            'book' => new BookResource($this->book),
            'subtotal' => $this->quantity * $this->book->price,
        ];
    }
}
