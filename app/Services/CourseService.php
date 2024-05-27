<?php

namespace App\Services;

use App\Http\Resources\ShowCourseResource;
use App\Http\Responses\Response;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseService
{
    public function index()
    {
        return Course::query()->where('is_reviewed', true)->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'cost' => 'required',
        ]);

        $course = Course::create([
            'title' => $request->title,
            'description' => $request->description,
            'creator_id' => Auth::id(),
            'image_course' => ' ',
            'cost' => $request->cost,
            'is_reviewed' => true
        ]);
        return ['message' => 'The course created successfully','course' => $course];
    }

    /**
     * Display the specified resource.
     */
    public function show($course_id)
    {
        return Course::query()->where('id', $course_id)->first();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        // Ayat also you can copy some details from the store section
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($course_id) {
        Course::query()->where('id', $course_id)->delete();
        return ['message' => 'The course deleted successfully'];
    }
}
