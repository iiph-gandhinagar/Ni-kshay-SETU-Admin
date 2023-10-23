<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TTrainingTag\BulkDestroyTTrainingTag;
use App\Http\Requests\Admin\TTrainingTag\DestroyTTrainingTag;
use App\Http\Requests\Admin\TTrainingTag\IndexTTrainingTag;
use App\Http\Requests\Admin\TTrainingTag\StoreTTrainingTag;
use App\Http\Requests\Admin\TTrainingTag\UpdateTTrainingTag;
use App\Models\TTrainingTag;
use App\Models\TModuleMaster;
use App\Models\TSubModuleMaster;
use App\Models\ChatQuestion;
use App\Models\ResourceMaterial;
use Brackets\AdminListing\Facades\AdminListing;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TTrainingTagController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexTTrainingTag $request
     * @return array|Factory|View
     */
    public function index(IndexTTrainingTag $request)
    {
        if (!$request->ajax() && $request->session()->has('orderDirection')) {
            $request['orderDirection'] = session('orderDirection');
            $request['orderBy'] = session('orderBy');
        }
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()) . '/training-tag-search')) {
            $request['search'] = session(\Str::slug($request->getPathInfo()) . '/training-tag-search');
            $search = session(\Str::slug($request->getPathInfo()) . '/training-tag-search');
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(TTrainingTag::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'tag', 'is_fix_response', 'like_count', 'dislike_count', 'questions', 'modules', 'sub_modules', 'resource_material', 'updated_at', 'created_at'],

            // set columns to searchIn
            ['id', 'tag', 'pattern', 'response', 'questions', 'modules', 'sub_modules', 'resource_material'],
            function ($query) {
                $query->select('t_training_tag.*', DB::raw("(CHAR_LENGTH(pattern) - CHAR_LENGTH(REPLACE(pattern, '|', '')) + 1) as PatternsInTag,(CHAR_LENGTH(questions) - CHAR_LENGTH(REPLACE(NULLIF(questions,''), ',', ''))+ 1) as QuestionsInTag"));
            }
        );
        // $this->setTranslate();

        if ($request->ajax()) {
            if ($request['orderDirection'] || $request['orderDirection'] == 0) {
                session(['orderDirection' => $request['orderDirection']]);
                session(['orderBy' => $request['orderBy']]);
            }

            if ($request['page'] && $request['page'] > 0) {
                session([\Str::slug($request->getPathInfo()) => $request['page']]);
            }
            if ($request['search'] && $request['search'] != '') {
                session([\Str::slug($request->getPathInfo()) . '/training-tag-search' => $request['search']]);
            } else {
                session([\Str::slug($request->getPathInfo()) . '/training-tag-search' => '']);
            }

            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data, 'search' => session(\Str::slug($request->getPathInfo()) . '/training-tag-search')];
        }
        return view('admin.t-training-tag.index', ['data' => $data, 'search' => session(\Str::slug($request->getPathInfo()) . '/training-tag-search')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.t-training-tag.create');

        $questions = ChatQuestion::get(['id', 'question']);
        $resourceMaterials = ResourceMaterial::get(['id', 'title']);
        $modules = TModuleMaster::get(['id', 'name']);
        $subModules = TSubModuleMaster::get();

        return view('admin.t-training-tag.create', [
            'questions' => $questions,
            'resourceMaterials' => $resourceMaterials,
            'modules' => $modules,
            'submodules' => $subModules,
        ]);
    }


    public function tagcount(Request $request)
    {
        $viewtag = TTrainingTag::paginate();

        $tagcount = TTrainingTag::count(); //get('tag')->

        $pattern = TTrainingTag::get(["id", "tag", "pattern"])->collect();

        $collection = collect([]);
        $arr = [];
        $count = 0;
        $varcount = 0;
        $url = $pattern->pluck('pattern');

        $repetitive = explode('|', $url);

        foreach ($repetitive as $key => $value) {
            $result = explode('","', $value);

            foreach ($result as $key => $value) {
                $arr[$count] = $value;
                $count++;
            }
        }

        $vals = array_count_values(array_map('strtolower', $arr));
        $patterncount = count(explode('|', $url));

        $count = TTrainingTag::count(); //get('pattern')->
        foreach ($vals as $key => $value) {
            if ($value > 1) {
                $varcount++;

                $compare = TTrainingTag::whereRaw('LOWER(`pattern`) Like?', '%' . '|' . $key . '|' . '%')->get('tag')->collect();
                $display = str_replace(array('["', '"]'), '', $compare->pluck('tag'));
                $collection->push(['tag' => str_replace('","', ' , ', $display), 'pattern' => $key, 'value' => $value]);
            }
        }

        $chunk = $collection->chunk(10);
        $chunks = count($chunk->toArray());
        return view('admin.t-training-tag.tagcount')->with('tagcount', $tagcount)->with('patterncount', $patterncount)->with('viewtag', $viewtag)->with('pattern', $pattern)->with('varcount', $varcount)->with('collection', $collection)->with('chunks', $chunks);
    }








    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTTrainingTag $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreTTrainingTag $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        // $max_id = TTrainingTag::max('id');
        // $sanitized['id'] = $max_id + 1;
        $sanitized = $this->arrayToString($sanitized);
        if (isset($sanitized['message']) && $sanitized['message'] != '') {
            $pattern = $sanitized['pattern'];
            return abort(400, "Pattern Found '$pattern' ");
        } else {
            // Store the TTrainingTag
            $tTrainingTag = TTrainingTag::create($sanitized);

            if ($request->ajax()) {
                return ['redirect' => url('admin/t-training-tags'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
            }

            return redirect('admin/t-training-tags');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param TTrainingTag $tTrainingTag
     * @throws AuthorizationException
     * @return void
     */
    public function show(TTrainingTag $tTrainingTag)
    {
        $this->authorize('admin.t-training-tag.show', $tTrainingTag);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param TTrainingTag $tTrainingTag
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(TTrainingTag $tTrainingTag)
    {
        $this->authorize('admin.t-training-tag.edit', $tTrainingTag);
        $questions = ChatQuestion::get(['id', 'question']);
        $resourceMaterials = ResourceMaterial::get(['id', 'title']);
        $modules = TModuleMaster::get(['id', 'name']);
        $subModules = TSubModuleMaster::get();

        //needed to show multiselect selected value
        if (isset($tTrainingTag['pattern']) && $tTrainingTag['pattern'] != "") {
            $tTrainingTag['pattern'] = explode('|', $tTrainingTag['pattern']);
        }

        if (isset($tTrainingTag['response']) && $tTrainingTag['response'] != "") {

            $response_value = $tTrainingTag->getTranslations('response');
            $tTrainingTag['response'] = ["en" => isset($response_value['en']) ? explode('|', $response_value['en']) : NULL, 'hi' => isset($response_value['hi']) ? explode('|', $response_value['hi']) : NULL, 'gu' => isset($response_value['gu']) ? explode('|', $response_value['gu']) : NULL, 'mr' => isset($response_value['mr']) ? explode('|', $response_value['mr']) : NULL];
        }

        if (isset($tTrainingTag['questions']) && $tTrainingTag['questions'] != "") {
            $assignedQuestions = explode(',', $tTrainingTag['questions']);
            $tTrainingTag['questions'] = ChatQuestion::whereIn('id', $assignedQuestions)->orderByRaw('FIELD (id, ' . $tTrainingTag['questions'] . ')')->get(['id', 'question']);
        } else {
            $tTrainingTag['questions'] = [];
        }

        if (isset($tTrainingTag['resource_material']) && $tTrainingTag['resource_material'] != "") {
            $assignedMaterials = explode(',', $tTrainingTag['resource_material']);
            $tTrainingTag['resource_material'] = ResourceMaterial::whereIn('id', $assignedMaterials)->orderByRaw('FIELD (id, ' . $tTrainingTag['resource_material'] . ')')->get(['id', 'title']);
        } else {
            $tTrainingTag['resource_material'] = [];
        }

        if (isset($tTrainingTag['modules']) && $tTrainingTag['modules'] != "") {
            $assignedModules = explode(',', $tTrainingTag['modules']);
            $tTrainingTag['modules'] = TModuleMaster::whereIn('id', $assignedModules)->get(['id', 'name']);
        }

        if (isset($tTrainingTag['sub_modules']) && $tTrainingTag['sub_modules'] != "") {
            $assignedSubModules = explode(',', $tTrainingTag['sub_modules']);
            $tTrainingTag['sub_modules'] = TSubModuleMaster::whereIn('id', $assignedSubModules)->get();
        }
        return view('admin.t-training-tag.edit', [
            'tTrainingTag' => $tTrainingTag,
            'questions' => $questions,
            'resourceMaterials' => $resourceMaterials,
            'modules' => $modules,
            'submodules' => $subModules,
        ]);
    }

    public function copy(TTrainingTag $tTrainingTag)
    {
        $this->authorize('admin.assessment.copy', $tTrainingTag);
        $copy = DB::table('t_training_tag')->where('id', $tTrainingTag->id)->where('deleted_at', '=', null)->get(['tag', 'response', 'questions', 'modules', 'sub_modules', 'resource_material', 'is_fix_response']);
        $copy_array = json_decode($copy, true);

        $copy_array[0]['like_count'] = 0;
        $copy_array[0]['dislike_count'] = 0;
        $copy_array[0]['tag'] = $copy_array[0]['tag'] . "_" .  now();
        if (isset($copy_array[0]['response'])) {
            $json_response_array = json_decode($copy_array[0]['response'], true);
            $copy_array[0]['response'] = ['en' => $json_response_array['en'], 'hi' => isset($json_response_array['hi']) ? $json_response_array['hi'] : null, 'gu' => isset($json_response_array['gu']) ? $json_response_array['gu'] : null];
        }

        TTrainingTag::create($copy_array[0]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTTrainingTag $request
     * @param TTrainingTag $tTrainingTag
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateTTrainingTag $request, TTrainingTag $tTrainingTag)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        $sanitized['response_en'] = $request['response_en'];
        $sanitized['response_hi'] = $request['response_hi'];
        $sanitized['response_gu'] = $request['response_gu'];
        $sanitized['response_mr'] = $request['response_mr'];
        $sanitized = $this->arrayToString($sanitized, $tTrainingTag->id);
        if (isset($sanitized['message']) && $sanitized['message'] != '') {
            $pattern = $sanitized['pattern'];
            return abort(400, "Pattern Found '$pattern' ");
        } else {
            // Update changed values TTrainingTag
            $tTrainingTag->update($sanitized);

            if ($request->ajax()) {
                return [
                    'redirect' => url('admin/t-training-tags'),
                    'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
                ];
            }

            return redirect('admin/t-training-tags');
        }
    }

    public function arrayToString($sanitized, $updatePattern = 0)
    {
        if (isset($sanitized['pattern']) && $sanitized['pattern'] != "") {
            if ($updatePattern != 0) {
                foreach ($sanitized['pattern'] as $item) {
                    $string = DB::select("SELECT id FROM t_training_tag WHERE instr(pattern,'|$item|') and id != $updatePattern and deleted_at is NULL"); //$item space needed for perfect match
                    if (count($string) > 0) {
                        return ['message' => 'Pattern Found', 'errorCode' => 400, 'pattern' => $item];
                    }
                }
            } else {
                foreach ($sanitized['pattern'] as $item) {
                    $string = DB::select("SELECT id FROM t_training_tag WHERE instr(pattern,'|$item|') and deleted_at is NULL"); //$item space needed for perfect match
                    if (count($string) > 0) {
                        return ['message' => 'Pattern Found', 'errorCode' => 400, 'pattern' => $item];
                    }
                }
            }
            $sanitized['pattern'] = implode("|", $sanitized['pattern']);
        }

        if (isset($sanitized['is_fix_response']) && $sanitized['is_fix_response'] == 1) {
            $sanitized['questions'] = "";
            $sanitized['modules'] = "";
            $sanitized['sub_modules'] = "";
            $sanitized['resource_material'] = "";
            if (isset($sanitized['response']) && $sanitized['response'] != "") {
                $sanitized['response']['en'] = implode("|", $sanitized['response']['en']);
                $sanitized['response']['hi'] = isset($sanitized['response']['hi']) ? implode("|", $sanitized['response']['hi']) : NULL;
                $sanitized['response']['gu'] = isset($sanitized['response']['gu']) ? implode("|", $sanitized['response']['gu']) : NULL;
                $sanitized['response']['mr'] = isset($sanitized['response']['mr']) ? implode("|", $sanitized['response']['mr']) : NULL;
            }
        } else {
            $sanitized['response'] = "";
            if (isset($sanitized['questions']) && $sanitized['questions'] != "") {
                $sanitized['questions'] = array_pluck($sanitized['questions'], 'id');
                $sanitized['questions'] = implode(",", $sanitized['questions']);
            }

            if (isset($sanitized['modules']) && $sanitized['modules'] != "") {
                $sanitized['modules'] = array_pluck($sanitized['modules'], 'id');
                $sanitized['modules'] = implode(",", $sanitized['modules']);
            }

            if (isset($sanitized['sub_modules']) && $sanitized['sub_modules'] != "") {
                $sanitized['sub_modules'] = array_pluck($sanitized['sub_modules'], 'id');
                $sanitized['sub_modules'] = implode(",", $sanitized['sub_modules']);
            }

            if (isset($sanitized['resource_material']) && $sanitized['resource_material'] != "") {
                $sanitized['resource_material'] = array_pluck($sanitized['resource_material'], 'id');
                $sanitized['resource_material'] = implode(",", $sanitized['resource_material']);
            }
        }
        return $sanitized;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyTTrainingTag $request
     * @param TTrainingTag $tTrainingTag
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyTTrainingTag $request, TTrainingTag $tTrainingTag)
    {
        $tTrainingTag->delete();

        if ($request->ajax()) {
            if (!$request->ajax() && $request->session()->has('orderDirection')) {
                $request['orderDirection'] = session('orderDirection');
                $request['orderBy'] = session('orderBy');
            }
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyTTrainingTag $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyTTrainingTag $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('tTrainingTags')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }

    public function setTranslate()
    {
        $TTrainingTag = TTrainingTag::get();

        foreach ($TTrainingTag as $key => $TTrainingTags) {
            $TTrainingTags = TTrainingTag::find($TTrainingTags->id);
            $TTrainingTags->setTranslation('response_json', 'en', $TTrainingTag[$key]->response)->save();
        }
    }

    // public function paginate($items, $perPage = 5, $page = null, $options = [])

    // {

    //     $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

    //     $items = $items instanceof Collection ? $items : Collection::make($items);

    //     return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);

    // }
}
