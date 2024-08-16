<?php

namespace App\Http\Resources;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'user' => $this->user->name,
            'user_id' => $this->user->id,
            'user_image' =>  $this->user->image ?? 'storage/uploads/1723489257_BrainLern.jpg.jpg',
            'course_id' => $this->course->id,
            'content' => $this->content,
            'rating' => $this->rating,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s')
        ];
    }
}
