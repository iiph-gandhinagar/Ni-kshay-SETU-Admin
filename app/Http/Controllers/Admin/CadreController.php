<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Cadre\BulkDestroyCadre;
use App\Http\Requests\Admin\Cadre\DestroyCadre;
use App\Http\Requests\Admin\Cadre\IndexCadre;
use App\Http\Requests\Admin\Cadre\StoreCadre;
use App\Http\Requests\Admin\Cadre\UpdateCadre;
use App\Models\Cadre;
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

class CadreController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexCadre $request
     * @return array|Factory|View
     */
    public function index(IndexCadre $request)
    {
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()) . '/cadre-search')) {
            $request['search'] = session(\Str::slug($request->getPathInfo()) . '/cadre-search');
            $search = session(\Str::slug($request->getPathInfo()) . '/cadre-search');
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Cadre::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'title', 'cadre_type'],

            // set columns to searchIn
            ['id', 'title', 'cadre_type']
        );

        if ($request->ajax()) {
            if ($request['page'] && $request['page'] > 0) {
                session([\Str::slug($request->getPathInfo()) => $request['page']]);
            }
            if ($request['search'] && $request['search'] != '') {
                session([\Str::slug($request->getPathInfo()) . '/cadre-search' => $request['search']]);
            } else {
                session([\Str::slug($request->getPathInfo()) . '/cadre-search' => '']);
            }
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data, 'search' => session(\Str::slug($request->getPathInfo()) . '/cadre-search')];
        }

        return view('admin.cadre.index', ['data' => $data, 'search' => session(\Str::slug($request->getPathInfo()) . '/cadre-search')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.cadre.create');

        return view('admin.cadre.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCadre $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreCadre $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Cadre
        $cadre = Cadre::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/cadres'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/cadres');
    }

    /**
     * Display the specified resource.
     *
     * @param Cadre $cadre
     * @throws AuthorizationException
     * @return void
     */
    public function show(Cadre $cadre)
    {
        $this->authorize('admin.cadre.show', $cadre);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Cadre $cadre
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Cadre $cadre)
    {
        $this->authorize('admin.cadre.edit', $cadre);


        return view('admin.cadre.edit', [
            'cadre' => $cadre,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCadre $request
     * @param Cadre $cadre
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateCadre $request, Cadre $cadre)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        // Update changed values Cadre
        $cadre->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/cadres'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/cadres');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyCadre $request
     * @param Cadre $cadre
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyCadre $request, Cadre $cadre)
    {
        $cadre->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyCadre $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyCadre $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Cadre::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
