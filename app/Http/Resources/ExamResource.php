<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'description' => $this->description,
            'code' => $this->code,
            'status' => $this->status,
            'status_id' => $this->status_id,
            'created_at' => $this->created_at->format('d M Y'),
        ];
    }
}
