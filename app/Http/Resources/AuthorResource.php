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
        $locale = App::getLocale();

        return [
            'id' => $this->id,
            'first_name' => $this->getTranslations('first_name'),
            'last_name' => $this->getTranslations('last_name'),
            'middle_name' => $this->getTranslations('middle_name'), // false to return null if no translation is available
            'biography' => $this->getTranslations('biography'),
            'birth_date' => $this->birth_date,
            'death_date' => $this->death_date,
            'country' => $this->country,
        ];
    }
}
