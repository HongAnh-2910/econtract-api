<?php

namespace App\Http\Controllers\Api\V1\Contract;

use App\Enums\ContractStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Contract\StoreContractRequest;
use App\Http\Resources\V1\Contract\ContractResource;
use App\Models\Contract;
use App\Models\File;
use App\Services\CrawlCompanyInformation;
use App\Services\FileService\FileServiceInterface;
use Dotenv\Exception\ValidationException;
use Error;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ContractController extends Controller
{
    /**
     * @var CrawlCompanyInformation
     */
    protected CrawlCompanyInformation $crawlCompanyInformation;

    protected Contract $contract;

    public function __construct(CrawlCompanyInformation $companyInformation , Contract $contract)
    {
        $this->crawlCompanyInformation = $companyInformation;
        $this->contract = $contract;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */

    public function getTaxCode(Request $request)
    {
        $tax_code = $request->input('tax_code');
        $getTaxCode = $this->crawlCompanyInformation->run($tax_code);
        return response()->json(['data' => $getTaxCode] ,201);
    }


    public function store(StoreContractRequest $request)
    {
        $randomNumber       = rand(0, 999);
        $styleNumber        = str_pad($randomNumber, 3, '0', STR_PAD_LEFT);
        $customerCodeRandom = '01GTKT0-'.$styleNumber;
        $requestContract    = $request->except(['files', 'signatures', 'signatures_follow']);
        $signatures         = $request->input('signatures', []);
        $signaturesFollow   = $request->input('signatures_follow', []);
        $signaturesFollow   = collect($signaturesFollow)->map(function ($item) {
            $item['token'] = Str::random(40);
            return $item;
        });
        $nameFiles          = [];
        DB::beginTransaction();
        try {
            $requestContract['code']    = $customerCodeRandom;
            $requestContract['status']  = ContractStatus::CANCELED;
            $requestContract['user_id'] = Auth::id();
            $requestContract['slug']    = Str::slug($customerCodeRandom.'-'.$requestContract['name_customer'], '-');
            $contract                   = Contract::create($requestContract);
            $uploadFileCreateContract   = app()->make(FileServiceInterface::class);
            if ($request->hasFile('files')) {
                $dataFile   = $uploadFileCreateContract->uploadMultipleFileAndCreateDatabase($request,
                    'upload_contract', $contract->id);
                $nameFiles  = $dataFile['nameFiles'];
                $fileIds    = $dataFile['fileIds'];
                $dataAttach = [];
                foreach ($nameFiles as $key => $name) {
                    $dataAttach[$fileIds[$key]] = ['base64' => "data:@file/pdf;base64,".base64_encode(file_get_contents(storage_path('app/public/files/'.$name)))];
                }
                $contract->files()->attach($dataAttach);
                $contract->signatures()->createMany($signatures);
                $contract->follows()->createMany($signaturesFollow);
            }
            DB::commit();
            $contract = $contract->load('files' ,'signatures' ,'follows' ,'banking' ,'user');
            return new ContractResource($contract);
        } catch (Exception $exception) {
            DB::rollBack();
            if (count($nameFiles) > 0) {
                foreach ($nameFiles as $nameFile) {
                    handleRemoveFile(storage_path('app/public/files'), $nameFile);
                }
            }
            throw new Error($exception->getMessage());
        }
    }

    public function find(Contract $contract)
    {
        $contract = $contract->load('files' ,'signatures' ,'follows' ,'banking' ,'user');
        return new ContractResource($contract);
    }

}
