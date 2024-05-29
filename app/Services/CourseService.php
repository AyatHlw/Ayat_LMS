<?php

namespace App\Services;

use App\Http\Resources\ShowCourseResource;
use App\Http\Responses\Response;
use App\Models\Course;
use Faker\Core\File;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequestsWithRedis;
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
        return ['message' => 'The course created successfully', 'course' => $course];
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
    public function do(Course $course, $request, $var)
    {
        if (isset($request[$var])) {
            $course[$var] = $request[$var];
            $course->save();
            return 1;
        }
        return 0;
    }

    public function update($request, $course_id)
    {
        $count = 0; // just wondering if something has been changed or we've got the request عالفاضي
        $course = Course::firstWhere('id', $course_id);
        $count += $this->do($course, $request, 'title');
        $count += $this->do($course, $request, 'description');
        // that's because image needs to be proccessed (updated) with bucket of operations
        if (isset($request['image'])) {
            $course['image_course'] = (new FileUploader())->storeFile($request, 'image');
            $course->save();
            $count++;
        }
        $count += $this->do($course, $request, 'cost');
        $count += $this->do($course, $request, 'average_rating');
        $count += $this->do($course, $request, 'is_reviewed');
        if (!$count) throw new \Exception('Nothing to update', 200);
        return ['message' => 'Course updated successfully.', 'course' => $course];
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($course_id)
    {
        Course::query()->where('id', $course_id)->delete();
        return ['message' => 'The course deleted successfully'];
    }
}
