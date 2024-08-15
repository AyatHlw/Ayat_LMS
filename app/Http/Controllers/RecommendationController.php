<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseResource;
use App\Http\Resources\CoursesForYou;
use App\Http\Responses\Response;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    protected $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    public function savePreferences(Request $request)
    {
        try {
            $categoryId = $request->input('category_ids', []);
            if (empty($categoryId)) {
                return Response::error(__('messages.save_preferences_error'), 400);
            }
            $this->recommendationService->savePreferences($categoryId);
            return Response::success(__('messages.save_preferences_success'), ' ');
        } catch (\Exception $e) {
            return Response::error($e->getMessage());
        }
    }

    public function getUserRecommendedCourses()
    {
        try {
            $forYouCourses = $this->recommendationService->getUserRecommendedCourses();
            return Response::success(
              __('messages.get_recommended_courses_success'),
                CourseResource::collection($forYouCourses)
            );
        } catch (\Exception $e) {
            return Response::error($e->getMessage());
        }
    }
}
