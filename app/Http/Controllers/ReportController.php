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

    public function reports()
    {
        try {
            $data = $this->reportService->reports();
            return Response::success($data['message'], $data['reports']);
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage());
        }
    }

    public function create(Request $request)
    {
        try {
            $data = $this->reportService->create($request);
            return Response::success($data['message']);
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage());
        }
    }

    public function destroy($report_id)
    {
        try {
            $data = $this->reportService->destroy($report_id);
            return Response::success($data['message']);
        } catch (\Throwable $exception) {
            return Response::error($exception->getMessage());
        }
    }
}
