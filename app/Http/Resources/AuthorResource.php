<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class AuthorResource extends JsonResource
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
            'name' => $this->name,
            'biography' => $this->biography,
            'birth_date' => $this->birth_date,
            'death_date' => $this->death_date,
            'country' => $this->country,
        ];
    }
}
