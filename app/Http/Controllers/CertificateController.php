<?php

namespace App\Http\Controllers;

use App\Http\Responses\Response;
use App\Services\CertificateService;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    private CertificateService $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    public function getCertificate($course_id){
        try {
            $data = $this->certificateService->getCertificate($course_id);
            return Response::success($data['message']);
        } catch (\Throwable $e){
            return Response::error($e->getMessage(), 403);
        }
    }
}
