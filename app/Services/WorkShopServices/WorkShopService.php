<?php

namespace App\Services\WorkShopServices;

use App\Models\User;
use App\Models\Workshop;
use App\Models\Workshop_enroll;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\CommonMark\Extension\Attributes\Util\AttributesHelper;

class WorkShopService
{
    private NotificationService $noticer;
    public function __construct(NotificationService $noticer)
    {
        $this->noticer = $noticer;
    }
    public function index()
    {
        $workshops = Workshop::all();
        if (count($workshops) == 0) return ['message' => 'There are no workshops yet!', 'workshops' => []];
        return ['message' => 'Workshops : ', 'workshops' => $workshops];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function createWorkshop(Request $request)
    {
        $workshop = Workshop::create([
            'title' => $request->title,
            'teacher_id' => Auth::id(),
            'category_id' => $request->category_id,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);

        // notification for students

        return ['message' => 'Workshop created successfully.', 'workshop' => $workshop];
    }

    /**
     * Display the specified resource.
     */
    public function showWorkshopDetails($workshop_id)
    {
        $workshop = Workshop::find($workshop_id);
        return ['message' => 'workshop : ', 'workshop' => $workshop];
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
        return ['message' => 'Workshop updated successfully.', 'workshop' => $workshop];
    }

    /**
     *   Remove the specified resource from storage.
     */
    public function destroy($workshop_id)
    {
        Workshop::find($workshop_id)->delete();
        return ['message' => 'Workshop deleted successfully.'];
    }

    public function workshopEnroll($workshop_id)
    {
        if (!auth()->user()->isPremium()) throw new \Exception('You don\'t have a premium account to enroll, please subscribe to unlock the workshops features.', 422);
        Workshop_enroll::create([
            'user_id' => Auth::id(),
            'workshop_id' => $workshop_id
        ]);
        return ['message' => 'You\'ve enrolled successfully.'];
    }

    public function getStudentsByPoints($workshop_id)
    {
        $workshop_enrolls = Workshop::find($workshop_id)->enrolls;
        $order = $workshop_enrolls->orderBy('points', 'DESC')
            ->take(min(count($workshop_enrolls), 10))
            ->get();
        return ['message' => 'Best students : ', 'order' => $order];
    }
}
