<?php

namespace App\Http\Controllers\Api\Application;

use App\Enums\ApplicationReason;
use App\Enums\ApplicationStatus;
use App\Events\HandleSendMailApplication;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Application\ApplicationStoreRequest;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    /**
     * @var Application
     */
    protected Application $application;

    /**
     * @param Application $application
     */

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function store(ApplicationStoreRequest $request)
    {
        $reason = $request->input('reason');
        $dateRests = $request->input('date_rest', []);
        $des = $request->input('des');
        $userId = $request->input('user_id');
        $userFollows = $request->input('user_follows');
        $applicationType = $request->input('application_type' ,ApplicationReason::SICK_LEAVE);
        $nameFile = "";
        DB::beginTransaction();
        try {
            if ($request->hasFile('file'))
            {
                $file = $request->file('file');
                $nameFile = time().'-'.$file->getClientOriginalName();
                handleUploadFile($file ,Storage::path('public/files') ,$nameFile);
            }
            $data = [
                'code' => 'ONESIGN-'.rand(0, 99999),
                'status' => ApplicationStatus::PENDING,
                'name' => Auth::user()->name,
                'reason' => $reason,
                'application_type' => $applicationType,
                'position' => 'develop',
                'user_id' => $userId,
                'description' =>$des,
                'files' => !empty($nameFile)?$nameFile:'0',
                'user_application' => Auth::id()
            ];
            $application = $this->application::create($data);
            $application->dateTimeApplications()->createMany($dateRests);
            if (count($userFollows) > 0)
            {
                $application->users()->attach($userFollows);
            }
            DB::commit();
            event(new HandleSendMailApplication($application));
            return $application->load('user' ,'users');
        }catch (\Exception $exception)
        {
            DB::rollBack();
            throw  new  \Exception($exception->getMessage());
        }
    }
}
