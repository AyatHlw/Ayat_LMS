<?php

namespace App\Http\Resources;

use App\Models\User;
use Carbon\Carbon;
use Carbon\PHPStan\AbstractMacro;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ShowCourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'creator' => User::query()->where('id',$this->creator_id)->first()->name,
            'cost' => $this->cost,
            'image' => $this->image_course,
            'rating' => $this->average_rating,
            'Created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s')
        ];
    }
}
