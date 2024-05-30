<?php

namespace App\Services\Course;

use App\Http\Controllers\AuthController;
use App\Models\Course;
use App\Models\CourseComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use mysql_xdevapi\Exception;
use function PHPUnit\Framework\isEmpty;

class CommentService
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string'
        ]);
        $comment = CourseComment::create([
            'content' => $request['content'],
            'course_id' => $request['course_id'],
            'user_id' => Auth::id()
        ]);
        return ['message' => 'Comment added successfully', 'comment' => $comment];
    }

    /**
     * Display the specified resource.
     */
    public function showComments($course_id)
    {
        $comments = Course::firstWhere('id', $course_id)->comments;
        if (isEmpty($comments)) throw new Exception('No comments yet');
        return ['message' => 'Comments : ', 'comments' => $comments];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $commentCourse_id)
    {
        $comment = CourseComment::firstWhere('id', $commentCourse_id);
        $comment->content = $request['content'];
        $comment->save();
        return ['message' => 'Comment updated successfully', 'comment' => $comment];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($commentCourse_id)
    {
        CourseComment::firstWhere('id', $commentCourse_id)->delete();
        return ['message' => 'Comment deleted successfully'];
    }
}
