<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureStudentIsEnrolled
{
    public function handle(Request $request, Closure $next)
    {
        $courseId = $request->route('course_id');
        $user = Auth::user();

        if (!$user->enrolledCourses()->where('course_id', $courseId)->exists()) {
            return response()->json(['message' => 'You are not enrolled in this course.'], 403);
        }

        return $next($request);
    }
}

