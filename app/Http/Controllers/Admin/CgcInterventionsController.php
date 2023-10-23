<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CgcIntervention\BulkDestroyCgcIntervention;
use App\Http\Requests\Admin\CgcIntervention\DestroyCgcIntervention;
use App\Http\Requests\Admin\CgcIntervention\IndexCgcIntervention;
use App\Http\Requests\Admin\CgcIntervention\StoreCgcIntervention;
use App\Http\Requests\Admin\CgcIntervention\UpdateCgcIntervention;
use App\Models\CgcIntervention;
use App\Models\Assessment;
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
use Illuminate\View\View;

class CgcInterventionsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexCgcIntervention $request
     * @return array|Factory|View
     */
    public function index(IndexCgcIntervention $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(CgcIntervention::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'chapter_title', 'video_title', 'description', 'reference_title', 'created_at'],

            // set columns to searchIn
            ['id', 'chapter_title', 'video_title', 'description', 'reference_title']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.cgc-intervention.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.cgc-intervention.create');
        $assessment = Assessment::get(['id', 'assessment_title']);
        return view('admin.cgc-intervention.create', [
            'assessment' => $assessment,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCgcIntervention $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreCgcIntervention $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the CgcIntervention
        $cgcIntervention = CgcIntervention::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/cgc-interventions'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/cgc-interventions');
    }

    /**
     * Display the specified resource.
     *
     * @param CgcIntervention $cgcIntervention
     * @throws AuthorizationException
     * @return void
     */
    public function show(CgcIntervention $cgcIntervention)
    {
        $this->authorize('admin.cgc-intervention.show', $cgcIntervention);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param CgcIntervention $cgcIntervention
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(CgcIntervention $cgcIntervention)
    {
        $this->authorize('admin.cgc-intervention.edit', $cgcIntervention);
        $assessment = Assessment::get(['id', 'assessment_title']);

        return view('admin.cgc-intervention.edit', [
            'assessment' => $assessment,
            'cgcIntervention' => $cgcIntervention,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCgcIntervention $request
     * @param CgcIntervention $cgcIntervention
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateCgcIntervention $request, CgcIntervention $cgcIntervention)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values CgcIntervention
        $cgcIntervention->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/cgc-interventions'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/cgc-interventions');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyCgcIntervention $request
     * @param CgcIntervention $cgcIntervention
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyCgcIntervention $request, CgcIntervention $cgcIntervention)
    {
        $cgcIntervention->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyCgcIntervention $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyCgcIntervention $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('cgcInterventions')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
