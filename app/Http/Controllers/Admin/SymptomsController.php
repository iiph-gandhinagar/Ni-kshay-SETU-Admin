<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Symptom\BulkDestroySymptom;
use App\Http\Requests\Admin\Symptom\DestroySymptom;
use App\Http\Requests\Admin\Symptom\IndexSymptom;
use App\Http\Requests\Admin\Symptom\StoreSymptom;
use App\Http\Requests\Admin\Symptom\UpdateSymptom;
use App\Models\Symptom;
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

class SymptomsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexSymptom $request
     * @return array|Factory|View
     */
    public function index(IndexSymptom $request)
    {
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()) . '/symptom-search')) {
            $request['search'] = session(\Str::slug($request->getPathInfo()) . '/symptom-search');
            $search = session(\Str::slug($request->getPathInfo()) . '/symptom-search');
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Symptom::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'category', 'symptoms_title','created_at'],

            // set columns to searchIn
            ['id', 'category', 'symptoms_title']
        );
        // $this->setTranslate();
        if ($request->ajax()) {
            if ($request['page'] && $request['page'] > 0) {
                session([\Str::slug($request->getPathInfo()) => $request['page']]);
            }
            if ($request['search'] && $request['search'] != '') {
                session([\Str::slug($request->getPathInfo()) . '/symptom-search' => $request['search']]);
            } else {
                session([\Str::slug($request->getPathInfo()) . '/symptom-search' => '']);
            }
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data,'search' => session(\Str::slug($request->getPathInfo()) . '/symptom-search')];
        }

        return view('admin.symptom.index', ['data' => $data,'search' => session(\Str::slug($request->getPathInfo()) . '/symptom-search')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.symptom.create');

        return view('admin.symptom.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreSymptom $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreSymptom $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Symptom
        $symptom = Symptom::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/symptoms'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/symptoms');
    }

    /**
     * Display the specified resource.
     *
     * @param Symptom $symptom
     * @throws AuthorizationException
     * @return void
     */
    public function show(Symptom $symptom)
    {
        $this->authorize('admin.symptom.show', $symptom);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Symptom $symptom
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Symptom $symptom)
    {
        $this->authorize('admin.symptom.edit', $symptom);


        return view('admin.symptom.edit', [
            'symptom' => $symptom,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateSymptom $request
     * @param Symptom $symptom
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateSymptom $request, Symptom $symptom)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Symptom
        $symptom->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/symptoms'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/symptoms');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroySymptom $request
     * @param Symptom $symptom
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroySymptom $request, Symptom $symptom)
    {
        $symptom->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroySymptom $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroySymptom $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('symptoms')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }

    public function setTranslate(){
        $symptom = Symptom::get();

        foreach ($symptom as $key => $symptoms) {
            $symptoms = Symptom::find($symptoms->id);
            $symptoms->setTranslation('symptoms_title_json', 'en', $symptom[$key]->symptoms_title)->save();
        }
    }
}
