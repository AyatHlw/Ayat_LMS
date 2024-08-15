<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\NotificationService;
use GPBMetadata\Google\Api\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    private NotificationService $noticer;
    public function __construct(NotificationService $noticer)
    {
        $this->noticer = $noticer;
    }
    public function Nothing()
    {
        return "ayat";
    }

    public function registerToken(Request $request){
        $user = User::query()->find(1);
        $user['fcm_token'] = $request->token;
        $user->save();
    }
    public function notice(){
        $user = User::find(1);
        $user['fcm_token'] =
        $this->noticer->send($user, 'finding', 'You are sweet', '\Notice');
    }
}
