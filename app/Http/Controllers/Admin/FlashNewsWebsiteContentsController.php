<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FlashNewsWebsiteContent\BulkDestroyFlashNewsWebsiteContent;
use App\Http\Requests\Admin\FlashNewsWebsiteContent\DestroyFlashNewsWebsiteContent;
use App\Http\Requests\Admin\FlashNewsWebsiteContent\IndexFlashNewsWebsiteContent;
use App\Http\Requests\Admin\FlashNewsWebsiteContent\StoreFlashNewsWebsiteContent;
use App\Http\Requests\Admin\FlashNewsWebsiteContent\UpdateFlashNewsWebsiteContent;
use App\Models\FlashNewsWebsiteContent;
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

class FlashNewsWebsiteContentsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexFlashNewsWebsiteContent $request
     * @return array|Factory|View
     */
    public function index(IndexFlashNewsWebsiteContent $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(FlashNewsWebsiteContent::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'title', 'source', 'href', 'author', 'publish_date'],

            // set columns to searchIn
            ['id', 'title', 'source', 'href', 'author', 'publish_date']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.flash-news-website-content.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.flash-news-website-content.create');

        return view('admin.flash-news-website-content.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreFlashNewsWebsiteContent $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreFlashNewsWebsiteContent $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the FlashNewsWebsiteContent
        $flashNewsWebsiteContent = FlashNewsWebsiteContent::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/flash-news-website-contents'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/flash-news-website-contents');
    }

    /**
     * Display the specified resource.
     *
     * @param FlashNewsWebsiteContent $flashNewsWebsiteContent
     * @throws AuthorizationException
     * @return void
     */
    public function show(FlashNewsWebsiteContent $flashNewsWebsiteContent)
    {
        $this->authorize('admin.flash-news-website-content.show', $flashNewsWebsiteContent);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param FlashNewsWebsiteContent $flashNewsWebsiteContent
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(FlashNewsWebsiteContent $flashNewsWebsiteContent)
    {
        $this->authorize('admin.flash-news-website-content.edit', $flashNewsWebsiteContent);


        return view('admin.flash-news-website-content.edit', [
            'flashNewsWebsiteContent' => $flashNewsWebsiteContent,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateFlashNewsWebsiteContent $request
     * @param FlashNewsWebsiteContent $flashNewsWebsiteContent
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateFlashNewsWebsiteContent $request, FlashNewsWebsiteContent $flashNewsWebsiteContent)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values FlashNewsWebsiteContent
        $flashNewsWebsiteContent->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/flash-news-website-contents'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/flash-news-website-contents');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyFlashNewsWebsiteContent $request
     * @param FlashNewsWebsiteContent $flashNewsWebsiteContent
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyFlashNewsWebsiteContent $request, FlashNewsWebsiteContent $flashNewsWebsiteContent)
    {
        $flashNewsWebsiteContent->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyFlashNewsWebsiteContent $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyFlashNewsWebsiteContent $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('flashNewsWebsiteContents')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
