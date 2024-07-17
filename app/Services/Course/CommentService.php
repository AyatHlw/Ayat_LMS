<?php

namespace App\Services\Course;

use App\Http\Controllers\AuthController;
use App\Models\Course;
use App\Models\CourseComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use mysql_xdevapi\Exception;
use PharIo\Manifest\ElementCollectionException;
use function PHPUnit\Framework\isEmpty;

class CommentService
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'course_id' => 'required|exists:courses,id',
            'rating' => 'required'
        ]);
        $comment = CourseComment::create([
            'user_id' => Auth::id(),
            'course_id' => $request['course_id'],
            'content' => $request['content'],
            'rating' => $request['rating']
        ]);
        $course = $comment->course;
        $course['average_rating'] = ($course['average_rating'] + $comment['rating']) / 2.0;
        $course->save();
        return ['message' => 'Comment added successfully', 'comment' => $comment];
    }

    /**
     * Display the specified resource.
     */
    public function showComments($course_id)
    {
        $comments = Course::firstWhere('id', $course_id);
        $comments = $comments->comments;
        if (count($comments) == 0 ) throw new \Exception('No comments yet');
        return ['message' => 'Comments : ', 'comments' => $comments];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $comment_id)
    {
        $comment = CourseComment::firstWhere('id', $comment_id);
        if (isset($request['content'])) {
            $comment->content = $request['content'];
            $comment->save();
        }
        if (isset($request['rating'])) {
            $course = $comment->course;
            if(count($course->comments) == 1) $course['average_rating'] = 0;
            else $course['average_rating'] -= $comment['rating'] / (count($course->comments) - 1); // not correct yet but we need this logic right here .
            $course['average_rating'] = ($course['average_rating'] + $request['rating']) / 2.0;
            $comment->rating = $request['rating'];
            $comment->save();
        }
        return ['message' => 'Comment updated successfully', 'comment' => $comment];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($comment_id)
    {
        $comment = CourseComment::firstWhere('id', $comment_id);
        if($comment->user_id != Auth::id()) Throw new \Exception('You can\'t delete this comment!');
        CourseComment::firstWhere('id', $comment_id)->delete();
        return ['message' => 'Comment deleted successfully'];
    }
}
