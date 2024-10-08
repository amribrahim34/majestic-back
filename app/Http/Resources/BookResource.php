<?php

namespace App\Http\Resources;

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
        $locale = $request->header('Accept-Language', app()->getLocale());

        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => $this->image,
            'authors' => AuthorResource::collection($this->whenLoaded('authors')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'publisher' => new PublisherResource($this->whenLoaded('publisher')),
            'language' => new LanguageResource($this->whenLoaded('language')),
            'publication_date' => $this->publication_date,
            'isbn10' => $this->isbn10,
            'isbn13' => $this->isbn13,
            'num_pages' => $this->num_pages,
            'dimensions' => $this->dimensions,
            'weight' => $this->weight,
            'format' => $this->format,
            'price' => $this->price,
            'stock_quantity' => $this->stock_quantity,
            'description' => $this->description,
            'is_wishlisted' => $this->is_wishlisted,

            'order_count' => $this->order_count,
            'average_rating' => intval($this->average_rating)  ?? 0,

            'img' => $this->img,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
