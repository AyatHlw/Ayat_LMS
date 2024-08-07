<?php

namespace App\Services;
use App\Mail\CertificateMail;
use App\Models\Course;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
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
        $user = User::find(Auth::id());
        $course = Course::find($courseId);
        /*if(!$user->hasPassedQuiz($course->quiz->id)) {
            throw new \Exception('Sorry, You have to pass the quiz first');
        }*/
        Mail::to($user->email)->send(new CertificateMail($user, $course));
        return ['message' => 'Certificate sent successfully!'];
    }
}