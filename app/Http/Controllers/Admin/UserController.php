<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PasswordRequest;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Role;
use App\Models\UploadFile;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    protected $module;
    public function __construct()
    {
        parent::__construct();
        $this->module = [
            'key'   => config('dummy.module.user.key'),
        ];
    }

    public function login()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin-user-list');
        }
        return view("admin::auth.sign-in");
    }

    public function index()
    {
        $data['roles'] = Role::where('status', $this->active)->get();
        return view("admin::pages.user.user.index", $data);
    }

    public function data()
    {
        $data = User::with('userRole')
            ->when(filled(request('search')), function ($q) {
                $q->where(function ($q) {
                    $q->orWhere('name', 'like', '%' . request('search') . '%');
                });
            })
            ->when(request('from_date') && request('from_date'), function ($q) {
                $q->where('created_at', '>=', request('from_date'));
                $q->where('created_at', '<=', request('to_date'));
            })
            ->when(request('type'), function ($q) {
                $q->where('type', request('type'));
            })
            ->when(request('trash'), function ($q) {
                $q->onlyTrashed();
            })
            ->where(function ($q) {
                $q->where('role', '!=', $this->isRoleSuperAdmin)
                    ->orWhereNull('role');
            })
            ->orderByDesc('created_at')
            ->paginate(25);

        return response()->json($data);
    }

    public function save(UserRequest $request)
    {
        DB::beginTransaction();
        try {
            $profile = UploadFile::uploadFile('/user', $request->file('profile'));
            $input = [
                'name'             => $request->name,
                'gender'           => $request->gender,
                'date_of_birth'    => $request->date_of_birth ? Carbon::createFromFormat('d/m/Y', $request->date_of_birth) : null,
                'email'            => $request->email,
                'phone'            => $request->phone,
                'role'             => $request->role_id ? optional(Role::find($request->role_id))->name : '',
                'role_id'          => $request->role_id,
                'profile'          => $profile,
                'status'           => $request->status,
            ];

            if (!$request->id) {
                $input['password'] = bcrypt($request->password);
                $data = User::create($input);
            } else {
                $data = User::find($request->id);
                if ($request->file('profile') || !$request->tmp_file) {
                    UploadFile::deleteFile('/user', $data->profile);
                }
                $input['profile'] = $profile ?? $request->tmp_file;
                $data->update($input);
            }
            // assignRole
            if ($input['role']) {
                $this->assignRole($data->id, $input['role']);
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => $request->id ? __('form.message.update.success') : __('form.message.create.success'),
                'error' => false,
            ]);
        } catch (Exception $e) {
            dd($e);
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => __('form.message.error'),
                'error' => true,
            ]);
        }
    }

    public function onSavePassword(PasswordRequest $request)
    {
        try {
            $user = User::find($request->id);
            $user->update(['password' => bcrypt($request->new_password)]);
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

    public function onUpdateStatus(Request $request)
    {
        try {
            $user = User::find($request->id);
            $user->update(['status' => $request->status]);
            return response()->json([
                'status' => 'success',
                'message' => __('form.message.status.success'),
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

    public function onDelete(Request $request)
    {
        try {
            User::find($request->id)->delete();
            return response()->json([
                'status' => 'success',
                'message' => __('form.message.move_to_trash.success'),
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
    public function onRestore(Request $request)
    {
        try {
            User::onlyTrashed()->find($request->id)->restore();
            return response()->json([
                'status' => 'success',
                'message' => __('form.message.restore.success'),
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
    public function onDestroy(Request $request)
    {
        try {
            User::onlyTrashed()->find($request->id)->forceDelete();
            return response()->json([
                'status' => 'success',
                'message' => __('form.message.delete.success'),
                'error' => false,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('form.message.error'),
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function assignRole($userId, $roleData)
    {
        try {
            $data = User::findOrFail($userId);
            $roles = $roleData;
            $original_data = $data->getOriginal();
            $data->syncRoles($roles);
            return $this->responseSuccess();
        } catch (Exception $e) {
            return $this->responseError();
        }
    }


}
