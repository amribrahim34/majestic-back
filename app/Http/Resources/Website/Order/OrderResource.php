<?php

namespace App\Http\Resources\Website\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'user_id' => $this->user_id,
            'shipping_cost' => $this->shipping_cost,
            'order_date' => $this->order_date,
            'city' => $this->city,
            'shipping_address' => $this->shipping_address,
            'country' => $this->country,
            'total_amount' => $this->total_amount,
            'status' => $this->status,
            'shipment_tracking_number' => $this->shipment_tracking_number,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
