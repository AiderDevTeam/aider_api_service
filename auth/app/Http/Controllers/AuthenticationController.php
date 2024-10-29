<?php

namespace App\Http\Controllers;

use App\Http\Actions\User\AddUserAddressAction;
use App\Http\Actions\User\CheckUsernameExistenceAction;
use App\Http\Actions\User\DeactivateAccountAction;
use App\Http\Actions\User\ForgotPasswordAction;
use App\Http\Actions\User\LoginAction;
use App\Http\Actions\User\LogoutAction;
use App\Http\Actions\User\PasswordResetAction;
use App\Http\Actions\User\ProfilePhotoUploadAction;
use App\Http\Actions\User\SignupAction;
use App\Http\Actions\User\UpdateAddressAction;
use App\Http\Actions\User\UpdateUserAction;
use App\Http\Actions\User\UsernameSuggestionAction;
use App\Http\Actions\User\VerifyIdAction;
use App\Http\Requests\AccountDeactivationRequest;
use App\Http\Requests\AddAddressRequest;
use App\Http\Requests\AuthenticationRequest;
use App\Http\Requests\DisplayNameVerificationRequest;
use App\Http\Requests\EmailVerificationRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Http\Requests\ProfilePhotoUploadRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UsernameSuggestionRequest;
use App\Http\Requests\UserSignupRequest;
use App\Http\Requests\VerifyIdRequest;
use App\Http\Resources\UserResource;
use App\Models\ProductAddress;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return successfulJsonResponse(
            new UserResource($request->user())
        );
    }

//    public function store()
//    {
//    }

    public function update(UpdateUserRequest $request, UpdateUserAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function login(AuthenticationRequest $request, LoginAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function logout(LogoutAction $action): JsonResponse
    {
        return $action->handle();
    }

    public function suggestUsername(UsernameSuggestionRequest $request, UsernameSuggestionAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function verifyId(VerifyIdRequest $request, VerifyIdAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function checkPhone()
    {
        //     TO DO
    }

    public function forgotPassword(ForgotPasswordRequest $request, ForgotPasswordAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function resetPassword(PasswordResetRequest $request, PasswordResetAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function addAddress(AddAddressRequest $request, AddUserAddressAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function updateAddress(ProductAddress $address, UpdateAddressRequest $request, UpdateAddressAction $action): JsonResponse
    {
        return $action->handle($request, $address);
    }

    public function getUser(User $user): JsonResponse
    {
        return successfulJsonResponse(
            new UserResource($user)
        );
    }

    public function checkUsernameExistence(Request $request, CheckUsernameExistenceAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function signUp(UserSignupRequest $request, SignupAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function accountDeactivation(AccountDeactivationRequest $request, DeactivateAccountAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function verifyEmail(EmailVerificationRequest $request): JsonResponse
    {
        return successfulJsonResponse(message: "{$request->validated('email')} is available");
    }

    public function verifyDisplayName(DisplayNameVerificationRequest $request): JsonResponse
    {
        return successfulJsonResponse(message: "Selected display name is available");
    }

    public function uploadProfilePhoto(ProfilePhotoUploadRequest $request, ProfilePhotoUploadAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
