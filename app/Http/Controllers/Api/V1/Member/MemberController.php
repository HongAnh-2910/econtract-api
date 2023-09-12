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
use Illuminate\Http\Request;
use App\Http\Requests\V1\Member\RegisterMemberRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

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
        $departmentId = $request->input('department_id');
        $query = $this->department->whereIn('id' , $departmentId)->whereNull('parent_id');
//       $idsUserDepartment = Auth::user()->departments->pluck('id');
//       $departmentId = $request->input('department_id');
//       $departmentIdUnique = $idsUserDepartment->merge($departmentId)->unique();
//       $departments = $this->department->whereIn('id' , $departmentIdUnique)->whereNull('parent_id')->get()->pluck('name');
        $checkDepartment = $query->count();
        if ($checkDepartment != count($departmentId)) {
            throw new ValidationException('Phòng ban không tồn tại', 422);
        }
        $data         = $query->with('childrenDepartment')->get();
        $departmentId = collect($this->department::dataTree($data, null))->pluck('id');
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
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(UpdateMemberRequest $request, User $user)
    {
       $a = storage_path('uploads/1694492467_hinh-anh-naruto-chat-ngau-dep-820x600.jpg');
       dd($a);
        dd(asset('storage/uploads/1694492467_hinh-anh-naruto-chat-ngau-dep-820x600.jpg'));
        $name          = $request->input('name');
        $email         = $request->input('email');
        $password      = $request->input('password');
        $departmentIds = $request->input('department_id');
        $active        = $request->input('active');
//        update
        $user->name = $name ?? $user->name;
        $user->email = $email ?? $user->email;
        $user->password = $password ?? $user->password;
        $user->active = $active ?? $user->active;
        if ($request->hasFile('images'))
        {
            $file = $request->images;
            $fileName = time().'_'.$file->getClientOriginalName();
            $filePath =$file->storeAs('uploads', $fileName, 'public');
            $user->img_user = $fileName;
//            $fileModel->name = time().'_'.$req->file->getClientOriginalName();
//            $fileModel->file_path = '/storage/' . $filePath;
//            $fileModel->save();
        }
        $user->save();
        return $user;

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
