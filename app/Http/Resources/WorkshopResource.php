<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkshopResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title ?? 'No title',
            'teacher' => $this->teacher->name,
            'category' => $this->category->name ?? 'valid category',
            'description' => $this->description ?? 'No description',
            'rating' => $this->average_rating,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'created_at' => $this->created_at->format('Y-m-d H:i:s')
        ];
    }
}
