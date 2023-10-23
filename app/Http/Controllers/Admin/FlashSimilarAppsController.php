<?php

namespace App\Http\Controllers\Admin;

use App\Exports\FlashSimilarAppsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FlashSimilarApp\BulkDestroyFlashSimilarApp;
use App\Http\Requests\Admin\FlashSimilarApp\DestroyFlashSimilarApp;
use App\Http\Requests\Admin\FlashSimilarApp\IndexFlashSimilarApp;
use App\Http\Requests\Admin\FlashSimilarApp\StoreFlashSimilarApp;
use App\Http\Requests\Admin\FlashSimilarApp\UpdateFlashSimilarApp;
use App\Models\FlashSimilarApp;
use Brackets\AdminListing\Facades\AdminListing;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\View\View;

class FlashSimilarAppsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexFlashSimilarApp $request
     * @return array|Factory|View
     */
    public function index(IndexFlashSimilarApp $request)
    {
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(FlashSimilarApp::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'title', 'sub_title', 'href', 'href_web', 'href_ios', 'order_index', 'active'],

            // set columns to searchIn
            ['id', 'title', 'sub_title', 'href', 'href_web', 'href_ios']
        );

        if ($request->ajax()) {
            if ($request['page'] && $request['page'] > 0) {
                session([\Str::slug($request->getPathInfo()) => $request['page']]);
            }
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.flash-similar-app.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.flash-similar-app.create');

        return view('admin.flash-similar-app.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreFlashSimilarApp $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreFlashSimilarApp $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the FlashSimilarApp
        $flashSimilarApp = FlashSimilarApp::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/flash-similar-apps'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/flash-similar-apps');
    }

    /**
     * Display the specified resource.
     *
     * @param FlashSimilarApp $flashSimilarApp
     * @throws AuthorizationException
     * @return void
     */
    public function show(FlashSimilarApp $flashSimilarApp)
    {
        $this->authorize('admin.flash-similar-app.show', $flashSimilarApp);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param FlashSimilarApp $flashSimilarApp
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(FlashSimilarApp $flashSimilarApp)
    {
        $this->authorize('admin.flash-similar-app.edit', $flashSimilarApp);


        return view('admin.flash-similar-app.edit', [
            'flashSimilarApp' => $flashSimilarApp,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateFlashSimilarApp $request
     * @param FlashSimilarApp $flashSimilarApp
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateFlashSimilarApp $request, FlashSimilarApp $flashSimilarApp)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values FlashSimilarApp
        $flashSimilarApp->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/flash-similar-apps'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/flash-similar-apps');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyFlashSimilarApp $request
     * @param FlashSimilarApp $flashSimilarApp
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyFlashSimilarApp $request, FlashSimilarApp $flashSimilarApp)
    {
        $flashSimilarApp->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyFlashSimilarApp $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyFlashSimilarApp $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('flashSimilarApps')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }

    /**
     * Export entities
     *
     * @return BinaryFileResponse|null
     */
    public function export(): ?BinaryFileResponse
    {
        return Excel::download(app(FlashSimilarAppsExport::class), 'flashSimilarApps.xlsx');
    }
}
