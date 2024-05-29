<?php

namespace App\Http\Controllers;

use App\Http\Resources\CoursesResource;
use App\Http\Resources\ShowCourseResource;
use App\Http\Responses\Response;
use App\Models\Course;
use App\Services\CourseService;
use App\Services\ResetPasswordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Psy\Exception\ThrowUpException;
use function Laravel\Prompts\error;

class CourseController extends Controller
{
    private CourseService $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    /**
     * Display a listing of the resource.
     */
    public function list()
    {
        try {
            $courses = Course::all();
            return Response::success('All courses : ', CoursesResource::collection($courses));
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $this->courseService->store($request);
            return Response::success($data['message'], ShowCourseResource::make($data['course']));
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($course_id)
    {
        $course = $this->courseService->show($course_id);
        return Response::success('great', ShowCourseResource::make($course));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $course_id)
    {
        try {
            $data = $this->courseService->update($request, $course_id);
            return Response::success($data['message'], ShowCourseResource::make($data['course']));
        } catch (\Throwable $exception){
            return Response::error($exception->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($course_id)
    {
        try {
            $data = $this->courseService->destroy($course_id);
            return Response::success($data['messaage']);
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage(), 422);
        }
    }
}
