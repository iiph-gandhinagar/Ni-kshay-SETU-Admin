<?php

namespace App\Http\Controllers\Admin;

use App\Exports\FlashNewsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FlashNews\BulkDestroyFlashNews;
use App\Http\Requests\Admin\FlashNews\DestroyFlashNews;
use App\Http\Requests\Admin\FlashNews\IndexFlashNews;
use App\Http\Requests\Admin\FlashNews\StoreFlashNews;
use App\Http\Requests\Admin\FlashNews\UpdateFlashNews;
use App\Models\FlashNews;
use Brackets\AdminListing\Facades\AdminListing;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\View\View;

class FlashNewsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexFlashNews $request
     * @return array|Factory|View
     */
    public function index(IndexFlashNews $request)
    {
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(FlashNews::class)->modifyQuery(function ($query) use ($request) {
            if ($request->has('from_date') && $request['from_date'] != '') {
                $query->whereDate('created_at', '>=', date('Y-m-d', strtotime($request->from_date)));
            }
            if ($request->has('to_date') && $request['to_date'] != '') {
                $query->whereDate('created_at', '<=', date('Y-m-d', strtotime($request->to_date)));
            }
        })->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['active', 'author', 'description', 'href', 'id', 'order_index', 'publish_date', 'source', 'title', 'created_at'],

            // set columns to searchIn
            ['author', 'description', 'href', 'id', 'publish_date', 'source', 'title']
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

        return view('admin.flash-news.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.flash-news.create');

        return view('admin.flash-news.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreFlashNews $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreFlashNews $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the FlashNews
        $flashNews = FlashNews::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/flash-news'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/flash-news');
    }

    /**
     * Display the specified resource.
     *
     * @param FlashNews $flashNews
     * @throws AuthorizationException
     * @return void
     */
    public function show(FlashNews $flashNews)
    {
        $this->authorize('admin.flash-news.show', $flashNews);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param FlashNews $flashNews
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(FlashNews $flashNews)
    {
        $this->authorize('admin.flash-news.edit', $flashNews);


        return view('admin.flash-news.edit', [
            'flashNews' => $flashNews,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateFlashNews $request
     * @param FlashNews $flashNews
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateFlashNews $request, FlashNews $flashNews)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values FlashNews
        $flashNews->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/flash-news'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/flash-news');
    }

    public function activeFlag(Request $request, FlashNews $flashNews)
    {
        $flashNews->update(['active' => $request['active']]);
        if ($request->ajax()) {
            return [
                'redirect' => url('admin/flash-news'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/flash-news');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyFlashNews $request
     * @param FlashNews $flashNews
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyFlashNews $request, FlashNews $flashNews)
    {
        $flashNews->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyFlashNews $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyFlashNews $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('flashNews')->whereIn('id', $bulkChunk)
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
    public function export(Request $request): ?BinaryFileResponse
    {
        return Excel::download(new FlashNewsExport($request), 'flashNews.xlsx');
    }
}
