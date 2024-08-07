<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;
use App\Http\Responses\Response;
use App\Models\CourseComment;
use App\Services\Course\CommentService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    private CommentService $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CommentRequest $request)
    {
        try {
            $data = $this->commentService->store($request);
            return Response::success($data['message'], CommentResource::make($data['comment']));
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function showComments($course_id)
    {
        try {
            $data = $this->commentService->showComments($course_id);
            return Response::success($data['message'], CommentResource::collection($data['comments']));
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $comment_id)
    {
        try {
            $data = $this->commentService->update($request, $comment_id);
            return Response::success($data['message'], CommentResource::make($data['comment']));
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage(), 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($comment_id)
    {
        try {
            $data = $this->commentService->destroy($comment_id);
            return Response::success($data['message']);
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage(), 422);
        }
    }
}
