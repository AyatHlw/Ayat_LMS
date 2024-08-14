<?php

namespace App\Services;

use App\Models\CommentReport;
use App\Models\CourseReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
        if ($reports->isEmpty()) {
            return ['message' => __('messages.no_reports'), 'reports' => []];
        }
        return ['message' => __('messages.reports_found'), 'reports' => $reports];
    }

    public function commentReports()
    {
        $reports = CommentReport::query()->get();
        if ($reports->isEmpty()) {
            return ['message' => __('messages.no_reports'), 'reports' => []];
        }
        return ['message' => __('messages.reports_found'), 'reports' => $reports];
    }

    public function courseReportDetails($report_id)
    {
        $report = CourseReport::find($report_id);
        if (!$report) throw new \Exception(__('messages.report_not_found'));
        return ['message' => __('messages.report_details'), 'report' => $report];
    }

    public function commentReportDetails($report_id)
    {
        $report = CommentReport::find($report_id);
        if (!$report) throw new \Exception(__('messages.report_not_found'));
        return ['message' => __('messages.report_details'), 'report' => $report];
    }

    // for students
    public function courseReport($request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new \Exception(__('messages.course_report_error'));
        }

        CourseReport::create([
            'user_id' => Auth::id(),
            'course_id' => $request->course_id,
            'content' => $request->content,
        ]);

        // notify admins to see the report

        return ['message' => __('messages.report_sent')];
    }

    public function commentReport($request)
    {
        $validator = Validator::make($request->all(), [
            'comment_id' => 'required|exists:course_comments,id',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new \Exception(__('messages.comment_report_error'));
        }

        CommentReport::create([
            'user_id' => Auth::id(),
            'comment_id' => $request->comment_id,
            'content' => $request->content,
        ]);

        // notify admins to see the report

        return ['message' => __('messages.report_sent')];
    }

    // for Admins
    public function destroyCourseReport($report_id)
    {
        $report = CourseReport::find($report_id);
        if (!$report) throw new \Exception(__('messages.report_not_found'));
        return ['message' => __('messages.report_deleted')];
    }

    public function destroyCommentReport($report_id)
    {
        $report = CommentReport::find($report_id);
        if (!$report) throw new \Exception(__('messages.report_not_found'));
        return ['message' => __('messages.report_deleted')];
    }
}
