<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

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
    public function notice(){
        $user = User::find(1);

    }
}
