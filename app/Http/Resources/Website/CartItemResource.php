<?php

namespace App\Http\Resources\Website;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isArrayItem = is_array($this->resource);
        $quantity = $isArrayItem ? $this->resource['quantity'] : $this->quantity;
        $book = $isArrayItem ? $this->resource['book'] : $this->book;



        return [
            'id' => $isArrayItem ? null : $this->id,
            'book_id' => $isArrayItem ? $this->resource['book_id'] : $this->book_id,
            'quantity' => $isArrayItem ? $this->resource['quantity'] : $this->quantity,
            'book' => new BookResource($book),
            'subtotal' => $quantity * $book->price,
        ];
    }
}
