<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StaticRelease\BulkDestroyStaticRelease;
use App\Http\Requests\Admin\StaticRelease\DestroyStaticRelease;
use App\Http\Requests\Admin\StaticRelease\IndexStaticRelease;
use App\Http\Requests\Admin\StaticRelease\StoreStaticRelease;
use App\Http\Requests\Admin\StaticRelease\UpdateStaticRelease;
use App\Models\StaticRelease;
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
use Log;

class StaticReleasesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexStaticRelease $request
     * @return array|Factory|View
     */
    public function index(IndexStaticRelease $request)
    {
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()) . '/static-release-search')) {
            $request['search'] = session(\Str::slug($request->getPathInfo()) . '/static-release-search');
            $search = session(\Str::slug($request->getPathInfo()) . '/static-release-search');
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(StaticRelease::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['active', 'bugs_fix', 'date', 'features', 'id', 'order_index', 'created_at'],

            // set columns to searchIn
            ['bugs_fix', 'date', 'features', 'id']
        );

        if ($request->ajax()) {
            if ($request['page'] && $request['page'] > 0) {
                session([\Str::slug($request->getPathInfo()) => $request['page']]);
            }
            if ($request['search'] && $request['search'] != '') {
                session([\Str::slug($request->getPathInfo()) . '/static-release-search' => $request['search']]);
            } else {
                session([\Str::slug($request->getPathInfo()) . '/static-release-search' => '']);
            }
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data, 'search' => session(\Str::slug($request->getPathInfo()) . '/static-release-search')];
        }

        return view('admin.static-release.index', ['data' => $data, 'search' => session(\Str::slug($request->getPathInfo()) . '/static-release-search')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.static-release.create');

        return view('admin.static-release.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreStaticRelease $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreStaticRelease $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        $sanitized['features'] = isset($request['features']['en']) ? implode("|", $request['features']['en']) : null;
        $sanitized['bugs_fix'] = isset($request['bugs_fix']['en']) ? implode("|", $request['bugs_fix']['en']) : null;
        // Store the StaticRelease
        $staticRelease = StaticRelease::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/static-releases'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/static-releases');
    }

    /**
     * Display the specified resource.
     *
     * @param StaticRelease $staticRelease
     * @throws AuthorizationException
     * @return void
     */
    public function show(StaticRelease $staticRelease)
    {
        $this->authorize('admin.static-release.show', $staticRelease);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param StaticRelease $staticRelease
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(StaticRelease $staticRelease)
    {
        $this->authorize('admin.static-release.edit', $staticRelease);
        if (isset($staticRelease['features']) && $staticRelease['features'] != "") {
            $features_value = $staticRelease->getTranslations('features');
            $staticRelease['features'] = ["en" => isset($features_value['en']) ? explode('|', $features_value['en']) : NULL];
        }
        if (isset($staticRelease['bugs_fix']) && $staticRelease['bugs_fix'] != "") {
            $bug_fix_value = $staticRelease->getTranslations('bugs_fix');
            $staticRelease['bugs_fix'] = ["en" => isset($bug_fix_value['en']) ? explode('|', $bug_fix_value['en']) : NULL];
        }
        return view('admin.static-release.edit', [
            'staticRelease' => $staticRelease,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateStaticRelease $request
     * @param StaticRelease $staticRelease
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateStaticRelease $request, StaticRelease $staticRelease)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        if (isset($request['features']) && isset($request['features']['en']) && $request['features']['en'] != "") {
            $sanitized['features'] = implode("|", $request['features']['en']);
        }
        if (isset($request['bugs_fix']) && isset($request['bugs_fix']['en']) && $request['bugs_fix']['en'] != "") {
            $sanitized['bugs_fix'] = implode("|", $request['bugs_fix']['en']);
        }
        // Update changed values StaticRelease
        $staticRelease->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/static-releases'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/static-releases');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyStaticRelease $request
     * @param StaticRelease $staticRelease
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyStaticRelease $request, StaticRelease $staticRelease)
    {
        $staticRelease->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyStaticRelease $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyStaticRelease $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('staticReleases')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
