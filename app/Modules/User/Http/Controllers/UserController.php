<?php

namespace App\Modules\User\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\ErrorCode;
use App\Http\Controllers\Controller;
use App\Models\Db\User;
use App\Modules\User\Http\Requests\CreateUser;
use App\Modules\User\Http\Requests\UpdateUser;
use Illuminate\Contracts\Auth\Guard;
use App\Modules\User\Services\User as UserService;

class UserController extends Controller
{
    /**
     * Get list of all allowed users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return ApiResponse::responseOk(User::allowed()->orderBy('id')->get());
    }

    /**
     * Creates new user.
     *
     * @param CreateUser $request
     * @param UserService $service
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateUser $request, UserService $service)
    {
        $user = $service->create($request);

        return ApiResponse::responseOk($user, 201);
    }

    /**
     * Return current user data.
     *
     * @param Guard $auth
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function current(Guard $auth)
    {
        $user = $auth->user();

        /*
         * @see \Illuminate\Database\Eloquent\Model;
         */
        $user->load('selectedUserCompany.role', 'selectedUserCompany.company');

        return ApiResponse::responseOk($user);
    }

    /**
     * Return list of companies for current user.
     *
     * @param Guard $auth
     * @return \Illuminate\Http\JsonResponse
     */
    public function companies(Guard $auth)
    {
        return ApiResponse::responseOk($auth->user()->companies);
    }

    /**
     * @param UpdateUser $request
     * @param UserService $service
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUser $request, UserService $service, $id)
    {
        $user = User::findOrFail($id);
        if ($request->input('password') && ! $service->checkPassword($user, $request)) {
            return ApiResponse::responseError(ErrorCode::PASSWORD_INVALID_PASSWORD, 422);
        }

        $service->updateData($user, $request);

        return ApiResponse::responseOK([]);
    }
}
