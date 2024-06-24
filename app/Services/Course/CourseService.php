<?php

namespace App\Services\Course;

use App\Models\Category;
use App\Models\Course;
use App\Models\Video;
use App\Services\FileUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseService
{
    protected $fileUploader;


    public function __construct(FileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }

    public function index()
    {
        return Course::query()->where('is_reviewed', true)->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function createCourse(Request $request)
    {
        DB::beginTransaction();

        try {
            $image = $this->fileUploader->storeFile($request, 'image');
            $course = Course::create([
                'creator_id' => $request->input('creator_id'),
                'category_id' => $request->input('category_id'),
                'title' => $request->input('title'),
                'image' => $image,
                'description' => $request->input('description'),
                'cost' => $request->input('cost'),
                'average_rating' => 0,
                'is_reviewed' => false
            ]);
            foreach ($request->input('videos') as $videoData) {
                $videoRequest = new Request($videoData);
                $videoPath = $this->fileUploader->storeFile($videoRequest, 'path');

                Video::create([
                    'course_id' => $course->id,
                    'title' => $videoRequest->input('title'),
                    'path' => $videoPath
                ]);
            }

            DB::commit();

            return [
                'message' => 'Course created successfully',
                'course' => $course
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function createCourseWithYouTubeLinks(Request $request)
    {
        DB::beginTransaction();

        try {
            // رفع الصورة
            $image = $this->fileUploader->storeFile($request, 'image');

            // إنشاء الكورس
            $course = Course::create([
                'creator_id' => $request->input('creator_id'),
                'category_id' => $request->input('category_id'),
                'title' => $request->input('title'),
                'image' => $image,
                'description' => $request->input('description'),
                'cost' => $request->input('cost'),
                'average_rating' => 0,
                'is_reviewed' => false
            ]);

            // إنشاء الفيديوهات من روابط يوتيوب
            foreach ($request->input('videos') as $videoLink) {
                $videoId = $this->extractYouTubeId($videoLink);
                if ($videoId) {
                    $videoTitle = $this->getYouTubeVideoTitle($videoLink);

                    Video::create([
                        'course_id' => $course->id,
                        'title' => $videoTitle,
                        'path' => $videoLink
                    ]);
                }
            }

            DB::commit();

            return [
                'message' => 'Course created successfully with YouTube videos',
                'course' => $course
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function extractYouTubeId($url)
    {
        preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:watch\?v=|embed\/|v\/|e\/|.+\?v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches);
        return $matches[1] ?? null;
    }

    private function getYouTubeVideoTitle($url)
    {
        try {
            $command = "yt-dlp --get-title --encoding UTF-8 " . escapeshellarg($url);
            $output = shell_exec($command);

            if (empty($output)) {
                throw new \Exception('Empty response from yt-dlp');
            }

            return trim($output);
        } catch (\Exception $e) {
            Log::error('Failed to fetch YouTube video title: ' . $e->getMessage());
            return null;
        }
    }




    /**
     * Display the specified resource.
     */
    public function showCourseDetails($course_id)
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



    public function createCategory($request)
    {
        DB::beginTransaction();

        try {
            $category = Category::create([
                'name' => $request['name']
            ]);

            DB::commit();

            return [
                'message' => 'Category created successfully',
                'category' => $category
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
