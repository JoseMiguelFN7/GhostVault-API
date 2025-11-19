<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SecretResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'content' => $this->resource->content,
            'requires_password' => $this->resource->requires_password,
            //Files associated with the secret (TO BE IMPLEMENTED)
        ];
    }
}
