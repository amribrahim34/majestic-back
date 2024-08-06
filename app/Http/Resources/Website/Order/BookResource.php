<?php

namespace App\Http\Resources\Website\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
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
            'title' => $this->title,
            'category_id' => $this->category_id,
            'publisher_id' => $this->publisher_id,
            'publication_date' => $this->publication_date,
            'language_id' => $this->language_id,
            'isbn10' => $this->isbn10,
            'isbn13' => $this->isbn13,
            'num_pages' => $this->num_pages,
            'dimensions' => $this->dimensions,
            'weight' => $this->weight,
            'format' => $this->format,
            'price' => $this->price,
            'stock_quantity' => $this->stock_quantity,
            'description' => $this->description,
            'img' => $this->img,
            'order_count' => $this->order_count,
            'average_rating' => $this->average_rating,
        ];
    }
}
