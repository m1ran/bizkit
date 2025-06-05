<?php

namespace App\Http\Resources;

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
        $order = [
            'id'          => $this->id,
            'num'         => $this->num,
            'status_id'   => $this->status_id,
            'customer_id' => $this->customer_id,
            'first_name'  => $this->first_name,
            'last_name'   => $this->last_name,
            'phone'       => $this->phone,
            'address'     => $this->address,
            'total_cost'  => $this->total_cost,
            'total_price' => $this->total_price,
            'notes'       => $this->notes,
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
        ];

        if  (!empty($this->customer)) {
            $order['customer'] = CustomerResource::make($this->customer);
        }

        if (!empty($this->items)) {
            $order['items'] = OrderItemResource::collection($this->items);
        }

        return $order;
    }
}
