<?php

    namespace App\Http\Controllers\Api\V1\Application;

    use App\Enums\ApplicationReason;
    use App\Enums\ApplicationStatus;
    use App\Events\HandleSendMailApplication;
    use App\Exports\Application\ApplicationsExport;
    use App\Http\Controllers\Controller;
    use App\Http\Requests\V1\Application\ApplicationStoreRequest;
    use App\Http\Requests\V1\Application\IndexApplicationRequest;
    use App\Http\Requests\V1\Application\StoreProposalApplicationRequest;
    use App\Http\Requests\V1\Application\UpdateStateApplicationRequest;
    use App\Http\Resources\V1\Application\ApplicationResource;
    use App\Http\Resources\V1\Application\ProposalApplicationResource;
    use App\Jobs\ZipFileOrFolderDownload;
    use App\Models\Application;
    use App\Models\File;
    use App\Services\FileService\FileServiceInterface;
    use Dotenv\Exception\ValidationException;
    use Illuminate\Bus\Batch;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Bus;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Storage;
    use Maatwebsite\Excel\Facades\Excel;

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
         * @throws \Illuminate\Contracts\Container\BindingResolutionException
         */

        public function store(ApplicationStoreRequest $request)
        {
            $reason                      = $request->input('reason');
            $dateRests                   = $request->input('date_rest', []);
            $des                         = $request->input('des');
            $userId                      = $request->input('user_id');
            $userFollows                 = $request->input('user_follows');
            $applicationType             = $request->input('application_type', ApplicationReason::SICK_LEAVE);
            $uploadFileCreateApplication = app()->make(FileServiceInterface::class);
            $data        = $this->bodyCreateApplication('ONESIGN-'.rand(0, 99999), ApplicationStatus::PENDING,
                Auth::user()->name, $reason, $applicationType, 'develop', $userId, $des, Auth::id(),
                ApplicationStatus::CREATE_APPLICATION);
            DB::beginTransaction();
            $nameFiles = [];
            try {
                $application = $this->application::create($data);

                if ($request->hasFile('files')) {
                    $dataFile  = $uploadFileCreateApplication->uploadMultipleFileAndCreateDatabase($request,
                        'upload_applications');
                    $nameFiles = $dataFile['nameFiles'];
                    $application->applicationFiles()->syncWithoutDetaching($dataFile['fileIds']);
                }
                $application->dateTimeApplications()->createMany($dateRests);
                if (count($userFollows) > 0) {
                    $application->users()->attach($userFollows);
                }
                DB::commit();
                event(new HandleSendMailApplication($application));
                return new ApplicationResource($application->load('user', 'users', 'userCreateApplication',
                    'dateTimeApplications', 'applicationFiles'));
            } catch (\Exception $exception) {
                DB::rollBack();
                if (count($nameFiles) > 0) {
                    foreach ($nameFiles as $nameFile) {
                        handleRemoveFile(config('pathUploadFile.path_file'), $nameFile);
                    }
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
            $check = $application->users()->where('user_id' , Auth::id())->first();
            if (!is_null($check))
            {
                throw new ValidationException('User follow không được cập nhật trạng thái !');
            }
            if (is_null($application->where('user_id' , Auth::id())->first()))
            {
                throw new ValidationException('Bạn không phải user được gán là người kiểm duyệt');
            }
            try {
                $application = $application->status->transitionTo($status);
                return new ApplicationResource($application->load('user', 'users', 'userCreateApplication',
                    'dateTimeApplications' ,'applicationFiles'));
            } catch (\Exception $exception) {
                throw new \Exception($exception->getMessage());
            }
        }

        /**
         * @param  StoreProposalApplicationRequest  $request
         * @return ProposalApplicationResource
         * @throws \Illuminate\Contracts\Container\BindingResolutionException
         */

        public function storeProposal(StoreProposalApplicationRequest $request)
        {
            $proposalName       = $request->input('proposal_name');
            $proponent          = $request->input('proponent');
            $priceProposal      = $request->input('price_proposal');
            $accountInformation = $request->input('account_information');
            $deliveryTime       = $request->input('delivery_time');
            $deliveryDate       = $request->input('delivery_date');
            $applicationType    = $request->input('application_type');
            $userId             = $request->input('user_id');
            $userFollows                 = $request->input('user_follows');
            $uploadFileCreateApplication = app()->make(FileServiceInterface::class);
            $data                        = $this->bodyCreateApplication('ONESIGN-'.rand(0, 99999),
                ApplicationStatus::PENDING,
                Auth::user()->name, '', $applicationType, 'develop', $userId, '', Auth::id(),
                ApplicationStatus::CREATE_SUGGESTION);
            $data['proposal_name']       = $proposalName;
            $data['proponent']           = $proponent;
            $data['price_proposal']      = $priceProposal;
            $data['account_information'] = $accountInformation;
            $data['delivery_time']       = $deliveryTime;
            $data['delivery_date']       = $deliveryDate;
            $nameFiles = [];
            DB::beginTransaction();
            try {
                $application = $this->application::create($data);
                if ($request->hasFile('files')) {
                    $dataFile  = $uploadFileCreateApplication->uploadMultipleFileAndCreateDatabase($request,
                        'upload_applications');
                    $nameFiles = $dataFile['nameFiles'];
                    $application->applicationFiles()->syncWithoutDetaching($dataFile['fileIds']);
                }
                if (count($userFollows) > 0) {
                    $application->users()->attach($userFollows);
                }

                DB::commit();
                event(new HandleSendMailApplication($application));
                return new ProposalApplicationResource($application->load('user', 'users', 'userCreateApplication', 'applicationFiles'));
            }catch (\Exception $exception)
            {
                DB::rollBack();
                if (count($nameFiles) > 0) {
                    foreach ($nameFiles as $nameFile) {
                        handleRemoveFile(config('pathUploadFile.path_file'), $nameFile);
                    }
                }
                throw new \Exception($exception->getMessage());
            }
        }

        /**
         * @param  IndexApplicationRequest  $request
         * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
         */

        public function index(IndexApplicationRequest $request)
        {
            $status = $request->input('status');
            $search = $request->input('search');
            $applications = $this->application->ByUserLogin()->FilterStatus($status)
                                                             ->SearchName($search)
                                                             ->orderByDesc('id');
            if ($status == ApplicationStatus::PROPOSAL_STR)
            {
                return  ProposalApplicationResource::collection($applications->with('user', 'users', 'userCreateApplication', 'applicationFiles')->paginate(15));
            }
            return ApplicationResource::collection($applications->with('user', 'users', 'userCreateApplication',
                'dateTimeApplications' ,'applicationFiles')->paginate(15));
        }

        public function exportApplication(IndexApplicationRequest $request)
        {
            $status       = $request->input('status');
            $search       = $request->input('search');
            $applications = $this->application->ByUserLogin()->FilterStatus($status)
                                              ->SearchName($search)
                                              ->orderByDesc('id');
            if ($status == ApplicationStatus::APPLICATION_STR) {
                $applications = $applications->with('user', 'users', 'userCreateApplication',
                    'dateTimeApplications', 'applicationFiles')->get();
            } else {
                $applications = $applications->with('user', 'users', 'userCreateApplication',
                    'applicationFiles')->get();
            }

             (new ApplicationsExport($applications))->store('public/export/application.xlsx')->onQueue('imports');
        }

        public function downloadExcel()
        {
            $path =public_path('storage/export/application.xlsx');
            response()->download($path);
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
