<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StaticBlog\BulkDestroyStaticBlog;
use App\Http\Requests\Admin\StaticBlog\DestroyStaticBlog;
use App\Http\Requests\Admin\StaticBlog\IndexStaticBlog;
use App\Http\Requests\Admin\StaticBlog\StoreStaticBlog;
use App\Http\Requests\Admin\StaticBlog\UpdateStaticBlog;
use App\Models\StaticBlog;
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

class StaticBlogsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexStaticBlog $request
     * @return array|Factory|View
     */
    public function index(IndexStaticBlog $request)
    {
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()) . '/static-blog-search')) {
            $request['search'] = session(\Str::slug($request->getPathInfo()) . '/static-blog-search');
            $search = session(\Str::slug($request->getPathInfo()) . '/static-blog-search');
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(StaticBlog::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['active', 'author', 'description', 'id', 'keywords', 'order_index', 'short_description', 'source', 'title', 'created_at'],

            // set columns to searchIn
            ['author', 'description', 'id', 'keywords', 'short_description', 'slug', 'source', 'title']
        );

        if ($request->ajax()) {
            if ($request['page'] && $request['page'] > 0) {
                session([\Str::slug($request->getPathInfo()) => $request['page']]);
            }
            if ($request['search'] && $request['search'] != '') {
                session([\Str::slug($request->getPathInfo()) . '/static-blog-search' => $request['search']]);
            } else {
                session([\Str::slug($request->getPathInfo()) . '/static-blog-search' => '']);
            }
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data, 'search' => session(\Str::slug($request->getPathInfo()) . '/static-blog-search')];
        }

        return view('admin.static-blog.index', ['data' => $data, 'search' => session(\Str::slug($request->getPathInfo()) . '/static-blog-search')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.static-blog.create');

        return view('admin.static-blog.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreStaticBlog $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreStaticBlog $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        $sanitized = $this->arrayToString($sanitized);
        if (isset($sanitized['message']) && $sanitized['message'] != '') {
            $pattern = $sanitized['keywords'];
            return abort(400, "keywords Found '$pattern' ");
        } else {

            // Store the StaticBlog
            $staticBlog = StaticBlog::create($sanitized);

            if ($request->ajax()) {
                return ['redirect' => url('admin/static-blogs'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
            }

            return redirect('admin/static-blogs');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param StaticBlog $staticBlog
     * @throws AuthorizationException
     * @return void
     */
    public function show(StaticBlog $staticBlog)
    {
        $this->authorize('admin.static-blog.show', $staticBlog);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param StaticBlog $staticBlog
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(StaticBlog $staticBlog)
    {
        $this->authorize('admin.static-blog.edit', $staticBlog);

        if (isset($staticBlog['keywords']) && $staticBlog['keywords'] != "") {
            $staticBlog['keywords'] = explode('|', $staticBlog['keywords']);
        }


        return view('admin.static-blog.edit', [
            'staticBlog' => $staticBlog,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateStaticBlog $request
     * @param StaticBlog $staticBlog
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateStaticBlog $request, StaticBlog $staticBlog)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        $sanitized = $this->arrayToString($sanitized, $staticBlog->id);
        if (isset($sanitized['message']) && $sanitized['message'] != '') {
            $keywords = $sanitized['keywords'];
            return abort(400, "keywords Found '$keywords' ");
        } else {
            // Update changed values StaticBlog
            $staticBlog->update($sanitized);

            if ($request->ajax()) {
                return [
                    'redirect' => url('admin/static-blogs'),
                    'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
                ];
            }

            return redirect('admin/static-blogs');
        }
    }

    public function arrayToString($sanitized, $updatePattern = 0)
    {
        if (isset($sanitized['keywords']) && $sanitized['keywords'] != "") {
            if ($updatePattern != 0) {
                foreach ($sanitized['keywords'] as $item) {
                    $string = DB::select("SELECT id FROM static_blogs WHERE instr(keywords,'|$item|') and id != $updatePattern and deleted_at is NULL"); //$item space needed for perfect match
                    if (count($string) > 0) {
                        return ['message' => 'keywords Found', 'errorCode' => 400, 'keywords' => $item];
                    }
                }
            } else {
                foreach ($sanitized['keywords'] as $item) {
                    $string = DB::select("SELECT id FROM static_blogs WHERE instr(keywords,'|$item|') and deleted_at is NULL"); //$item space needed for perfect match
                    if (count($string) > 0) {
                        return ['message' => 'keywords Found', 'errorCode' => 400, 'keywords' => $item];
                    }
                }
            }
            $sanitized['keywords'] = implode("|", $sanitized['keywords']);
        }

        return $sanitized;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyStaticBlog $request
     * @param StaticBlog $staticBlog
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyStaticBlog $request, StaticBlog $staticBlog)
    {
        $staticBlog->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyStaticBlog $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyStaticBlog $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('staticBlogs')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
