<?php

namespace App\Http\Resources\Admin;

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
            'authors' => AuthorResource::collection($this->whenLoaded('authors')),
            'author_id' => $this->author_id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'category_id' => $this->category_id,
            'publisher' => new PublisherResource($this->whenLoaded('publisher')),
            'publisher_id' => $this->publisher_id,
            'language' => new LanguageResource($this->whenLoaded('language')),
            'language_id' => $this->language_id,
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
            'img' => $this->img,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
