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
        return [
            'id' => $this->id,
            'title' => $this->title, // Assuming title is automatically decoded by Spatie's translatable package
            'author' => new AuthorResource($this->whenLoaded('author')),
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
            'description' => $this->description, // Assuming description is automatically decoded
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}