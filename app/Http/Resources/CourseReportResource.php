<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseReportResource extends JsonResource
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
            'Reporter_id' => $this->user_id,
            'Reporter_name' => $this->user->name,
            'course_id' => $this->course_id,
            'course_name' => $this->course->title,
            'created_at' => $this->created_at->format('Y-m-d H-m-s')
        ];
    }
}
