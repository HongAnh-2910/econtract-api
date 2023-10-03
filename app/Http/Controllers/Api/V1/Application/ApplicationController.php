<?php

    namespace App\Http\Controllers\Api\V1\Application;

    use App\Enums\ApplicationReason;
    use App\Enums\ApplicationStatus;
    use App\Events\HandleSendMailApplication;
    use App\Http\Controllers\Controller;
    use App\Http\Requests\V1\Application\ApplicationStoreRequest;
    use App\Http\Requests\V1\Application\StoreProposalApplicationRequest;
    use App\Http\Requests\V1\Application\UpdateStateApplicationRequest;
    use App\Http\Resources\V1\Application\ApplicationResource;
    use App\Models\Application;
    use App\Models\File;
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
         * @param  Application  $application
         */

        /**
         * @var File
         */

        protected File $file;

        public function __construct(Application $application , File $file)
        {
            $this->application = $application;
            $this->file = $file;
        }


        /**
         * @param  ApplicationStoreRequest  $request
         * @return ApplicationResource
         * @throws \Exception
         */

        public function store(ApplicationStoreRequest $request)
        {
            $reason          = $request->input('reason');
            $dateRests       = $request->input('date_rest', []);
            $des             = $request->input('des');
            $userId          = $request->input('user_id');
            $userFollows     = $request->input('user_follows');
            $applicationType = $request->input('application_type', ApplicationReason::SICK_LEAVE);
            DB::beginTransaction();
            $fileIds = [];
            $nameFiles = [];
            try {
                if ($request->hasFile('files')) {
                    foreach ($request->file('files') as $file)
                    {
                        $name = time().'-'.$file->getClientOriginalName();
                        $nameFiles[] = $name;
                        $storage = floor((int) $file->getSize() / 1024);
                        $extension = $file->getClientOriginalExtension();
                        handleUploadFile($file ,Storage::path('public/files') ,$name);
                        $fileInstance =$this->file->create([
                            'name' => $name,
                            'path' => $name,
                            'type' => $extension,
                            'user_id' => Auth::id(),
                            'folder_id' => null,
                            'size' => $storage,
                            'upload_st' => 'upload_applications'
                        ]);
                        $fileIds[] = $fileInstance->id;
                    }
                }
                $data = $this->bodyCreateApplication('ONESIGN-'.rand(0, 99999), ApplicationStatus::PENDING,
                    Auth::user()->name, $reason, $applicationType, 'develop', $userId, $des, Auth::id(),
                    ApplicationStatus::CREATE_APPLICATION);
                $application = $this->application::create($data);
                $application->files()->syncWithoutDetaching($fileIds);
                $application->dateTimeApplications()->createMany($dateRests);
                if (count($userFollows) > 0) {
                    $application->users()->attach($userFollows);
                }
                DB::commit();
                event(new HandleSendMailApplication($application));
                return new ApplicationResource($application->load('user', 'users', 'userCreateApplication',
                    'dateTimeApplications' , 'applicationFiles'));
            } catch (\Exception $exception) {
                DB::rollBack();
                foreach ($nameFiles as $nameFile)
                {
                    handleRemoveFile(config('pathUploadFile.path_file'), $nameFile);
                }
                throw new \Exception($exception->getMessage());
            }
        }

        /**
         * @param  UpdateStateApplicationRequest  $request
         * @param  Application  $application
         * @return ApplicationResource
         * @throws \Exception
         */

        public function updateState(UpdateStateApplicationRequest $request, Application $application)
        {
            $status = $request->input('status');
            try {
                $application = $application->status->transitionTo($status);
                return new ApplicationResource($application->load('user', 'users', 'userCreateApplication',
                    'dateTimeApplications' ,'applicationFiles'));
            } catch (\Exception $exception) {
                throw new \Exception($exception->getMessage());
            }
        }

        public function storeProposal(StoreProposalApplicationRequest $request)
        {
            $body = $request->validated();

            $data = $this->bodyCreateApplication('ONESIGN-'.rand(0, 99999), ApplicationStatus::PENDING,
                Auth::user()->name, '', $body['application_type'], 'develop', $body['user_id'], '', Auth::id(),
                ApplicationStatus::CREATE_SUGGESTION);
            $data['proposal_name'] = $body['proposal_name'];
            $data['proponent'] = $body['proponent'];
            $data['price_proposal'] = $body['price_proposal'];
            $data['account_information'] = $body['account_information'];
            $data['delivery_time'] = $body['delivery_time'];
            $data['delivery_date'] = $body['delivery_date'];
            $fileIds = [];
            $nameFiles = [];
            DB::beginTransaction();
            try {
                if ($request->hasFile('files')) {
                    foreach ($request->file('files') as $file)
                    {
                        $name = time().'-'.$file->getClientOriginalName();
                        $nameFiles[] = $name;
                        $storage = floor((int) $file->getSize() / 1024);
                        $extension = $file->getClientOriginalExtension();
                        handleUploadFile($file ,Storage::path('public/files') ,$name);
                        $fileInstance =$this->file->create([
                            'name' => $name,
                            'path' => $name,
                            'type' => $extension,
                            'user_id' => Auth::id(),
                            'folder_id' => null,
                            'size' => $storage,
                            'upload_st' => 'upload_applications'
                        ]);
                        $fileIds[] = $fileInstance->id;
                    }
                }
                $application = $this->application::create($data);

                DB::commit();
            }catch (\Exception $exception)
            {
                DB::rollBack();
            }



        }

        /**
         * @param  string  $code
         * @param  float  $status
         * @param  string  $name
         * @param  string  $reason
         * @param  float  $applicationType
         * @param  string  $position
         * @param  float  $userId
         * @param  string  $description
         * @param  string  $file
         * @param  float  $userApplication
         * @param  float  $type
         * @return array
         */

        private function bodyCreateApplication(string $code,
                                               float  $status,
                                               string $name,
                                               string $reason,
                                               float  $applicationType,
                                               string $position,
                                               float  $userId,
                                               string $description,
                                               float  $userApplication,
                                               float  $type)
        {
            return [
                'code'             => $code,
                'status'           => $status,
                'name'             => $name,
                'reason'           => $reason,
                'application_type' => $applicationType,
                'position'         => $position,
                'user_id'          => $userId,
                'description'      => $description,
                'files'            =>  '0',
                'user_application' => $userApplication,
                'type'             => $type
            ];
        }
    }
