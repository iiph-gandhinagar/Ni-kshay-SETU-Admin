<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChatKeyword\BulkDestroyChatKeyword;
use App\Http\Requests\Admin\ChatKeyword\DestroyChatKeyword;
use App\Http\Requests\Admin\ChatKeyword\IndexChatKeyword;
use App\Http\Requests\Admin\ChatKeyword\StoreChatKeyword;
use App\Http\Requests\Admin\ChatKeyword\UpdateChatKeyword;
use App\Models\ChatKeyword;
use App\Models\TModuleMaster;
use App\Models\TSubModuleMaster;
use App\Models\ResourceMaterial;
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

class ChatKeywordsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexChatKeyword $request
     * @return array|Factory|View
     */
    public function index(IndexChatKeyword $request)
    {
        // if(!$request->ajax() && $request->session()->has('orderDirectionKeyword')){
        //     $request['orderDirection'] = session('orderDirectionKeyword');  
        //     $request['orderBy'] = session('orderByKeyword');
        // }

        if(!$request->ajax() && session(\Str::slug($request->getPathInfo()))){
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()) . '/chat-keyword-search')) {
            $request['search'] = session(\Str::slug($request->getPathInfo()) . '/chat-keyword-search');
            $search = session(\Str::slug($request->getPathInfo()) . '/chat-keyword-search');
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(ChatKeyword::class)
        ->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'title', 'hit', 'modules', 'sub_modules', 'resource_material', 'custom_ordering','created_at'],

            // set columns to searchIn
            ['id', 'title', 'modules', 'sub_modules', 'resource_material']
        );
        // $this->setTranslate();
        if ($request->ajax()) {
            // if($request['orderDirection'] || $request['orderDirection'] == 0){ 
            //     session(['orderDirectionKeyword' => $request['orderDirection']]);
            //     session(['orderByKeyword' => $request['orderBy']]);
            // }
            if($request['page'] && $request['page'] > 0){ 
                session([\Str::slug($request->getPathInfo()) => $request['page']]);
            }
            if ($request['search'] && $request['search'] != '') {
                session([\Str::slug($request->getPathInfo()) . '/chat-keyword-search' => $request['search']]);
            } else {
                session([\Str::slug($request->getPathInfo()) . '/chat-keyword-search' => '']);
            }
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data,'search' => session(\Str::slug($request->getPathInfo()) . '/chat-keyword-search')];
        }

        return view('admin.chat-keyword.index', ['data' => $data,'search' => session(\Str::slug($request->getPathInfo()) . '/chat-keyword-search')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.chat-keyword.create');

        $resourceMaterials = ResourceMaterial::get(['id', 'title']);
        $modules = TModuleMaster::get(['id', 'name']);
        $subModules = TSubModuleMaster::get();

        return view('admin.chat-keyword.create', [
            'resourceMaterials' => $resourceMaterials,
            'modules' => $modules,
            'submodules' => $subModules,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreChatKeyword $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreChatKeyword $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        $sanitized = $this->arrayToString($sanitized);


        // Store the ChatKeyword
        $chatKeyword = ChatKeyword::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/chat-keywords'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/chat-keywords');
    }

    /**
     * Display the specified resource.
     *
     * @param ChatKeyword $chatKeyword
     * @throws AuthorizationException
     * @return void
     */
    public function show(ChatKeyword $chatKeyword)
    {
        $this->authorize('admin.chat-keyword.show', $chatKeyword);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ChatKeyword $chatKeyword
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(ChatKeyword $chatKeyword)
    {
        $this->authorize('admin.chat-keyword.edit', $chatKeyword);

        $resourceMaterials = ResourceMaterial::get(['id', 'title']);
        $modules = TModuleMaster::get(['id', 'name']);
        $subModules = TSubModuleMaster::get();
        
        if(isset($chatKeyword['resource_material']) && $chatKeyword['resource_material'] != ""){
            $assignedMaterials = explode(',',$chatKeyword['resource_material']);
            $chatKeyword['resource_material'] = ResourceMaterial::whereIn('id',$assignedMaterials)->get(['id', 'title']);
        }

        if(isset($chatKeyword['modules']) && $chatKeyword['modules'] != ""){
            $assignedModules = explode(',',$chatKeyword['modules']);
            $chatKeyword['modules'] = TModuleMaster::whereIn('id',$assignedModules)->get(['id', 'name']);
        }

        if(isset($chatKeyword['sub_modules']) && $chatKeyword['sub_modules'] != ""){
            $assignedSubModules = explode(',',$chatKeyword['sub_modules']);
            $chatKeyword['sub_modules'] = TSubModuleMaster::whereIn('id',$assignedSubModules)->get();
        }

        return view('admin.chat-keyword.edit', [
            'chatKeyword' => $chatKeyword,
            'resourceMaterials' => $resourceMaterials,
            'modules' => $modules,
            'submodules' => $subModules,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateChatKeyword $request
     * @param ChatKeyword $chatKeyword
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateChatKeyword $request, ChatKeyword $chatKeyword)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        $sanitized = $this->arrayToString($sanitized);

        // Update changed values ChatKeyword
        $chatKeyword->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/chat-keywords'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/chat-keywords');
    }

    public function arrayToString($sanitized)
    {
        if(isset($sanitized['modules']) && $sanitized['modules'] != ""){
            $sanitized['modules'] = array_pluck($sanitized['modules'], 'id');
            $sanitized['modules'] = implode(",", $sanitized['modules']);
        }

        if(isset($sanitized['sub_modules']) && $sanitized['sub_modules'] != ""){
            $sanitized['sub_modules'] = array_pluck($sanitized['sub_modules'], 'id');
            $sanitized['sub_modules'] = implode(",", $sanitized['sub_modules']);
        }

        if(isset($sanitized['resource_material']) && $sanitized['resource_material'] != ""){
            $sanitized['resource_material'] = array_pluck($sanitized['resource_material'], 'id');
            $sanitized['resource_material'] = implode(",", $sanitized['resource_material']);
        }
        return $sanitized;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyChatKeyword $request
     * @param ChatKeyword $chatKeyword
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyChatKeyword $request, ChatKeyword $chatKeyword)
    {
        $chatKeyword->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyChatKeyword $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyChatKeyword $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('chatKeywords')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }

    public function setTranslate(){
        $chatKeyword = ChatKeyword::get();

        foreach ($chatKeyword as $key => $chatKeywords) {
            $chatKeywords = ChatKeyword::find($chatKeywords->id);
            $chatKeywords->setTranslation('title_json', 'en', $chatKeyword[$key]->title)->save();
        }
    }
}
