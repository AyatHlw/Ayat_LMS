<?php

namespace App\Services\Course;

use App\Http\Controllers\AuthController;
use App\Models\Course;
use App\Models\CourseComment;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use mysql_xdevapi\Exception;
use PharIo\Manifest\ElementCollectionException;
use function PHPUnit\Framework\isEmpty;

class CommentService
{
    private NotificationService $noticer;
    public function __construct(NotificationService $noticer)
    {
        $this->noticer = $noticer;
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store($request)
    {
        $comment = CourseComment::create([
            'user_id' => Auth::id(),
            'course_id' => $request['course_id'],
            'content' => $request['content'],
            'rating' => $request['rating']
        ]);
        $course = Course::find($request->course_id);
        $comments = $course->comments;
        $course['average_rating'] = $comments->sum('rating') / count($comments);
        $course->save();
        return ['message' => 'Comment added successfully', 'comment' => $comment];
    }

    /**
     * Display the specified resource.
     */
    public function showComments($course_id)
    {
        $course = Course::find($course_id);
        $comments = $course->comments;
        if (count($comments) == 0) throw new \Exception('No comments yet');
        return ['message' => 'Comments : ', 'comments' => $comments];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $comment_id)
    {
        $comment = CourseComment::find($comment_id);
        if (isset($request['content'])) {
            $comment->content = $request['content'];
            $comment->save();
        }
        if (isset($request['rating'])) {
            $comment->rating = $request['rating'];
            $course = $comment->course;
            $comments = $course->comments;
            $course['average_rating'] = $comments->sum('rating') / count($comments);
            $course->save();
            $comment->save();
        }
        return ['message' => 'Comment updated successfully', 'comment' => $comment];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($comment_id)
    {
        $comment = CourseComment::find($comment_id);
        if($comment->user_id != Auth::id()) Throw new \Exception('You can\'t delete this comment!');
        CourseComment::find($comment_id)->delete();
        return ['message' => 'Comment deleted successfully'];
    }
}
