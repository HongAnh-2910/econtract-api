<?php

namespace App\Http\Controllers\Api\V1\Department;

use App\Enums\Status;
use App\Enums\TypeDelete;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Department\DestroyTypeDepartmentRequest;
use App\Http\Requests\V1\Department\IndexDepartmentRequest;
use App\Http\Requests\V1\Department\StoreDepartmentRequest;
use App\Http\Requests\V1\Department\UpdateDepartmentRequest;
use App\Http\Resources\V1\Department\DepartmentResource;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    protected Department $department;
    public function __construct(Department $department)
    {
        $this->department = $department;
    }

    /**
     * @param IndexDepartmentRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexDepartmentRequest $request)
    {
        $search = $request->input('name');
        $status = $request->input('status');
        $departments = $this->department->QueryUserDepartment()
                                        ->when($status == Status::TRASHED , function ($query){
            return $query->onlyTrashed();
        })->with(['user', 'treeChildren' => function ($query) {
            return $query->with('parent', 'user');
        }])
            ->whereNull('parent_id')
            ->SearchName($search)
            ->paginate();
        return (DepartmentResource::collection($departments));
    }


    /**
     * @param StoreDepartmentRequest $request
     * @return JsonResponse
     */
    public function store(StoreDepartmentRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $department = $this->department->create($data);
        $department = new DepartmentResource($department->load('user', 'treeChildren', 'parent'));
        return $this->successResponse($department, 'success', 200);
    }

    /**
     * @param Department $department
     * @return JsonResponse
     */
    public function show(Department $department):JsonResponse
    {
        $department = $department->load(['user', 'treeChildren' => function ($query) {
            return $query->with(['parent', 'user']);
        } ,'parent' => function($query){
            return $query->with('treeChildren', 'user');
        }]);
        $department = new DepartmentResource($department);
        return $this->successResponse($department, 'success', 200);
    }

    /**
     * @param UpdateDepartmentRequest $request
     * @param Department $department
     * @return JsonResponse
     */
    public function update(UpdateDepartmentRequest $request, Department $department):JsonResponse
    {
        $name = $request->input('name');
        $parentId = $request->input('parent_id');
        $department->name = $name ?? $department->name;
        $department->parent_id = $parentId;
        $department->user_id = Auth::id();
        $department->save();
        $department = $department->load(['user', 'treeChildren' => function ($query) {
            return $query->with('parent', 'user');
        } ,'parent' => function($query){
            return $query->with('children', 'user');
        }]);
        $department = new DepartmentResource($department);
        return $this->successResponse($department, 'success', 200);
    }


    /**
     * @param DestroyTypeDepartmentRequest $request
     * @param $id
     * @return JsonResponse
     */

    public function destroy(DestroyTypeDepartmentRequest $request , $id):JsonResponse
    {
        $type = $request->input('type', TypeDelete::SOFT_DELETE);
        $department = $this->department->CheckTrashed($type)->findOrFail($id)
            ->load(['children' => function ($item) use ($type) {
                return $item->CheckTrashed($type);
            }]);
        try {
            $departmentIds = $department->children->pluck('id')->merge($department->id);
            if ($type == TypeDelete::SOFT_DELETE) {
                $this->department->GetIdsDepartment($departmentIds)->delete();
                return $this->successResponse(null, 'oke', 201);
            }
            $this->department->withTrashed()->GetIdsDepartment($departmentIds)->forceDelete();
            return $this->successResponse(null, 'oke', 201);
        } catch (\Exception $exception) {
            return $this->errorResponse('Lỗi delete', 400);
        }
    }

    public function updatePermissionDepartment()
    {
        dd('123');
    }

}
