<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgencyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'email' => $this->email,
            'status' => $this->status,
            'subscription_plan' => $this->subscription_plan,
            'subscription_status' => $this->subscription_status,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
