<?php

namespace App\Services\WorkShopServices;

use App\Models\User;
use App\Models\Workshop;
use App\Models\Workshop_enroll;
use App\Services\FileUploader;
use App\Services\NotificationService;
use Faker\Core\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\CommonMark\Extension\Attributes\Util\AttributesHelper;

class WorkShopService
{
    private NotificationService $noticer;
    private FileUploader $fileUploader;

    public function __construct(NotificationService $noticer, FileUploader $fileUploader)
    {
        $this->noticer = $noticer;
        $this->fileUploader = $fileUploader;
    }

    public function index()
    {
        $workshops = Workshop::all();
        if (count($workshops) == 0) {
            return ['message' => __('messages.no_workshops'), 'workshops' => []];
        }
        return ['message' => __('messages.workshops_list'), 'workshops' => $workshops];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function createWorkshop($request)
    {

        $workshop = Workshop::create([
            'title' => $request->title,
            'teacher_id' => Auth::id(),
            'category_id' => $request->category_id,
            'description' => $request->description,
            'image' => $this->fileUploader->storeFile($request, 'image'),
            'start_date' => date($request->start_date),
            'end_date' => date($request->end_date)
        ]);

        // notification for students

        return ['message' => __('messages.workshop_created_successfully'), 'workshop' => $workshop];
    }

    /**
     * Display the specified resource.
     */
    public function showWorkshopDetails($workshop_id)
    {
        $workshop = Workshop::find($workshop_id);
        return ['message' => __('messages.workshop_details'), 'workshop' => $workshop];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $workshop_id)
    {
        $workshop = Workshop::find($workshop_id);
        $attibutes = ['title', 'description', 'start_date', 'end_date', 'average_rating'];
        foreach ($attibutes as $a) {
            if (isset($request[$a])) $workshop[$a] = $request[$a];
        }
        $workshop->save();
        return ['message' => __('messages.workshop_updated_successfully'), 'workshop' => $workshop];
    }

    /**
     *   Remove the specified resource from storage.
     */
    public function destroy($workshop_id)
    {
        Workshop::find($workshop_id)->delete();
        return ['message' => __('messages.workshop_deleted_successfully')];
    }

    public function workshopEnroll($workshop_id)
    {
        if (!auth()->user()->isPremium()) throw new \Exception(__('messages.premium_account_required'), 422);
        Workshop_enroll::create([
            'user_id' => Auth::id(),
            'workshop_id' => $workshop_id
        ]);
        return ['message' => __('messages.enrollment_successful')];
    }

    public function getStudentsByPoints($workshop_id)
    {
        $workshop_enrolls = Workshop::find($workshop_id)->enrolls;
        if(!$workshop_enrolls) throw new \Exception(__('messages.no_workshop_enrollments'));
        $order = $workshop_enrolls->orderBy('points', 'DESC')
            ->take(min(count($workshop_enrolls), 10))
            ->get();
        return ['message' => __('messages.best_students'), 'order' => $order];
    }
}
