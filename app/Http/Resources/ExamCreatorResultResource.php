<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed description
 * @property mixed uuid
 * @property mixed name
 * @property mixed participant
 */
class ExamCreatorResultResource extends JsonResource
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
            'participant' => $this->participant,
            'finish_participant' => $this->finish_participant,
        ];
    }
}
