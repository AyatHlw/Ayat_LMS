<?php

namespace App\Services\Course;

use App\Mail\CertificateMail;
use App\Mail\CourseRejectedMail;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use App\Models\Video;
use App\Services\FileUploader;
use App\Services\NotificationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

class CourseService
{
    protected $fileUploader;
    private NotificationService $noticer;

    public function __construct(FileUploader $fileUploader, NotificationService $noticer)
    {
        $this->fileUploader = $fileUploader;
        $this->noticer = $noticer;
    }

    public function index()
    {
        return Course::query()->where('is_reviewed', true)->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function createCourse($request)
    {
        DB::beginTransaction();

        try {
            $image = $this->fileUploader->storeFile($request, 'image');
            $course = Course::create([
                'creator_id' => Auth::id(),
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

    public function createCourseWithYouTubeLinks($request)
    {
        DB::beginTransaction();

        try {
            // رفع الصورة
            $image = $this->fileUploader->storeFile($request, 'image');

            // إنشاء الكورس
            $course = Course::create([
                'creator_id' => Auth::id(),
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

    public function getTeacherCourses($teacher_id)
    {
        try {
            $teacher = User::with('courses')->find($teacher_id);

            if (!$teacher) {
                throw new \Exception('Teacher not found!', 404);
            }

            $courses = $teacher->courses;

            if ($courses->isEmpty()) {
                throw new \Exception('No courses found for this teacher.');
            }

            return [
                'message' => $teacher->name . ' courses:',
                'teacher' => $teacher,
                'courses' => $courses
            ];

        } catch (\Exception $e) {
            throw $e;
        }
    }


    public function showCourseDetails($course_id)
    {
        try {
            $data = Course::firstWhere('id', $course_id);
            if (is_null($data))
                throw new \Exception('this course not found');
            return $data;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update the specified resource in storage.
     */

    public function update($request, $course_id)
    {
        $course = Course::firstWhere('id', $course_id);
        $attibutes = ['title', 'description', 'cost', 'average_rating'];
        foreach ($attibutes as $a) {
            if (isset($request[$a])) $course[$a] = $request[$a];
        }
        if (isset($request['image'])) {
            $course['image'] = (new FileUploader())->storeFile($request, 'image');
            $course->save();
        }
        $course->save();
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

    public function getTopCourses()
    {
        $topRatedCourses = Course::orderBy('average_rating', 'DESC')->take(min(count(Course::all()), 10))->get();
        return ['message' => 'Top courses : ', 'courses' => $topRatedCourses];
    }

    public function createCategory($request)
    {
        DB::beginTransaction();

        try {
            $category = Category::create([
                'name' => $request['name'],
                'image' => $this->fileUploader->storeFile($request, 'image')
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

    public function updateCategory($request, $category_id)
    {
        $category = Category::find($category_id);
        if (isset($request['name'])) {
            $category['name'] = $request['name'];
        }
        if (isset($request['image'])) {
            $category['image'] = $this->fileUploader->storeFile($request, 'image');
        }
        $category->save();
        return ['message' => 'Category updated successfully', 'category' => $category];
    }

    public function destroyCategory($category_id)
    {
        Category::find($category_id)->delete();
        return ['message' => 'Category deleted successfully'];
    }

    public function getAllCoursesForAdmin()
    {
        return Course::query()->where('is_reviewed', false)->get();
    }

    public function approveCourse($courseId)
    {
        try {
            $course = Course::find($courseId);
            $course->is_reviewed = 1;
            $course->save();
            return $course;
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new \Exception('Course not found.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function rejectCourse($courseId)
    {
        DB::beginTransaction();

        try {
            $course = Course::with('creator')->findOrFail($courseId);
            Mail::to($course->creator->email)->send(new CourseRejectedMail($course));
            $course->delete();

            DB::commit();

            return true;
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new \Exception('Course not found.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function addToFavorites($request)
    {
        $request->validate(['course_id' => 'required|exists:courses,id']);
        if (!auth()->user()->hasFavorite($request['course_id'])) {
            auth()->user()->favoritesList()->attach($request['course_id']);
            return ['message' => 'Course added to favorites.'];
        }
        throw new \Exception('Course has already been In favorites', 200);
    }

    public function favorites()
    {
        $courses = auth()->user()->favoritesList()->latest()->get();
        return ['message' => 'Favorite courses : ', 'courses' => $courses];
    }

    public function removeFromFavorites($course_id)
    {
        auth()->user()->favoritesList()->detach($course_id);
        return ['message' => 'Course removed from favorites'];
    }

    public function addToWatchLater($request)
    {
        $request->validate(['video_id' => 'required|exists:videos,id']);
        if (!auth()->user()->hasInWatchLater($request['video_id'])) {
            auth()->user()->watchLaterList()->attach($request['video_id']);
            return ['message' => 'Video added to watch later.'];
        }
        throw new \Exception('Video has already been in watch later', 200);
    }

    public function watchLaterList()
    {
        $videos = auth()->user()->watchLaterList()->latest()->get();
        return ['message' => 'Watch later videos : ', 'videos' => $videos];
    }

    public function removeFromWatchLater($video_id)
    {
        auth()->user()->watchLaterList()->detach($video_id);
        return ['message' => 'Video removed from watch later'];
    }

    public function courseEnroll($request, $course_id){
        $request->validate([
            'courses' => 'required|array',
            'courses.*.course_id' => 'required|exists:courses,id'
        ]);
    }

}

