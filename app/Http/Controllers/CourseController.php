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
        return Response::success('All courses : ', CoursesResource::collection(Course::all()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $course = $this->courseService->store($request);
            return Response::success('The course created successfully', ShowCourseResource::make($course));
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
    public function update(Request $request, Course $course)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($course_id)
    {
        try {
            $data = $this->courseService->destroy($course_id);
            return Response()->json([
                'message' => $data['messaage'],
            ], 200);
        } catch (\Throwable $exception){
            return Response::error($exception->getMessage(), 422);
        }
    }
}
