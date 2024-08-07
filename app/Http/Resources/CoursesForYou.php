<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CoursesForYou extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title ?? 'No title',
            'creator' => $this->creator->name,
            'category' => $this->category->name,
            'cost' => $this->cost ?? 0,
            'image' => $this->image ?? 'No image',
            'rating' => $this->average_rating ?? 0,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : 'No date'
        ];
    }
}
