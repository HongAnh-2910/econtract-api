<?php

namespace App\Http\Controllers\Api\V1\Member;

use App\Http\Controllers\Controller;
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
          return $query->whereNull('parent_id')->with('childrenDepartment');
      }])->where('parent_id' ,Auth::id())->paginate();

        return  MemberResource::collection($members);
    }

    /**
     * @param  RegisterMemberRequest  $request
     * @return JsonResponse
     */

    public function store(RegisterMemberRequest $request):JsonResponse
    {
        $departmentIds = $request->input('department_id');
        $query = $this->department->GetIdsDepartment($departmentIds)->whereNull('parent_id');
        $checkDepartment = $query->count();
        if ($checkDepartment != count($departmentIds)) {
            throw new ValidationException('Phòng ban không tồn tại', 422);
        }
        $data         = $query->with('childrenDepartment')->get();
        $departmentId = dataTree($data, null ,'childrenDepartment')->pluck('id');
        $params       = $request->except('department_id', 'password_confirmation');
        $params['parent_id'] = Auth::id();
        try {
            $user = $this->user->create($params);
            $user->departments()->sync($departmentId);
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
           return $query->whereNull('parent_id')->with('childrenDepartment');
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
        $user->name = $name ?? $user->name;
        $user->email = $email ?? $user->email;
        $user->password = $password ?? $user->password;
        $user->active = $active ?? $user->active;
        if ($request->hasFile('images'))
        {
            $file = $request->images;
            $fileName = time().'_'.$file->getClientOriginalName();
            handleUploadFile($file ,Storage::path('public/uploads') , $fileName);
            if (!is_null($user->img_user))
            {
                handleRemoveFile(config('pathUploadFile.path_avatar_user') ,$user->img_user);
            }
            $user->img_user = $fileName;
        }
        $user->save();
        $getDepartmentParent = $this->department->GetIdsDepartment($departmentIds)->whereNull('parent_id')
            ->with('childrenDepartment')->get();
        $departmentId = dataTree($getDepartmentParent ,null)->pluck('id');
        $user->departments()->sync($departmentId);
        $member = $user->load(['departments' => function($query){
            return $query->whereNull('parent_id')->with('childrenDepartment');
        }]);
        return $this->successResponse(new MemberResource($member), 'success', 201);

        }catch (Exception $exception)
        {
            $this->errorResponse('error update member' , 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
