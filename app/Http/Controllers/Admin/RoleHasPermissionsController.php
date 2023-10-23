<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleHasPermission\BulkDestroyRoleHasPermission;
use App\Http\Requests\Admin\RoleHasPermission\DestroyRoleHasPermission;
use App\Http\Requests\Admin\RoleHasPermission\IndexRoleHasPermission;
use App\Http\Requests\Admin\RoleHasPermission\StoreRoleHasPermission;
use App\Http\Requests\Admin\RoleHasPermission\UpdateRoleHasPermission;
use App\Models\RoleHasPermission;
use App\Models\Role;
use App\Models\Permission;
use Brackets\AdminListing\Facades\AdminListing;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RoleHasPermissionsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexRoleHasPermission $request
     * @return array|Factory|View
     */
    public function index(IndexRoleHasPermission $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(RoleHasPermission::class)
            ->modifyQuery(function ($query) use ($request) {
                if ($request->orderBy == 'id') {
                    $request['orderBy'] = 'permission_id';
                }
            })
            ->processRequestAndGet(
                // pass the request with params
                $request,

                // set columns to query
                ['permission_id', 'role_id'],

                // set columns to searchIn
                ['permission_id', 'role_id', 'permissions.name', 'roles.name'],

                function ($query) use ($request) {
                    $query->with(['permission', 'role']);

                    // add this line if you want to search by author attributes
                    $query->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id');
                    $query->join('roles', 'roles.id', '=', 'role_has_permissions.role_id');
                },
            );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.role-has-permission.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $roles = Role::distinct()->get(['id', 'name']);
        $permissions = Permission::distinct()->get(['id', 'name']);
        $this->authorize('admin.role-has-permission.create');

        return view('admin.role-has-permission.create', [
            'roles' => $roles,
            'permissions' => $permissions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRoleHasPermission $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreRoleHasPermission $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        foreach ($request['permission_id'] as $permission) {
            $roleHasPermission = RoleHasPermission::create(['permission_id' => $permission['id'], 'role_id' => $request['role_id']]);
        }
        // Store the RoleHasPermission
        \Artisan::call('config:cache');
        \Artisan::call('optimize:clear');
        if ($request->ajax()) {
            return ['redirect' => url('admin/role-has-permissions'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/role-has-permissions');
    }

    /**
     * Display the specified resource.
     *
     * @param RoleHasPermission $roleHasPermission
     * @throws AuthorizationException
     * @return void
     */
    public function show(RoleHasPermission $roleHasPermission)
    {
        $this->authorize('admin.role-has-permission.show', $roleHasPermission);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param RoleHasPermission $roleHasPermission
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(RoleHasPermission $roleHasPermission)
    {
        $this->authorize('admin.role-has-permission.edit', $roleHasPermission);

        $roles = Role::distinct()->get(['id', 'name']);
        $permissions = Permission::distinct()->get(['id', 'name']);

        return view('admin.role-has-permission.edit', [
            'roleHasPermission' => $roleHasPermission,
            'roles' => $roles,
            'permissions' => $permissions
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRoleHasPermission $request
     * @param RoleHasPermission $roleHasPermission
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateRoleHasPermission $request, RoleHasPermission $roleHasPermission)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values RoleHasPermission
        $roleHasPermission->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/role-has-permissions'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/role-has-permissions');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyRoleHasPermission $request
     * @param RoleHasPermission $roleHasPermission
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyRoleHasPermission $request, RoleHasPermission $roleHasPermission)
    {
        $roleHasPermission->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyRoleHasPermission $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyRoleHasPermission $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    RoleHasPermission::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
