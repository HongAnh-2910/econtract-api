<?php

namespace App\Http\Controllers\Api\V1\Contract;

use App\Enums\ContractStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Contract\StoreContractRequest;
use App\Models\Contract;
use App\Services\CrawlCompanyInformation;
use App\Services\FileService\FileServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContractController extends Controller
{
    /**
     * @var CrawlCompanyInformation
     */
    protected $crawlCompanyInformation;

    public function __construct(CrawlCompanyInformation $companyInformation)
    {
        $this->crawlCompanyInformation = $companyInformation;
    }

    /**
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function getTaxCode(Request $request)
    {
        $tax_code = $request->input('tax_code');
        $getTaxCode = $this->crawlCompanyInformation->run($tax_code);
        return response()->json(['data' => $getTaxCode] ,201);
    }


    public function store(StoreContractRequest $request)
    {
//        $createdContract = $request->input('created_contract');
//        $nameCustomer   = $request->input('name_customer');
//        $email = $request->input('email');
//        $nameCty = $request->input('name_cty');
//        $address = $request
        DB::beginTransaction();
        try {
            $randomNumber               = rand(0, 999);
            $styleNumber                = str_pad($randomNumber, 3, '0', STR_PAD_LEFT);
            $customerCodeRandom         = '01GTKT0-'.$styleNumber;
            $requestContract            = $request->except(['files', 'signatures', 'signatures_follow']);
            $requestContract['code']    = $customerCodeRandom;
            $requestContract['status']  = ContractStatus::CANCELED;
            $requestContract['user_id'] = Auth::id();
            $requestContract['slug']    = Str::slug($customerCodeRandom.'-'.$requestContract['name_customer'], '-');
            $contract = Contract::create($requestContract);
            $uploadFileCreateContract = app()->make(FileServiceInterface::class);
            if ($request->hasFile('files')) {
                $dataFile  = $uploadFileCreateContract->uploadMultipleFileAndCreateDatabase($request,
                    'upload_contract' , $contract->id);
                $nameFiles = $dataFile['nameFiles'];
                $contract->applicationFiles()->syncWithoutDetaching($dataFile['fileIds']);
            }
            DB::commit();
        }catch (\Exception $exception)
        {

        }
    }



}
