<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'total_amount' => $this->total_amount,
            'status' => $this->status,
            'order_date' => $this->order_date,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            // Add other fields as needed
        ];
    }
}
