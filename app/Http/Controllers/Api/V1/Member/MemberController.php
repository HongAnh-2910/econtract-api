<?php

namespace App\Http\Controllers\Api\V1\Member;

use App\Enums\TypeDelete;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Member\DestroyTypeMemberRequest;
use App\Http\Requests\V1\Member\UpdateMemberRequest;
use App\Http\Resources\V1\Member\MemberResource;
use App\Models\Department;
use App\Models\User;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\V1\Member\RegisterMemberRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    /**
     * @var User
     */
    protected User $user;

    /**
     * @var Department
     */

    protected Department $department;

    public function __construct(User $user , Department $department)
    {
        $this->user       = $user;
        $this->department = $department;
    }

    /**
     * @return AnonymousResourceCollection
     */

    public function index():AnonymousResourceCollection
    {
        $members =  $this->user->with(['departments' => function($query){
          return $query->GetParentAndLoadChildrenDepartment();
      }])->where('parent_id' ,Auth::id())->paginate();

        return  MemberResource::collection($members);
    }

    /**
     * @param  RegisterMemberRequest  $request
     * @return JsonResponse
     */

    public function store(RegisterMemberRequest $request)
    {
        $departmentIds = $request->input('department_id');
        $query = $this->department->GetIdsDepartment($departmentIds)->whereNull('parent_id');
        $checkDepartment = $query->count();
        if ($checkDepartment != count($departmentIds)) {
            throw new ValidationException('Phòng ban không tồn tại', 422);
        }
        $data         = $query->with('childrenDepartment')->get();
        $departmentIds = dataTree($data, null)->pluck('id');
        $params       = $request->except('department_id', 'password_confirmation');
        $params['parent_id'] = Auth::id();
        try {
            $user = $this->user->create($params);
            $user->departments()->sync($departmentIds);
            return $this->successResponse(null, 'success', 201);
        } catch (Exception $exception) {
            return $this->errorResponse('create user error', 500);
        }
    }

    /**
     * @param $id
     * @return MemberResource
     */
    public function show($id):MemberResource
    {
       $member = $this->user->findOrFail($id)->load(['departments' => function($query){
           return $query->GetParentAndLoadChildrenDepartment();
       }]);
       return new MemberResource($member);
    }

    /**
     * @param UpdateMemberRequest $request
     * @param User $user
     * @return JsonResponse|void
     */
    public function update(UpdateMemberRequest $request, User $user)
    {
        $name          = $request->input('name');
        $email         = $request->input('email');
        $password      = $request->input('password');
        $departmentIds = $request->input('department_id');
        $active        = $request->input('active');
        try {
            $user->name     = $name ?? $user->name;
            $user->email    = $email ?? $user->email;
            $user->password = $password ?? $user->password;
            $user->active   = $active ?? $user->active;
            if ($request->hasFile('images')) {
                $file     = $request->images;
                $fileName = time().'_'.$file->getClientOriginalName();
                handleUploadFile($file, Storage::path('public/uploads'), $fileName);
                if ( ! is_null($user->img_user)) {
                    handleRemoveFile(config('pathUploadFile.path_avatar_user'), $user->img_user);
                }
                $user->img_user = $fileName;
            }
            $user->save();
            if (!empty($departmentIds))
            {
                $getDepartmentParent = $this->department->GetIdsDepartment($departmentIds)
                                                        ->GetParentAndLoadChildrenDepartment()
                                                        ->get();
                $departmentId        = dataTree($getDepartmentParent, null)->pluck('id');
                $user->departments()->sync($departmentId);
                $member = $user->load(['departments' => function ($query) {
                    return $query->GetParentAndLoadChildrenDepartment();
                }]);
            }else
            {
                $member = $user->load('departmentsOrUser');
            }
            return $this->successResponse(new MemberResource($member), 'success', 201);

        } catch (Exception $exception) {
            $this->errorResponse('error update member', 500);
        }
    }

    /**
     * @param $id
     * @param  DestroyTypeMemberRequest  $request
     * @return JsonResponse
     */

    public function destroy($id,DestroyTypeMemberRequest $request)
    {
        $type = $request->input('type' , TypeDelete::SOFT_DELETE);
        $member = $this->user->CheckTrashed($type)->findOrFail($id);
        if ($type == TypeDelete::SOFT_DELETE)
        {
            $member->delete();
            return $this->successResponse(null, 'oke', 201);
        }
        $departmentIds =  $member->departments->pluck('id');
        try {
            $member->departments()->detach($departmentIds);
            $member->forceDelete();
            return $this->successResponse(null, 'oke', 201);
        }catch (Exception $exception)
        {
            return $this->errorResponse('error delete' ,500);
        }
    }
}
