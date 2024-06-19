<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseResource;
use App\Http\Responses\Response;
use App\Models\Course;
use App\Services\Course\CourseService;
use Illuminate\Http\Request;

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
            $courses = Course::query()->where('is_reviewed', 1)->get();
            return Response::success('All courses : ', CourseResource::collection($courses));
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage(), 500);
        }
    }

    public function getTopCourses(Request $request)
    {
        try {
            $data = $this->courseService->getTopCourses();
            return Response::success($data['message'], CourseResource::make($data['courses']));
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
            return Response::success($data['message'], CourseResource::make($data['course']));
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
        return Response::success('great', CourseResource::make($course));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $course_id)
    {
        try {
            $data = $this->courseService->update($request, $course_id);
            return Response::success($data['message'], CourseResource::make($data['course']));
        } catch (\Throwable $exception) {
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

    public function courseReview($course_id, $reviewResult)
    {
        try {
            $data = $this->courseService->courseReview($course_id, $reviewResult);
            return Response::success($data['message'], $data['course']);
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage(), 500);
        }
    }
}
