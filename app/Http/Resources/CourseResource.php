<?php

namespace App\Http\Resources;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CourseResource extends JsonResource
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
            'category' => $this->category->name,
            'title' => $this->title ?? 'No title',
            'description' => $this->description ?? 'No description',
            'creator' => $this->creator->name,
            'creator_id' => $this->creator_id,
            'cost' => $this->cost ?? 0,
            'image' => $this->image ?? 'No image',
            'rating' => $this->average_rating,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : 'No date'
        ];
    }
}
