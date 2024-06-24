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
            'title' => $this->title ?? 'No title',
            'description' => $this->description ?? 'No description',
            'creator' => $this->creator ? $this->creator->name : 'No creator',
            'cost' => $this->cost ?? 0,
            'image' => $this->image ?? 'No image',
            'rating' => $this->average_rating ?? 0,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : 'No date'
        ];
    }



}
