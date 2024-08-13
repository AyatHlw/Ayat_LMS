<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseResource;
use App\Http\Responses\Response;
use App\Services\Course\CourseService;
use App\Services\QuizService;
use Illuminate\Http\Request;
use Throwable;

class CourseWithStudentController extends Controller
{
    private CourseService $courseService;
    private QuizService $quizService;

    public function __construct(CourseService $courseService,QuizService $quizService)
    {
        $this->courseService = $courseService;
        $this->quizService = $quizService;
    }

    public function addToFavorites(Request $request)
    {
        try {
            $data = $this->courseService->addToFavorites($request);
            return Response::success($data['message']);
        } catch (Throwable $exception){
            return Response::error($exception->getMessage());
        }
    }

    public function favoritesList()
    {
        try {
            $data = $this->courseService->favorites();
            return Response::success($data['message'], CourseResource::collection($data['courses']));
        } catch (Throwable $exception){
            return Response::error($exception->getMessage(), $exception->getCode());
        }
    }

    public function removeFromFavorites($course_id)
    {
        try {
            $data = $this->courseService->removeFromFavorites($course_id);
            return Response::success($data['message']);
        } catch (Throwable $exception){
            return Response::error($exception->getMessage());
        }
    }

    public function addToWatchLater(Request $request)
    {
        try {
            $data = $this->courseService->addToWatchLater($request);
            return Response::success($data['message']);
        } catch (Throwable $exception){
            return Response::error($exception->getMessage(), $exception->getCode());
        }
    }

    public function watchLaterList()
    {
        try {
            $data = $this->courseService->watchLaterList();
            return Response::success($data['message'], $data['videos']);
        } catch (Throwable $exception){
            return Response::error($exception->getMessage(), $exception->getCode());
        }
    }

    public function removeFromWatchLater($video_id)
    {
        try {
            $data = $this->courseService->removeFromWatchLater($video_id);
            return Response::success($data['message']);
        } catch (Throwable $exception){
            return Response::error($exception->getMessage());
        }
    }

    public function courseEnroll($course_id)
    {
        try {
            $res = $this->courseService->courseEnroll($course_id);
            return Response::success($res['message']);
        }catch (Throwable $exception){
            return Response::error($exception->getMessage(), $exception->getCode());
        }
    }
}
