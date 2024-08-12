<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CourseResource;
use App\Http\Responses\Response;
use App\Models\Category;
use App\Services\Course\CourseService;
use App\Services\QuizService;
use http\Exception\BadConversionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;
use function PHPUnit\Framework\isEmpty;

class CategoryController extends Controller
{

    private CourseService $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function list()
    {
        try {
            $categories = Category::all();
            return Response::success('All categories : ', CategoryResource::collection($categories));
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage(), 500);
        }
    }

    public function createCategory(CreateCategoryRequest $request): JsonResponse
    {
        try {
            $data = $this->courseService->createCategory($request);
            return response()->json([
                'message' => $data['message'],
                'category' => $data['category']
            ], 201);
        } catch (Throwable $throwable) {
            return Response::error($throwable->getMessage());
        }
    }

    public function categoryCourses($category_id)
    {
        try {
            $category = Category::with('courses')->find($category_id);
            if (!$category || $category->courses->isEmpty()) throw new \Exception('No courses in this category yet.');
            return Response::success('Courses : ', CourseResource::collection($category->courses));
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage(), 500);
        }
    }
    public function update(Request $request, $category_id){
        try {
            $data = $this->courseService->updateCategory($request, $category_id);
            return Response::success($data['message'], $data['category']);
        } catch (Throwable $e){
            return Response::error($e->getMessage(), $e->getCode());
        }
    }
    public function destroy($category_id){
        try {
            $data = $this->courseService->destroyCategory($category_id);
            return Response::success($data['message']);
        } catch (Throwable $e){
            return Response::error($e->getMessage(), $e->getCode());
        }
    }
}
