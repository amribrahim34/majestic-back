<?php

namespace App\Http\Resources\Website;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'price' => $this->price,
            'image' => $this->image,
            'order_count' => $this->order_count,
            'average_rating' => $this->average_rating,
            // Add other book fields as needed
        ];
    }
}
