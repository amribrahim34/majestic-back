<?php

namespace App\Http\Resources\Website\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'book_id' => $this->book_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'title' => $this->title,
            'isbn13' => $this->isbn13,
            'book' => new BookResource($this->whenLoaded('book')),
        ];
    }
}
