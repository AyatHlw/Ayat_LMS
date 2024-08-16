<?php

namespace App\Services;

use App\Models\Course;
use App\Models\UserCategoryPreference;
use Dompdf\Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    public function savePreferences(array $categoryIds)
    {
        DB::beginTransaction();
        try {
            $userId = Auth::id();

            //delete old info
            UserCategoryPreference::where('user_id', $userId)->delete();

            //create new info
            foreach ($categoryIds as $categoryId) {
                $preferences = UserCategoryPreference::create([
                    'user_id' => $userId,
                    'category_id' => $categoryId,
                ]);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getUserRecommendedCourses()
    {
        DB::beginTransaction();

        try {
            $userId = Auth::id();
            $categoryIds = UserCategoryPreference::where('user_id', $userId)->pluck('category_id');

            $data = Course::whereIn('category_id', $categoryIds)
                ->where('is_reviewed', 0)
                ->orderBy('average_rating', 'DESC')
                ->take(10)
                ->get();

            DB::commit();

            return $data;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }

}
