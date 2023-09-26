<?php

namespace App\Http\Controllers\Api\Application;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Application\ApplicationStoreRequest;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function store(ApplicationStoreRequest $request)
    {
        $reason = $request->input('reason');
        $dateRests = $request->input('date_rest', []);
        $des = $request->input('des');
        $userId = $request->input('userId');
        $userFollows = $request->input('user_follows');

        $application = Application::create([
            'code' => 'ONESIGN-'.rand(0, 99999),
            'status' => ApplicationStatus::PENDING,
            'name' => Auth::user()->name,
        ]);
    }
}
