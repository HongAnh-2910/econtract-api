<?php

namespace App\Http\Controllers\Api\V1\Member;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Member\MemberResource;
use App\Models\Department;
use App\Models\User;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\V1\Member\RegisterRequest;
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
      }])->where(function ($query){
           $query->where('parent_id' ,Auth::id());
      })->paginate();

        return  MemberResource::collection($members);
    }

    /**
     * @param  RegisterRequest  $request
     * @return JsonResponse
     */

    public function store(RegisterRequest $request):JsonResponse
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
    public function update(Request $request, $id)
    {
        dd('123');
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
