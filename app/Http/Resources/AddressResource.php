<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'postal_code' => $this->postal_code,
            'is_default' => $this->is_default,
        ];
    }



    /**
     * Get the default address for a user.
     *
     * @param  int  $userId
     * @return self|null
     */
    public static function getDefaultAddress($userId)
    {
        $defaultAddress = \App\Models\Address::where('user_id', $userId)
            ->default()
            ->first();

        return $defaultAddress ? new self($defaultAddress) : null;
    }
}
