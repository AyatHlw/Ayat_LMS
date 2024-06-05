<?php

namespace App\Services\Course;

use App\Models\Course;
use App\Services\FileUploader;
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
            'title' => 'required|string',
            'description' => 'required',
            'cost' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'category_id' => 'required'
        ]);
        $course = Course::create([
            'title' => $request->title,
            'description' => $request->description,
            'creator_id' => Auth::id(),
            'category_id' => $request->category_id,
            'image' => (new FileUploader())->storeFile($request, 'image'),
            'cost' => $request->cost,
        ]);
        return ['message' => 'The course created successfully', 'course' => $course];
    }

    /**
     * Display the specified resource.
     */
    public function show($course_id)
    {
        return Course::firstWhere('id', $course_id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function do(Course $course, $request, $var)
    {
        if (isset($request[$var])) {
            $course[$var] = $request[$var];
            $course->save();
        }
    }

    public function update($request, $course_id)
    {
        $course = Course::firstWhere('id', $course_id);
        $this->do($course, $request, 'title');
        $this->do($course, $request, 'description');
        $this->do($course, $request, 'cost');
        $this->do($course, $request, 'average_rating');
        $this->do($course, $request, 'is_reviewed');
        // that's because image needs to be proccessed (updated) with bucket of operations
        if (isset($request['image'])) {
            $course['image'] = (new FileUploader())->storeFile($request, 'image');
            $course->save();
        }
        return ['message' => 'Course updated successfully.', 'course' => $course];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($course_id)
    {
        Course::firstWhere('id', $course_id)->delete();
        return ['message' => 'The course deleted successfully'];
    }

    public function courseReview($course_id, $reviewResult)
    {
        $course = Course::firstWhere('id', $course_id);
        if ($reviewResult) {
            $course->is_reviewed = 1;
            $course->save();
            // notification stuff for approval goes here..
            return ['message' => 'course approved successfully', 'course' => $course];
        } else {
            // notification for rejection
            return ['message' => 'course rejected.', 'course' => $course];
        }
    }
    public function getTopCourses()
    {
        $topRatedCourses = Course::orderBy('average_rating', 'DESC')->take(min(count(Course::all()), 10))->get();
        return ['message' => 'Top courses : ','courses' => $topRatedCourses];
    }
}
