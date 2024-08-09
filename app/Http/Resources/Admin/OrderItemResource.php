<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'book_id' => $this->book_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'title' => $this->title,
            'isbn13' => $this->isbn13,
            'book' => new BookResource($this->book),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
