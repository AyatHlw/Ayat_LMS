<?php

namespace App\Services;
use App\Mail\CertificateMail;
use App\Models\Course;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CertificateService
{
    private NotificationService $noticer;
    public function __construct(NotificationService $noticer)
    {
        $this->noticer = $noticer;
    }
    public function getCertificate($courseId)
    {
        try {
            $user = User::find(Auth::id());
            if (!$user) {
                throw new \Exception('User not found');
            }
            $course = Course::find($courseId);
            if (!$course) {
                throw new \Exception('Course not found');
            }
            $quiz = $course->quizzes->first();
            if (!$quiz) {
                throw new \Exception('Quiz not found for this course');
            }
            $result = DB::table('quiz_results')
                ->where('student_id', $user->id)
                ->where('quiz_id', $quiz->id)
                ->where('passed', true)
                ->first();
            if (!$result) {
                throw new \Exception('Sorry, You have to pass the quiz first');
            }
            Mail::to($user->email)->send(new CertificateMail($user, $course));
            return ['message' => 'Certificate sent successfully!'];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
