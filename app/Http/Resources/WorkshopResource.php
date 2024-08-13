<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkshopResource extends JsonResource
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
            'title' => $this->title ?? 'No title',
            'teacher' => $this->teacher->first_name . $this->teacher->name,
            'category' => $this->category->name,
            'description' => $this->description ?? 'No description',
            'rating' => $this->average_rating,
            'start_date' => $this->start_date->format('Y-m-d H:i:s'),
            'end_date' => $this->end_date->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : 'No date'
        ];
    }
}
