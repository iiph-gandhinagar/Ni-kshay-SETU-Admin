<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AutomaticNotificationsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AutomaticNotification\BulkDestroyAutomaticNotification;
use App\Http\Requests\Admin\AutomaticNotification\DestroyAutomaticNotification;
use App\Http\Requests\Admin\AutomaticNotification\IndexAutomaticNotification;
use App\Http\Requests\Admin\AutomaticNotification\StoreAutomaticNotification;
use App\Http\Requests\Admin\AutomaticNotification\UpdateAutomaticNotification;
use App\Models\AutomaticNotification;
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

class AutomaticNotificationsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexAutomaticNotification $request
     * @return array|Factory|View
     */
    public function index(IndexAutomaticNotification $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(AutomaticNotification::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['description', 'id', 'linking_url', 'subscriber_id', 'title', 'type','created_by', 'created_at','successful_count','failed_count', 'status'],

            // set columns to searchIn
            ['description', 'id', 'linking_url', 'subscriber_id', 'title', 'type','created_by','created_at','successful_count','failed_count', 'status'],
            function ($query) {
                $query->with(['admin_user']);
            }
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.automatic-notification.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.automatic-notification.create');

        return view('admin.automatic-notification.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAutomaticNotification $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreAutomaticNotification $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        $sanitized['created_by'] = \Auth::user()->id;
        // Store the AutomaticNotification
        $automaticNotification = AutomaticNotification::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/automatic-notifications'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/automatic-notifications');
    }

    /**
     * Display the specified resource.
     *
     * @param AutomaticNotification $automaticNotification
     * @throws AuthorizationException
     * @return void
     */
    public function show(AutomaticNotification $automaticNotification)
    {
        $this->authorize('admin.automatic-notification.show', $automaticNotification);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param AutomaticNotification $automaticNotification
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(AutomaticNotification $automaticNotification)
    {
        $this->authorize('admin.automatic-notification.edit', $automaticNotification);


        return view('admin.automatic-notification.edit', [
            'automaticNotification' => $automaticNotification,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAutomaticNotification $request
     * @param AutomaticNotification $automaticNotification
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateAutomaticNotification $request, AutomaticNotification $automaticNotification)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values AutomaticNotification
        $automaticNotification->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/automatic-notifications'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/automatic-notifications');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyAutomaticNotification $request
     * @param AutomaticNotification $automaticNotification
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyAutomaticNotification $request, AutomaticNotification $automaticNotification)
    {
        $automaticNotification->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyAutomaticNotification $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyAutomaticNotification $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('automaticNotifications')->whereIn('id', $bulkChunk)
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
        return Excel::download(app(AutomaticNotificationsExport::class), 'automaticNotifications.xlsx');
    }
}
