<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\CourseResource;
use App\Http\Responses\Response;
use App\Models\Category;
use http\Exception\BadConversionException;
use Illuminate\Http\Request;
use function PHPUnit\Framework\isEmpty;

class CategoryController extends Controller
{
    public function list()
    {
        try {
            $categories = Category::all();
            return Response::success('All categories : ', CategoryResource::collection($categories));
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage(), 500);
        }
    }

    public function categoryCourses($category_id)
    {
        try {
            $courses = Category::firstWhere('id', $category_id)->courses;
            if (isEmpty($courses)) throw new \Exception('No courses in this category yet.');
            return Response::success('Courses : ', CourseResource::collection($courses));
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage(), 500);
        }
    }
}
