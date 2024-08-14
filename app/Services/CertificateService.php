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
                throw new \Exception(__('messages.user_not_found'));
            }
            $course = Course::find($courseId);
            if (!$course) {
                throw new \Exception(__('messages.not_found'));
            }
            $quiz = $course->quizzes->first();
            if (!$quiz) {
                throw new \Exception(__('messages.not_found'));
            }
            $result = DB::table('quiz_results')
                ->where('student_id', $user->id)
                ->where('quiz_id', $quiz->id)
                ->where('passed', true)
                ->first();
            if (!$result) {
                throw new \Exception(__('messages.must_pass'));
            }
            Mail::to($user->email)->send(new CertificateMail($user, $course));
            return ['message' => __('messages.must_pass')];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
