<?php

namespace App\Http\Resources;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CoursesResource extends JsonResource
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
            'creator' => User::query()->where('id', $this->creator_id)->first()->name,
            'cost' => $this->cost,
            'Created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s')
        ];
    }
}
