<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminUser\DestroyAdminUser;
use App\Http\Requests\Admin\AdminUser\ImpersonalLoginAdminUser;
use App\Http\Requests\Admin\AdminUser\IndexAdminUser;
use App\Http\Requests\Admin\AdminUser\StoreAdminUser;
use App\Http\Requests\Admin\AdminUser\UpdateAdminUser;
use App\Models\Cadre;
use App\Models\Country;
use App\Models\District;
use Brackets\AdminAuth\Models\AdminUser;
use Spatie\Permission\Models\Role;
use App\Models\State;
use Brackets\AdminAuth\Activation\Facades\Activation;
use Brackets\AdminAuth\Services\ActivationService;
use Brackets\AdminListing\Facades\AdminListing;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Config;
use Illuminate\View\View;

class AdminUsersController extends Controller
{

    /**
     * Guard used for admin user
     *
     * @var string
     */
    protected $guard = 'admin';

    /**
     * AdminUsersController constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->guard = config('admin-auth.defaults.guard');
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexAdminUser $request
     * @return Factory|View
     */
    public function index(IndexAdminUser $request)
    {
        $states = State::get(['id', 'title']);
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(AdminUser::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'first_name', 'last_name', 'email', 'activated', 'forbidden', 'language', 'state', 'last_login_at', 'cadre', 'district', 'role_type', 'country'],

            // set columns to searchIn
            ['id', 'first_name', 'last_name', 'email', 'language', 'state'],
            function ($q) {
                $q->with(['roles']);
            }
        );

        if ($request->ajax()) {
            return ['data' => $data, 'activation' => Config::get('admin-auth.activation_enabled'), 'code' => 200];
        }

        return view('admin.admin-user.index', ['data' => $data, 'activation' => Config::get('admin-auth.activation_enabled'), 'states' => $states]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.admin-user.create');

        //show Administrator role only for SuperAdmin user
        if (\Auth::user()->id == 1) {
            $roles = Role::get();
        } else {
            $roles = Role::where('id', '>', 1)->get();
        }
        return view('admin.admin-user.create', [
            'activation' => Config::get('admin-auth.activation_enabled'),
            'roles' => $roles, //where('guard_name', $this->guard)->
            'states' => State::get(['id', 'title']),
            'country' => Country::get(['id', 'title']),
            'district' => District::get(['id', 'title', 'state_id']),
            'cadre' => Cadre::get(['id', 'title', 'cadre_type']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAdminUser $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreAdminUser $request)
    {
        // Sanitize input
        $sanitized = $request->getModifiedData($request);
        if ($request['role_type'] != 'country_type') {
            if (isset($sanitized['state']) && $sanitized['state'] != '' && isset($sanitized['state'][0]) && $sanitized['state'][0] != '') {
                // $sanitized['state'] = $sanitized['state']['id'];
                $sanitized['state'] = array_pluck($sanitized['state'], 'id');
                $sanitized['state'] = implode(',', $sanitized['state']);
            } else {
                $sanitized['state'] = $sanitized['state']['id'];
            }

            if (isset($sanitized['district']) && $sanitized['district'] != '') {
                $sanitized['district'] = array_pluck($sanitized['district'], 'id');
                $sanitized['district'] = implode(',', $sanitized['district']);
            }
        } else {
            $sanitized['state'] = '';
            $sanitized['district'] = '';
        }

        if (isset($sanitized['cadre']) && $sanitized['cadre'] != '') {
            $sanitized['cadre'] = array_pluck($sanitized['cadre'], 'id');
            $sanitized['cadre'] = implode(',', $sanitized['cadre']);
        }
        if (isset($sanitized['country']) && $sanitized['country'] != '') {
            $sanitized['country'] = $request['country']['id'];
        }
        // Store the AdminUser
        $adminUser = AdminUser::create($sanitized);

        // But we do have a roles, so we need to attach the roles to the adminUser
        // $adminUser->roles()->sync(collect($request->input('roles', []))->map->id->toArray());
        $adminUser->roles()->sync(collect($request->input('roles', []))['id']);

        if ($request->ajax()) {
            return ['redirect' => url('admin/admin-users'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/admin-users');
    }

    /**
     * Display the specified resource.
     *
     * @param AdminUser $adminUser
     * @throws AuthorizationException
     * @return void
     */
    public function show(AdminUser $adminUser)
    {
        $this->authorize('admin.admin-user.show', $adminUser);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param AdminUser $adminUser
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(AdminUser $adminUser)
    {
        //Super admin's record can updated by Super admin only.
        if ($adminUser->id == 1) {
            if (\Auth::user()->id != 1) {
                return redirect('admin/admin-users');
            }
        }
        $this->authorize('admin.admin-user.edit', $adminUser);

        $adminUser->load('roles');

        if (isset($adminUser['state']) && $adminUser['state'] != "") {
            $adminUser['state'] = explode(',', $adminUser['state']);
            $adminUser['state'] = State::whereIn('id', $adminUser['state'])->get(['id', 'title']);
        } else {
            $adminUser['state'] = State::get(['id', 'title']);
        }

        if (isset($adminUser['district']) && $adminUser['district'] != "") {
            $adminUser['district'] = explode(',', $adminUser['district']);
            $adminUser['district'] = District::whereIn('id', $adminUser['district'])->get(['id', 'title']);
        } else {
            $adminUser['district'] = District::get(['id', 'title']);
        }

        if (isset($adminUser['cadre']) && $adminUser['cadre'] != "") {
            $adminUser['cadre'] = explode(',', $adminUser['cadre']);
            $adminUser['cadre'] = Cadre::whereIn('id', $adminUser['cadre'])->get(['id', 'title']);
        }

        if (isset($adminUser['country']) && $adminUser['country'] != "") {
            $adminUser['country'] = explode(',', $adminUser['country']);
            $adminUser['country'] = Country::where('id', $adminUser['country'])->get(['id', 'title']);
        } else {
            $adminUser['country'] = [];
        }
        //show Administrator role only for Super user
        if (\Auth::user()->id == 1) {
            $roles = Role::get();
        } else {
            $roles = Role::where('id', '>', 1)->get();
        }

        return view('admin.admin-user.edit', [
            'adminUser' => $adminUser,
            'activation' => Config::get('admin-auth.activation_enabled'),
            'roles' => $roles, //where('guard_name', $this->guard)->
            'states' => State::get(['id', 'title']),
            'country' => Country::get(['id', 'title']),
            'district' => District::get(['id', 'title', 'state_id']),
            'cadre' => Cadre::get(['id', 'title', 'cadre_type']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAdminUser $request
     * @param AdminUser $adminUser
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateAdminUser $request, AdminUser $adminUser)
    {
        // Sanitize input
        $sanitized = $request->getModifiedData($request);
        if ($request['role_type'] != 'country_type') {
            if (isset($sanitized['state']) && isset($sanitized['state'][0]) && $sanitized['state'][0] != '') {
                $sanitized['state'] = array_pluck($sanitized['state'], 'id');
                $sanitized['state'] = implode(',', $sanitized['state']);
            } elseif (isset($sanitized['state']) && $sanitized['state'] != '' && $sanitized['state']['id']) {
                $sanitized['state'] = $sanitized['state']['id'];
            }

            if (isset($sanitized['district']) && isset($sanitized['district'][0]) && $sanitized['district'][0] != '') {
                $sanitized['district'] = array_pluck($sanitized['district'], 'id');
                $sanitized['district'] = implode(',', $sanitized['district']);
            } elseif (isset($sanitized['district']) && $sanitized['district'] != '' && $sanitized['district']['id']) {
                $sanitized['district'] = $sanitized['district']['id'];
            }
        } else {
            $sanitized['state'] = '';
            $sanitized['district'] = '';
        }

        if (isset($sanitized['cadre']) && isset($sanitized['cadre'][0]) && $sanitized['cadre'][0] != '') {
            $sanitized['cadre'] = array_pluck($sanitized['cadre'], 'id');
            $sanitized['cadre'] = implode(',', $sanitized['cadre']);
        } elseif (isset($sanitized['cadre']) && $sanitized['cadre'] != '' && $sanitized['cadre']['id']) {
            $sanitized['cadre'] = $sanitized['cadre']['id'];
        }

        if (isset($sanitized['country']) && isset($sanitized['country'][0]) && $sanitized['country'][0] != '') {
            $sanitized['country'] = array_pluck($sanitized['country'], 'id');
            $sanitized['country'] = implode(',', $sanitized['country']);
        } elseif (isset($sanitized['country']) && $sanitized['country'] != '' && count($sanitized['country']) > 0 && $sanitized['country']['id']) {
            $sanitized['country'] = $sanitized['country']['id'];
        }
        // Update changed values AdminUser
        $adminUser->update($sanitized);

        // But we do have a roles, so we need to attach the roles to the adminUser
        if ($request->input('roles') && isset($request->input('roles')['id'])) {
            $adminUser->roles()->sync(collect($request->input('roles', []))['id']);
            // $adminUser->roles()->sync(collect($request->input('roles', []))->map->id->toArray());
        } else {
            $adminUser->roles()->sync(collect($request->input('roles', []))[0]['id']);
        }

        if ($request->ajax()) {
            return ['redirect' => url('admin/admin-users'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/admin-users');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyAdminUser $request
     * @param AdminUser $adminUser
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyAdminUser $request, AdminUser $adminUser)
    {
        $adminUser->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Resend activation e-mail
     *
     * @param Request $request
     * @param ActivationService $activationService
     * @param AdminUser $adminUser
     * @return array|RedirectResponse
     */
    public function resendActivationEmail(Request $request, ActivationService $activationService, AdminUser $adminUser)
    {
        if (Config::get('admin-auth.activation_enabled')) {
            $response = $activationService->handle($adminUser);
            if ($response == Activation::ACTIVATION_LINK_SENT) {
                if ($request->ajax()) {
                    return ['message' => trans('brackets/admin-ui::admin.operation.succeeded')];
                }

                return redirect()->back();
            } else {
                if ($request->ajax()) {
                    abort(409, trans('brackets/admin-ui::admin.operation.failed'));
                }

                return redirect()->back();
            }
        } else {
            if ($request->ajax()) {
                abort(400, trans('brackets/admin-ui::admin.operation.not_allowed'));
            }

            return redirect()->back();
        }
    }

    /**
     * @param ImpersonalLoginAdminUser $request
     * @param AdminUser $adminUser
     * @return RedirectResponse
     * @throws  AuthorizationException
     */
    public function impersonalLogin(ImpersonalLoginAdminUser $request, AdminUser $adminUser)
    {
        Auth::login($adminUser);
        return redirect()->back();
    }
}
