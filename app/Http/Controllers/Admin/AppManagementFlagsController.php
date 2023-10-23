<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AppManagementFlag\BulkDestroyAppManagementFlag;
use App\Http\Requests\Admin\AppManagementFlag\DestroyAppManagementFlag;
use App\Http\Requests\Admin\AppManagementFlag\IndexAppManagementFlag;
use App\Http\Requests\Admin\AppManagementFlag\StoreAppManagementFlag;
use App\Http\Requests\Admin\AppManagementFlag\UpdateAppManagementFlag;
use App\Models\AppManagementFlag;
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

class AppManagementFlagsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexAppManagementFlag $request
     * @return array|Factory|View
     */
    public function index(IndexAppManagementFlag $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(AppManagementFlag::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'variable', 'value', 'type', 'created_at'],

            // set columns to searchIn
            ['id', 'variable', 'value', 'type']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.app-management-flag.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.app-management-flag.create');

        return view('admin.app-management-flag.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAppManagementFlag $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreAppManagementFlag $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        if (isset($sanitized['type']) && $sanitized['type'] == "list") {
            $sanitized['value']['en'] = implode("|", $sanitized['value']['en']);
            $sanitized['value']['hi'] = isset($sanitized['value']['hi']) ? implode("|", $sanitized['value']['hi']) : NULL;
            $sanitized['value']['gu'] = isset($sanitized['value']['gu']) ? implode("|", $sanitized['value']['gu']) : NULL;
            $sanitized['value']['mr'] = isset($sanitized['value']['mr']) ? implode("|", $sanitized['value']['mr']) : NULL;
        }
        // Store the AppManagementFlag
        $appManagementFlag = AppManagementFlag::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/app-management-flags'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/app-management-flags');
    }

    /**
     * Display the specified resource.
     *
     * @param AppManagementFlag $appManagementFlag
     * @throws AuthorizationException
     * @return void
     */
    public function show(AppManagementFlag $appManagementFlag)
    {
        $this->authorize('admin.app-management-flag.show', $appManagementFlag);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param AppManagementFlag $appManagementFlag
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(AppManagementFlag $appManagementFlag)
    {
        $this->authorize('admin.app-management-flag.edit', $appManagementFlag);
        if (isset($appManagementFlag['value']) && $appManagementFlag['value'] != "") {

            $response_value = $appManagementFlag->getTranslations('value');
            $appManagementFlag['value'] = ["en" => isset($response_value['en']) && $response_value['en'] != [] ? explode('|', $response_value['en']) : NULL, 'hi' => isset($response_value['hi']) ? explode('|', $response_value['hi']) : NULL, 'gu' => isset($response_value['gu']) ? explode('|', $response_value['gu']) : NULL, 'mr' => isset($response_value['mr']) ? explode('|', $response_value['mr']) : NULL, 'ta' => isset($response_value['ta']) ? explode('|', $response_value['ta']) : NULL];
        }

        return view('admin.app-management-flag.edit', [
            'appManagementFlag' => $appManagementFlag,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAppManagementFlag $request
     * @param AppManagementFlag $appManagementFlag
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateAppManagementFlag $request, AppManagementFlag $appManagementFlag)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        if (isset($sanitized['type']) && $sanitized['type'] == "list") {
            $sanitized['value']['en'] = implode("|", $sanitized['value']['en']);
            $sanitized['value']['hi'] = isset($sanitized['value']['hi']) ? implode("|", $sanitized['value']['hi']) : NULL;
            $sanitized['value']['gu'] = isset($sanitized['value']['gu']) ? implode("|", $sanitized['value']['gu']) : NULL;
            $sanitized['value']['mr'] = isset($sanitized['value']['mr']) ? implode("|", $sanitized['value']['mr']) : NULL;
        }
        // Update changed values AppManagementFlag
        $appManagementFlag->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/app-management-flags'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/app-management-flags');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyAppManagementFlag $request
     * @param AppManagementFlag $appManagementFlag
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyAppManagementFlag $request, AppManagementFlag $appManagementFlag)
    {
        $appManagementFlag->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyAppManagementFlag $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyAppManagementFlag $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    AppManagementFlag::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
