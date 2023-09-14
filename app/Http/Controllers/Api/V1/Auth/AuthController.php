<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Enums\StatusIsActive;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\LoginAuthRequest;
use App\Http\Requests\V1\Auth\RegisterAuthRequest;
use App\Http\Resources\V1\User\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @var User
     */
    protected User $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param RegisterAuthRequest $request
     * @return JsonResponse
     */

    public function register(RegisterAuthRequest $request): JsonResponse
    {
        $params = $request->except('department_id' ,'password_confirmation');
        try {
            $user = $this->user->create($params);
            $user = $this->dataUserResponse($user);
            return $this->successResponse($user ,'success' , 200);

        }catch (Exception $exception)
        {
            return $this->errorResponse('create user error' , 500);
        }

    }

    /**
     * @param LoginAuthRequest $request
     * @return JsonResponse
     */

    public function login(LoginAuthRequest $request): JsonResponse
    {
        $email = $request->input('email');
        $password = $request->input('password');
        try {
            $user = $this->user->where('email' , $email)->first();
            $checkPassword = Hash::check($password ,$user->password);
            if (!$checkPassword)
            {
                return $this->errorResponse('Sai mật khẩu hoặc email' , 401);
            }

            $user = $this->dataUserResponse($user);

            return $this->successResponse($user ,'success' , 200);

        }catch (Exception $exception)
        {
            return $this->errorResponse('login user failed' , 401);
        }
    }

    /**
     * @return UserResource
     */

    public function user(): UserResource
    {
        return new UserResource(Auth::user());
    }

    /**
     * @param User $user
     * @return UserResource
     */
    private function dataUserResponse(User $user): UserResource
    {
        $token = $user->createToken('token')->plainTextToken;
        $user['access_token'] = $token;
        $user['token_type'] = 'Bearer';
        return new UserResource($user);
    }

}
