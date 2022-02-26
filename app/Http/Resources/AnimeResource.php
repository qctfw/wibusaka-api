<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AnimeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'platform_name' => $this->platform->name,
            'platform_type' => $this->platform->type,
            'url' => $this->link,
            'is_paid' => $this->paid,
            'note' => $this->when(! empty($this->note), $this->note, null),
        ];
    }
}
