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
                return Response::error('Please select at least one category', 400);
            }
            $this->recommendationService->savePreferences($categoryId);

            return Response::success('Nice! Enjoy a truly exceptional experience', ' ', 200);
        } catch (\Exception $e) {
            return Response::error($e->getMessage());
        }
    }

    public function getUserRecommendedCourses()
    {
        try {
            $forYouCourses = $this->recommendationService->getUserRecommendedCourses();
            return Response::success(
              'For you courses',
                CoursesForYou::collection($forYouCourses),
                200
            );

        } catch (\Exception $e) {
            return Response::error($e->getMessage());
        }
    }

}
