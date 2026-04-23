<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Web\LoginRequest;
use App\Http\Requests\Api\Web\ProfileRequest;
use App\Http\Requests\Api\Web\RegisterRequest;
use App\Http\Resources\Web\ProfileResource;
use App\Models\UploadFile;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $module;

    public function __construct()
    {
        parent::__construct();
        $this->module = [
            'key' => config('dummy.module.auth.key'),
        ];
    }

    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $profile = UploadFile::uploadFile('/user', $request->file('profile'));

            $name = $request->name;
            if (!$name) {
                $name = trim(($request->first_name ?? '') . ' ' . ($request->last_name ?? ''));
            }

            $input = [
                'name' => $name ?: null,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'profile' => $profile,
                'address' => $request->address,
                'status' => config('dummy.status.active.key'),
                'password' => bcrypt($request->password),
            ];

            $data = User::create($input);

            $credentials = ['password' => $request->password];
            if ($request->filled('email')) {
                $credentials['email'] = $request->email;
            } else {
                $credentials['phone'] = $request->phone;
            }

            if (!$token = Auth::guard('api_web')->attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            DB::commit();

            return response()->json([
                'code' => 200,
                'data' => $data,
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => null,
                'status' => 'success',
                'error' => false,
                'message' => 'Operation successful!',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return $this->responseError();
        }
    }

    public function login(LoginRequest $request)
    {
        $credentials = ['password' => $request->password];
        if ($request->filled('email')) {
            $credentials['email'] = $request->email;
        } else {
            $credentials['phone'] = $request->phone;
        }

        if (!$token = Auth::guard('api_web')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => null,
        ]);
    }

    public function logout()
    {
        Auth::guard('api_web')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function profile()
    {
        return $this->responseSuccess(new ProfileResource(Auth::guard('api_web')->user()));
    }

    public function updateProfile(ProfileRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = Auth::guard('api_web')->user();

            $input = [
                'name' => $request->name,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'address' => $request->address,
            ];

            if ($request->file('profile')) {
                $input['profile'] = UploadFile::uploadFile('/user', $request->file('profile'));
                UploadFile::deleteFile('/user', $user?->profile);
            }

            $user->update($input);

            DB::commit();

            return $this->responseSuccess();
        } catch (Exception $e) {
            DB::rollBack();

            return $this->responseError();
        }
    }

    public function checkUniquePhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|unique:users,phone',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 200);
        }

        return response()->json([
            'status' => true,
            'message' => 'success',
        ]);
    }

    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required',
                'new_password' => 'nullable|min:6',
                'password' => 'nullable|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $newPassword = $request->new_password ?: $request->password;
            if (!$newPassword) {
                return response()->json([
                    'status' => false,
                    'errors' => ['password' => ['Password is required.']],
                ], 422);
            }

            DB::transaction(function () use ($request, $newPassword) {
                $user = User::where('phone', $request->phone)->firstOrFail();
                $user->update(['password' => bcrypt($newPassword)]);
            });

            return response()->json([
                'status' => 'success',
                'message' => __('form.message.change_password'),
                'error' => false,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('form.message.error'),
                'error' => true,
            ]);
        }
    }
}
