<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use App\Models\ModelHasPermission;
use App\Models\ModulePermission;
use App\Models\Permission;
use App\Models\Role;
use App\Services\ActivityLogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role as ModelsRole;

class RoleController extends Controller
{
    protected $module;
    public function __construct()
    {
        parent::__construct();
        $this->module = [
            'key'   => config('dummy.module.role.key'),
        ];
        $this->middleware('permission:role-view', ['only' => ['index']]);
        $this->middleware('permission:role-create', ['only' => ['onCreate', 'onSave']]);
        $this->middleware('permission:role-update', ['only' => ['onUpdateStatus', 'onAssignPermission']]);
    }

    public function index()
    {
        return view("admin::pages.user.role.index");
    }

    public function data()
    {
        $data = Role::query()
            ->when(filled(request('search')), function ($q) {
                $search = '%' . strtolower(request('search')) . '%';
                $q->where(function ($query) use ($search) {
                    $query->whereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(display_name, "$.en"))) like ?', [$search])
                        ->orWhereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(display_name, "$.km"))) like ?', [$search]);
                });
            })
            ->orderByDesc("created_at")
            ->paginate(25);

        return response()->json(['data' => $data]);
    }

    public function onSave(RoleRequest $request)
    {
        Log::info("Start: Admin/RoleController > onSave | admin: " . $request);
        DB::beginTransaction();
        try {
            $id = $request->id;
            $items = [
                'display_name'         => [
                    'en'    => $request->display_name_en,
                    'km'    => $request->display_name_km,
                ],
                'name'          => strtolower(str_replace(' ', '-', $request->display_name_en)),
                'guard_name'    => 'admin',
                'status'        => $request->status,
            ];

            if ($id) {
                $data = Role::find($id);
                $data->update($items);
            } else {
                $data = Role::create($items);
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => $id ? __('form.message.update.success') : __('form.message.create.success'),
                'error' => false,
            ]);
        } catch (Exception $e) {
            DB::rollback();
            Log::error("Error: Admin/RoleController > data | message: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => __('form.message.error'),
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function updateStatus()
    {
        DB::beginTransaction();
        try {
            $data = Role::findOrFail(request('id'));
            $data->update([
                'status'    => request('status')
            ]);

            DB::commit();
            return $this->responseSuccess();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseError();
        }
    }

    public function onUpdateStatus(Request $request)
    {
        try {
            $data = Role::find($request->id);
            $original_data = $data->getOriginal();
            $data->update(['status' => $request->status]);
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

    public function fetchModulePermission(Request $request)
    {
        try {
            $role = Role::findOrFail($request->role_id);

            if (Auth::user()->role == config('dummy.user.role.super_admin')) {
                $modulePermissions = ModulePermission::with('subModules.subModules.permissions', 'permissions')
                    ->whereNull('parent_id')
                    ->orderBy('sort_no')
                    ->get();

                $rolePermissionIds = $role->permissions->pluck('id')->toArray();

                foreach ($modulePermissions as $module) {
                    $module->check = false;

                    // Top-level permissions
                    foreach ($module->permissions as $permission) {
                        $permission->check = in_array($permission->id, $rolePermissionIds);
                        if ($permission->check) $module->check = true;
                    }

                    // Submodules
                    foreach ($module->subModules as $subModule) {
                        $subModule->check = false;

                        // Submodule's own permissions
                        foreach ($subModule->permissions as $permission) {
                            $permission->check = in_array($permission->id, $rolePermissionIds);
                            if ($permission->check) {
                                $module->check = true;
                                $subModule->check = true;
                            }
                        }

                        // Grand-submodules
                        foreach ($subModule->subModules as $grandSubModule) {
                            $grandSubModule->check = false;

                            foreach ($grandSubModule->permissions as $permission) {
                                $permission->check = in_array($permission->id, $rolePermissionIds);
                                if ($permission->check) {
                                    $module->check = true;
                                    $subModule->check = true;
                                    $grandSubModule->check = true;
                                }
                            }
                        }
                    }
                }
            } else {
                // Get permission ids assigned to this user through their role
                $userRolePermissionIds = Auth::user()?->roles()?->with('permissions')->get()
                    ->pluck('permissions')->flatten()->pluck('id')->unique()->toArray();

                // If using model_has_permissions in parallel
                $directPermissionIds = ModelHasPermission::where('model_id', Auth::id())->pluck('permission_id')->toArray();

                $userPermissionIds = array_unique(array_merge($userRolePermissionIds, $directPermissionIds));

                if (empty($userPermissionIds)) {
                    return response()->json([]);
                }

                // Build modules and structure
                $pluckModules = Permission::whereIn('id', $userPermissionIds)->pluck('module_id')->toArray();
                $modules = ModulePermission::whereIn('id', $pluckModules)->whereNotNull('parent_id')->pluck('parent_id')->toArray();
                $mergeModules = array_unique(array_merge($modules, $pluckModules));

                $modulePermissions = ModulePermission::whereIn('id', $mergeModules)
                    ->whereNull('parent_id')
                    ->orderBy('sort_no')
                    ->get();

                $rolePermissionIds = $role->permissions->pluck('id')->toArray();

                foreach ($modulePermissions as $module) {
                    $module->check = false;
                    $module->permissions = Permission::whereIn('id', $userPermissionIds)
                        ->where('module_id', $module->id)->get();

                    foreach ($module->permissions as $permission) {
                        $permission->check = in_array($permission->id, $rolePermissionIds);
                        if ($permission->check) $module->check = true;
                    }

                    $module->sub_modules = ModulePermission::whereIn('id', $pluckModules)
                        ->where('parent_id', $module->id)->get();

                    foreach ($module->sub_modules as $subModule) {
                        $subModule->check = false;
                        $subModule->permissions = Permission::whereIn('id', $userPermissionIds)
                            ->where('module_id', $subModule->id)->get();

                        foreach ($subModule->permissions as $permission) {
                            $permission->check = in_array($permission->id, $rolePermissionIds);
                            if ($permission->check) {
                                $module->check = true;
                                $subModule->check = true;
                            }
                        }
                    }
                }

                return response()->json($modulePermissions);
            }

            return response()->json($modulePermissions);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => __('form.message.error'),
                'error'   => $e->getMessage(),
                'line'    => $e->getLine(),
            ]);
        }
    }

    public function onAssignPermission(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = ModelsRole::with('permissions')->findOrFail($request->role_id);
            $original_data = $data->getOriginal();
            $permissions = $request->permissions;
            $data->syncPermissions($permissions);
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => __('form.message.assign.success'),
                'error' => false,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('form.message.error'),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
