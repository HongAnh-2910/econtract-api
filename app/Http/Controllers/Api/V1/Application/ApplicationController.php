<?php

    namespace App\Http\Controllers\Api\V1\Application;

    use App\Enums\ApplicationReason;
    use App\Enums\ApplicationStatus;
    use App\Events\HandleSendMailApplication;
    use App\Http\Controllers\Controller;
    use App\Http\Requests\V1\Application\ApplicationStoreRequest;
    use App\Http\Requests\V1\Application\UpdateStateApplicationRequest;
    use App\Http\Resources\V1\Application\ApplicationResource;
    use App\Http\States\Application\Success;
    use App\Models\Application;
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


        /**
         * @param ApplicationStoreRequest $request
         * @return ApplicationResource
         * @throws \Exception
         */

        public function store(ApplicationStoreRequest $request)
        {
            $reason = $request->input('reason');
            $dateRests = $request->input('date_rest', []);
            $des = $request->input('des');
            $userId = $request->input('user_id');
            $userFollows = $request->input('user_follows');
            $applicationType = $request->input('application_type', ApplicationReason::SICK_LEAVE);
            $nameFile = "";
            DB::beginTransaction();
            try {
                if ($request->hasFile('file')) {
                    $file = $request->file('file');
                    $nameFile = time() . '-' . $file->getClientOriginalName();
                    handleUploadFile($file, Storage::path('public/files'), $nameFile);
                }
                $data = [
                    'code' => 'ONESIGN-' . rand(0, 99999),
                    'status' => ApplicationStatus::PENDING,
                    'name' => Auth::user()->name,
                    'reason' => $reason,
                    'application_type' => $applicationType,
                    'position' => 'develop',
                    'user_id' => $userId,
                    'description' => $des,
                    'files' => !empty($nameFile) ? $nameFile : '0',
                    'user_application' => Auth::id(),
                    'type' => ApplicationStatus::CREATE_APPLICATION
                ];
                $application = $this->application::create($data);
                $application->dateTimeApplications()->createMany($dateRests);
                if (count($userFollows) > 0) {
                    $application->users()->attach($userFollows);
                }
                DB::commit();
                event(new HandleSendMailApplication($application));
                return new ApplicationResource($application->load('user', 'users' ,'userCreateApplication' ,'dateTimeApplications'));
            } catch (\Exception $exception) {
                DB::rollBack();
                throw new \Exception($exception->getMessage());
            }
        }

        public function updateState(UpdateStateApplicationRequest $request , Application $application)
        {
            return $application->status->transitionTo(Success::class);
        }
    }
