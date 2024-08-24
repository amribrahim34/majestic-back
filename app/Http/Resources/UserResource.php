<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_name' => $this->user_name,
            'email' => $this->email,
            // 'address' => $this->address,
            // 'city' => $this->city,
            // 'state_province' => $this->state_province,
            // 'country' => $this->country,
            'mobile' => $this->mobile,
            'gender' => $this->gender,
            'avatar' => $this->avatar,
            'birthday' => $this->birthday,
            // 'addresses' => AddressResource::collection($this->whenLoaded('addresses')),
            'default_address' => AddressResource::getDefaultAddress($this->id),

            // 'last_login' => $this->last_login,
            // 'cart' => CartResource::collection($this->whenLoaded('cart')),
            // 'wishlist' => new WishListResource($this->whenLoaded('wishlist')),

        ];
    }
}
