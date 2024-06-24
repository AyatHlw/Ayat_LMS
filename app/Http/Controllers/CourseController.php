<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\CreateCourseRequest;
use App\Http\Requests\CreateCourseYoutubeRequest;
use App\Http\Resources\CourseResource;
use App\Http\Resources\QuizResource;
use App\Http\Responses\Response;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\User;
use App\Services\Course\CourseService;
use App\Services\QuizService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class CourseController extends Controller
{
    private CourseService $courseService;
    private QuizService $quizService;

    public function __construct(CourseService $courseService,QuizService $quizService)
    {
        $this->courseService = $courseService;
        $this->quizService = $quizService;
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
    public function createCourse(CreateCourseRequest $request)
    {
        try {
            $data = $this->courseService->createCourse($request);
            return response()->json([
                'message' => $data['message'],
                'course' => $data['course']
            ], 201);
        } catch (Throwable $throwable) {
            return response()->json([
                'message' => $throwable->getMessage()
            ], 500);
        }
    }

    public function createCourseWithYouTubeLinks(CreateCourseYoutubeRequest $request): JsonResponse
    {
        try {
            $data = $this->courseService->createCourseWithYouTubeLinks($request);
            return response()->json([
                'message' => $data['message'],
                'course' => $data['course']
            ], 201);
        } catch (Throwable $throwable) {
            return response()->json([
                'message' => $throwable->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function showCourseDetails($course_id)
    {
        $course = $this->courseService->showCourseDetails($course_id);
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

    public function createCategory(CreateCategoryRequest $request): JsonResponse
    {
        try {
            $data = $this->courseService->createCategory($request->validated());
            return response()->json([
                'message' => $data['message'],
                'category' => $data['category']
            ], 201);
        } catch (Throwable $throwable) {
            return response()->json([
                'message' => $throwable->getMessage()
            ], 500);
        }
    }

    public function createQuiz(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'questions' => 'required|array',
            'questions.*.question_text' => 'required|string',
            'questions.*.answers' => 'required|array|min:2',
            'questions.*.answers.*.answer_text' => 'required|string',
            'questions.*.answers.*.is_correct' => 'required|boolean',
        ]);

        try {
            $data = $this->quizService->createQuiz($request);
            return response()->json([
                'message' => $data['message'],
                'quiz' => $data['quiz']
            ], 201);
        } catch (\Throwable $throwable) {
            return response()->json([
                'message' => $throwable->getMessage()
            ], 500);
        }
    }

    public function showQuizForTeachers($quizId)
    {
        try {
            $quiz = $this->quizService->showQuizForTeachers($quizId);
            return response()->json([
                'message' => 'Quiz retrieved successfully',
                'quiz' => new QuizResource($quiz)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e instanceof ModelNotFoundException ? 404 : 500);
        }
    }

    public function showQuizForStudents($quizId)
    {
        try {
            $quiz = $this->quizService->showQuizForStudents($quizId);
            return response()->json([
                'message' => 'Here your quiz, good luck',
                'quiz' => new QuizResource($quiz)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function checkAnswers(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'quiz_id' => 'required|exists:quizzes,id',
            'answers' => 'required|array',
            'answers.*' => 'required|exists:answers,id',
        ]);

        $student = User::find($request->input('student_id'));

        if ($student->hasPassedQuiz($request->input('quiz_id'))) {
            return response()->json([
                'message' => 'Student has already passed this quiz.'
            ], 400);
        }

        try {
            $data = $this->quizService->checkAnswers($request);
            return response()->json([
                'message' => $data['message'],
                'correct_answers' => $data['correct_answers'],
                'total_questions' => $data['total_questions'],
                'passed' => $data['passed']
            ], 200);
        } catch (\Throwable $throwable) {
            return response()->json([
                'message' => $throwable->getMessage()
            ], 500);
        }
    }

    public function updateQuiz(Request $request, $quizId)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'questions' => 'sometimes|array',
            'questions.*.id' => 'sometimes|integer|exists:questions,id',
            'questions.*.question_text' => 'required_with:questions|string',
            'questions.*.answers' => 'sometimes|array',
            'questions.*.answers.*.id' => 'sometimes|integer|exists:answers,id',
            'questions.*.answers.*.answer_text' => 'required_with:questions.*.answers|string',
            'questions.*.answers.*.is_correct' => 'sometimes|boolean'
        ]);

        try {
            $quiz = $this->quizService->updateQuiz($quizId, $validated);
            return response()->json([
                'message' => 'Quiz updated successfully',
                'quiz' => $quiz
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteQuestion($questionId)
    {
        try {
            $result = $this->quizService->deleteQuestion($questionId);
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
