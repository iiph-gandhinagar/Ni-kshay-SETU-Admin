<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Block\BulkDestroyBlock;
use App\Http\Requests\Admin\Block\DestroyBlock;
use App\Http\Requests\Admin\Block\IndexBlock;
use App\Http\Requests\Admin\Block\StoreBlock;
use App\Http\Requests\Admin\Block\UpdateBlock;
use App\Models\Block;
use App\Models\Country;
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

class BlocksController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexBlock $request
     * @return array|Factory|View
     */
    public function index(IndexBlock $request)
    {
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()) . '/block-search')) {
            $request['search'] = session(\Str::slug($request->getPathInfo()) . '/block-search');
            $search = session(\Str::slug($request->getPathInfo()) . '/block-search');
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Block::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'state_id', 'district_id', 'title', 'country_id'],

            // set columns to searchIn
            ['id', 'title', 'state.title', 'districts.title'],

            function ($query) use ($request) {
                $query->with(['state', 'district', 'country']);

                $assignedState = \Auth::user()->state;
                $assignedDistrict = \Auth::user()->district;

                if ($assignedState != '' && $assignedState > 0) {
                    $query->whereIn('blocks.state_id', explode(',', $assignedState));
                }
                if ($assignedDistrict != '' && $assignedDistrict > 0) {
                    $query->whereIn('blocks.district_id', explode(',', $assignedDistrict));
                }

                // add this line if you want to search by author attributes
                $query->join('state', 'state.id', '=', 'blocks.state_id');
                $query->join('districts', 'districts.id', '=', 'blocks.district_id');
                $query->leftJoin('country', 'country.id', '=', 'blocks.country_id');
            }
        );

        if ($request->ajax()) {
            if ($request['page'] && $request['page'] > 0) {
                session([\Str::slug($request->getPathInfo()) => $request['page']]);
            }
            if ($request['search'] && $request['search'] != '') {
                session([\Str::slug($request->getPathInfo()) . '/block-search' => $request['search']]);
            } else {
                session([\Str::slug($request->getPathInfo()) . '/block-search' => '']);
            }
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data, 'search' => session(\Str::slug($request->getPathInfo()) . '/block-search')];
        }

        return view('admin.block.index', ['data' => $data, 'search' => session(\Str::slug($request->getPathInfo()) . '/block-search')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.block.create');
        $masterData = \StateWiseFilterData::getStateWiseMasterData();
        $state = $masterData['state'];
        $district = $masterData['district'];
        $country = Country::get(['id', 'title']);

        return view('admin.block.create', [
            'state' => $state,
            'district' => $district,
            'country' => $country,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreBlock $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreBlock $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        if (isset($request['country_id']) && $request['country_id'] != '') {

            $sanitized['country_id'] = $request['country_id']['id'];
        }
        // Store the Block
        $block = Block::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/blocks'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/blocks');
    }

    /**
     * Display the specified resource.
     *
     * @param Block $block
     * @throws AuthorizationException
     * @return void
     */
    public function show(Block $block)
    {
        $this->authorize('admin.block.show', $block);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Block $block
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Block $block)
    {
        $this->authorize('admin.block.edit', $block);
        $masterData = \StateWiseFilterData::getStateWiseMasterData();
        $state = $masterData['state'];
        $district = $masterData['district'];
        $country = Country::get(['id', 'title']);
        if (isset($block['country_id']) && $block['country_id'] != "") {
            $block['country_id'] = $block['country_id'];
            $block['country_id'] = Country::where('id', $block['country_id'])->get(['id', 'title']);
        }

        return view('admin.block.edit', [
            'block' => $block,
            'state' => $state,
            'district' => $district,
            'country' => $country,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateBlock $request
     * @param Block $block
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateBlock $request, Block $block)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        if (isset($request['country_id'])) {
            if ($request['country_id'] == NULL) {
                $sanitized['country_id'] = 0;
            } else {
                $sanitized['country_id'] = $request['country_id'][0]['id'];
            }
        }


        // Update changed values Block
        $block->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/blocks'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/blocks');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyBlock $request
     * @param Block $block
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyBlock $request, Block $block)
    {
        $block->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyBlock $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyBlock $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('blocks')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
