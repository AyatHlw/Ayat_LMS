<?php

namespace App\Http\Controllers;

use App\Http\Responses\Response;
use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Http\Request;
use PHPUnit\Framework\Constraint\Count;

class ReportController extends Controller
{
    private ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function courseReports()
    {
        try {
            $data = $this->reportService->courseReports();
            return Response::success($data['message'], $data['reports']);
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage());
        }
    }

    public function commentReports()
    {
        try {
            $data = $this->reportService->commentReports();
            return Response::success($data['message'], $data['reports']);
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage());
        }
    }

    public function courseReport(Request $request)
    {
        try {
            $data = $this->reportService->courseReport($request);
            return Response::success($data['message']);
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage());
        }
    }

    public function commentReport(Request $request)
    {
        try {
            $data = $this->reportService->commentReport($request);
            return Response::success($data['message']);
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage());
        }
    }

    public function courseReportDetails($report_id)
    {
        try {
            $data = $this->reportService->courseReportDetails($report_id);
            return Response::success($data['message'], $data['report']);
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage());
        }
    }

    public function commentReportDetails($report_id)
    {
        try {
            $data = $this->reportService->commentReportDetails($report_id);
            return Response::success($data['message'], $data['report']);
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage());
        }
    }

    public function destroyCourseReport($report_id)
    {
        try {
            $data = $this->reportService->destroyCourseReport($report_id);
            return Response::success($data['message']);
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage());
        }
    }

    public function destroyCommentReport($report_id)
    {
        try {
            $data = $this->reportService->destroyCommentReport($report_id);
            return Response::success($data['message']);
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage());
        }
    }
}
