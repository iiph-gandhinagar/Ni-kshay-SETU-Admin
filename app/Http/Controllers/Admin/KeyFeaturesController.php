<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\KeyFeature\BulkDestroyKeyFeature;
use App\Http\Requests\Admin\KeyFeature\DestroyKeyFeature;
use App\Http\Requests\Admin\KeyFeature\IndexKeyFeature;
use App\Http\Requests\Admin\KeyFeature\StoreKeyFeature;
use App\Http\Requests\Admin\KeyFeature\UpdateKeyFeature;
use App\Models\KeyFeature;
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

class KeyFeaturesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexKeyFeature $request
     * @return array|Factory|View
     */
    public function index(IndexKeyFeature $request)
    {
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(KeyFeature::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['active', 'description', 'id', 'order_index', 'title','created_at'],

            // set columns to searchIn
            ['description', 'id', 'title']
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

        return view('admin.key-feature.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.key-feature.create');

        return view('admin.key-feature.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreKeyFeature $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreKeyFeature $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the KeyFeature
        $keyFeature = KeyFeature::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/key-features'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/key-features');
    }

    /**
     * Display the specified resource.
     *
     * @param KeyFeature $keyFeature
     * @throws AuthorizationException
     * @return void
     */
    public function show(KeyFeature $keyFeature)
    {
        $this->authorize('admin.key-feature.show', $keyFeature);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param KeyFeature $keyFeature
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(KeyFeature $keyFeature)
    {
        $this->authorize('admin.key-feature.edit', $keyFeature);


        return view('admin.key-feature.edit', [
            'keyFeature' => $keyFeature,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateKeyFeature $request
     * @param KeyFeature $keyFeature
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateKeyFeature $request, KeyFeature $keyFeature)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values KeyFeature
        $keyFeature->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/key-features'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/key-features');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyKeyFeature $request
     * @param KeyFeature $keyFeature
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyKeyFeature $request, KeyFeature $keyFeature)
    {
        $keyFeature->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyKeyFeature $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyKeyFeature $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('keyFeatures')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
