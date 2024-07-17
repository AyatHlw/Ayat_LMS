<?php

namespace App\Services;

use App\Models\Report;

class ReportService
{
    // for admins
    public function reports()
    {
        $reports = Report::query()->get();
        if (count($reports) == 0) return ['message' => 'No Reports.', 'reports' => []];
        return ['message' => 'Reports : ', 'reports' => $reports];
    }
    // for students
    public function create($request)
    {
        $request->validate([
            'content' => 'required|string'
            // something_id
        ]);
        Report::create([
            'content' => $request->content,
            // something_id
        ]);

        // notify admins to see the report

        return ['message' => 'Report sent.'];
    }
    // for Admins
    public function destroy($report_id){
        Report::query()->where('id', $report_id)->delete();
        return ['message' => 'Report deleted.'];
    }
}
