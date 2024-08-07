<?php

namespace App\Services;

use App\Models\CommentReport;
use App\Models\CourseReport;
use Illuminate\Support\Facades\Auth;

class ReportService
{
    private NotificationService $noticer;
    public function __construct(NotificationService $noticer)
    {
        $this->noticer = $noticer;
    }
    // for admins
    public function courseReports()
    {
        $reports = CourseReport::query()->get();
        if (count($reports) == 0) return ['message' => 'No Reports.', 'reports' => []];
        return ['message' => 'Reports : ', 'reports' => $reports];
    }

    public function commentReports()
    {
        $reports = CommentReport::query()->get();
        if (count($reports) == 0) return ['message' => 'No Reports.', 'reports' => []];
        return ['message' => 'Reports : ', 'reports' => $reports];
    }

    public function courseReportDetails($report_id){
        $report = CourseReport::find($report_id);
        return ['report details : ', 'report' => $report];
    }

    public function commentReportDetails($report_id){
        $report = CommentReport::find($report_id);
        return ['report details : ', 'report' => $report];
    }

    // for students
    public function courseReport($request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'content' => 'required|string',
        ]);
        CourseReport::create([
            'user_id' => Auth::id(),
            'course_id' => $request->course_id,
            'content' => $request->content,
        ]);

        // notify admins to see the report

        return ['message' => 'Report sent.'];
    }

    public function commentReport($request)
    {
        $request->validate([
            'comment_id' => 'required|exists:course_comments,id',
            'content' => 'required|string',
        ]);
        CommentReport::create([
            'user_id' => Auth::id(),
            'comment_id' => $request->comment_id,
            'content' => $request->content,
        ]);

        // notify admins to see the report

        return ['message' => 'Report sent.'];
    }

    // for Admins
    public function destroyCourseReport($report_id){
        CourseReport::find($report_id)->delete();
        return ['message' => 'Report deleted.'];
    }
    public function destroyCommentReport($report_id){
        CommentReport::find($report_id)->delete();
        return ['message' => 'Report deleted.'];
    }
}
