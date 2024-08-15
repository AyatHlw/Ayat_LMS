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
        return ['message' => __('messages.comment_created'), 'comment' => $comment];
    }

    /**
     * Display the specified resource.
     */
    public function showComments($course_id)
    {
        $course = Course::find($course_id);
        if (!$course) throw new \Exception(__('messages.course_not_found'));
        $comments = $course->comments;
        if ($comments->isEmpty()) throw new \Exception(__('messages.no_comments'));
        return ['message' => __('messages.comment_retrieved'), 'comments' => $comments];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $comment_id)
    {
        $comment = CourseComment::find($comment_id);
        if (!$comment) throw new \Exception(__('messages.comment_not_found'));

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
        return ['message' => __('messages.comment_updated'), 'comment' => $comment];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($comment_id)
    {
        $comment = CourseComment::find($comment_id);
        if (!$comment) throw new \Exception(__('messages.comment_not_found'));

        // update rating
        $course = $comment->course;
        $comment->delete();
        $comments = $course->comments;
        if ($comments) {
            $course['average_rating'] = $comments->sum('rating') / count($comments);
        } else {
            $course['average_rating'] = 0.0;
        }
        $course->save();
        return ['message' => __('messages.comment_deleted')];
    }
}
