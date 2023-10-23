<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Assessment\BulkDestroyAssessment;
use App\Http\Requests\Admin\Assessment\DestroyAssessment;
use App\Http\Requests\Admin\Assessment\IndexAssessment;
use App\Http\Requests\Admin\Assessment\StoreAssessment;
use App\Http\Requests\Admin\Assessment\UpdateAssessment;
use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\Cadre;
use App\Models\District;
use App\Models\State;
use App\Models\UserAssessment;
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
use App\Exports\AssessmentResultExport;
use App\Models\AssessmentEnrollment;
use App\Models\Country;
use App\Models\UserDeviceToken;
use Maatwebsite\Excel\Facades\Excel;
use App\Jobs\sendNotification;
use App\Models\AssessmentCertificate;
use App\Models\AutomaticNotification;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Config;

class AssessmentsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexAssessment $request
     * @return array|Factory|View
     */
    public function index(IndexAssessment $request)
    {
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()) . '/assessment-search')) {
            $request['search'] = session(\Str::slug($request->getPathInfo()) . '/assessment-search');
            $search = session(\Str::slug($request->getPathInfo()) . '/assessment-search');
        }
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        $orWhereCondition = "";
        if ($request->cadre_id && count($request->cadre_id) > 0) {
            $orWhereCondition .= '(';
            for ($i = 0; $i < count($request->cadre_id); $i++) {
                $cadreId = $request->cadre_id[$i];
                $orWhereCondition .= "find_in_set('" . $cadreId . "',cadre_id) OR ";
            }
        }

        // $orWhereCondition = "find_in_set('12',cadre_id) OR find_in_set('14',cadre_id) OR ";
        $orWhereCondition = substr_replace($orWhereCondition, "", -3);
        $orWhereCondition .= ')';

        $state = State::get(['id', 'title']);
        $districts = District::get(['id', 'title']);
        $cadre = Cadre::get(['id', 'title']);
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Assessment::class)->modifyQuery(function ($query) use ($request, $orWhereCondition) {
            // $assignedDistrict = '';
            // $assignedCountry = '';
            // $assignedState = '';
            // $assignedCadre = '';
            // if(\Auth::user()->role_type == 'country_type' && (\Auth::user()->roles[0]['id'] == 1 || \Auth::user()->roles[0]['id'] == 2)){
            //     // $assignedCountry = \Auth::user()->country;
            //     // $assignedState = \Auth::user()->state;
            //     // $assignedCadre = \Auth::user()->cadre;
            //     // $assignedDistrict = \Auth::user()->district;
            // } else if(\Auth::user()->role_type == 'country_type'){
            //     $assignedCountry = \Auth::user()->country;
            //     $assignedCadre = \Auth::user()->cadre;
            // }
            // elseif (\Auth::user()->role_type == 'state_type'){
            //     $assignedState = \Auth::user()->state;
            //     $assignedCadre = \Auth::user()->cadre;
            // }else {
            //     $assignedDistrict = \Auth::user()->district;
            //     $assignedCadre = \Auth::user()->cadre;
            // }
            // if ($assignedCountry != '' && $assignedCountry > 0) {
            //     $query->whereIn('country_id', explode(',',$assignedCountry));
            // }
            // if ($assignedState != '' && $assignedState > 0) {
            //    $query->whereRaw("substr('" . $assignedState . "',state_id)");
            // }
            // if ($assignedCadre != '' && $assignedCadre > 0) {
            //     // $query->whereIn('cadre_id', explode(',',$assignedCadre));
            //     $query->whereRaw("substr('" . $assignedCadre . "',cadre_id)")->orWhere('cadre_id',$assignedCadre);
            // }
            // if ($assignedDistrict != '' && $assignedDistrict > 0) {
            //     $query->whereRaw("substr('" . $assignedDistrict . "',district_id)")->orWhere('district_id',$assignedDistrict);
            // }
            if (\Auth::user()->roles[0]['id'] == 10 || \Auth::user()->roles[0]['id'] == 11) {
                $query->where('created_by', \Auth::user()->id);
            }
            if ($request->has('cadre_id')) {
                $query->whereRaw($orWhereCondition);
            }
        })->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'time_to_complete', 'cadre_id', 'country_id', 'state_id', 'assessment_title', 'district_id', 'assessment_type', 'from_date', 'to_date', 'initial_invitation', 'activated', 'cadre_type', 'created_by', 'created_at', 'certificate_type'],

            // set columns to searchIn
            ['id', 'cadre_id', 'state_id', 'assessment_title', 'assessment_type', 'from_date', 'to_date', 'district_id', 'cadre_type', 'certificate_type'],
            function ($query) use ($request) {
                $query->with(['assessment_questions', 'user_with_trashed', 'assessment_certificate']);
                $assignedState = \Auth::user()->state;
                if ($assignedState != '' && $assignedState > 0) {
                    $query->whereRaw("substr('" . $assignedState . "',state_id)");
                }
            }
        );

        if ($request->ajax()) {
            $request->session()->pull('assessment_notification');
            if ($request['page'] && $request['page'] > 0) {
                session([\Str::slug($request->getPathInfo()) => $request['page']]);
            }
            if ($request['search'] && $request['search'] != '') {
                session([\Str::slug($request->getPathInfo()) . '/assessment-search' => $request['search']]);
            } else {
                session([\Str::slug($request->getPathInfo()) . '/assessment-search' => '']);
            }
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data, 'search' => session(\Str::slug($request->getPathInfo()) . '/assessment-search')];
        }
        return view('admin.assessment.index', [
            'data' => $data,
            'state' => $state,
            'districts' => $districts,
            'cadre' => $cadre,
            'message' => session('assessment_notification'),
            'search' => session(\Str::slug($request->getPathInfo()) . '/assessment-search')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.assessment.create');
        $masterData = \StateWiseFilterData::getStateWiseFilterDataWithSubscriber();
        $states = $masterData['state'];
        $districts = $masterData['district'];
        $cadres = $masterData['cadres'];
        $country = $masterData['country'];
        $certificate = AssessmentCertificate::get(['id', 'title']);

        if (\Auth::user()->roles[0]['id'] == 10) {
            $cadres = Cadre::whereIn('id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->get(['id', 'title', 'cadre_type']);
        }

        return view('admin.assessment.create', [
            'cadre' => $cadres,
            'state' => $states,
            'district' => $districts,
            'country' => $country,
            'user_state' => \Auth::user()->state,
            'user_role' => \Auth::user()->roles[0]['id'],
            'certificate' => $certificate
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAssessment $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreAssessment $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        $sanitized['cadre_id'] = array_pluck($sanitized['cadre_id'], 'id');
        $sanitized['cadre_id'] = implode(",", $sanitized['cadre_id']);


        if (isset($request['country_id']) && $request['country_id'] != '') {

            $sanitized['country_id'] = $request['country_id']['id'];
        } else {
            $sanitized['country_id'] = 0;
        }
        $sanitized['certificate_type'] = $request['certificate_type']['id'];

        $sanitized['state_id'] = array_pluck($sanitized['state_id'], 'id');
        $sanitized['state_id'] = implode(",", $sanitized['state_id']);

        $sanitized['district_id'] = array_pluck($sanitized['district_id'], 'id');
        $sanitized['district_id'] = implode(",", $sanitized['district_id']);
        $sanitized['created_by'] = \Auth::user()->id;
        try {
            DB::beginTransaction();
            // Store the Assessment
            $assessment = Assessment::create($sanitized);
            if (isset($request['ids']) && $request['ids'] != "") {
                $this->addAssessmentQuestionByIds($request['ids'], $assessment);
            }

            if ($request->ajax()) {
                DB::commit();
                return ['redirect' => url('admin/assessments'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
            }
            DB::commit();
            return redirect('admin/assessments');
        } catch (Exception $e) {
            Log::info("inside store assesment error");
            Log::error($e);
            DB::rollback();
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Assessment $assessment
     * @throws AuthorizationException
     * @return void
     */
    public function show(Assessment $assessment)
    {
        $this->authorize('admin.assessment.show', $assessment);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Assessment $assessment
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Assessment $assessment)
    {
        if (\Auth::user()->roles[0]['id'] == 3 || \Auth::user()->roles[0]['id'] == 10) {
            $assessment->load('user');
            if ($assessment->user->roles[0]->id != 3 && $assessment->user->roles[0]->id != 10) {
                abort(403);
            } else {
                $this->authorize('admin.assessment.edit', $assessment);
            }
        } else {
            $this->authorize('admin.assessment.edit', $assessment);
        }
        $cadres = Cadre::get(['id', 'title', 'cadre_type']);
        $country = Country::get(['id', 'title']);
        $certificate = AssessmentCertificate::get(['id', 'title']);
        $masterData = \StateWiseFilterData::getStateWiseFilterDataWithSubscriber();
        $states = $masterData['state'];
        $districts = $masterData['district'];

        if (\Auth::user()->roles[0]['id'] == 10) {
            $cadres = cadre::whereIn('id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->get(['id', 'title', 'cadre_type']);
        }

        //needed to show multiselect selected value
        if (isset($assessment['cadre_id']) && $assessment['cadre_id'] != "") {
            $assignedCadres = explode(',', $assessment['cadre_id']);
            $assessment['cadre_id'] = Cadre::whereIn('id', $assignedCadres)->get(['id', 'title', 'cadre_type']);
        }

        if (isset($assessment['country_id']) && $assessment['country_id'] != "") {
            $assessment['country_id'] = $assessment['country_id'];
            $assessment['country_id'] = Country::where('id', $assessment['country_id'])->get(['id', 'title']);
        }

        if (isset($assessment['state_id']) && $assessment['state_id'] != "") {
            $assignedStates = explode(',', $assessment['state_id']);
            $assessment['state_id'] = State::whereIn('id', $assignedStates)->get();
        }

        if (isset($assessment['district_id']) && $assessment['district_id'] != "") {
            $assignedDistricts = explode(',', $assessment['district_id']);
            $assessment['district_id'] = District::whereIn('id', $assignedDistricts)->get();
        } else {
            $assessment['district_id'] = [];
        }

        if (isset($assessment['certificate_type']) && $assessment['certificate_type'] != null) {
            $assessment['certificate_type'] = AssessmentCertificate::where('id', $assessment['certificate_type'])->get(['id', 'title']);
        }

        $assessment['all_cadres'] = $cadres;
        $assessment['all_states'] = $states;

        return view('admin.assessment.edit', [
            'assessment' => $assessment,
            'cadre' => $cadres,
            'state' => $states,
            'district' => $districts,
            'country' => $country,
            'user_state' => \Auth::user()->state,
            'certificate' => $certificate
        ]);
    }

    public function copy(Assessment $assessment)
    {
        $this->authorize('admin.assessment.copy', $assessment);
        // $copy = $assessment->replicate();
        $copy = DB::table('assessments')->where('id', $assessment->id)->where('deleted_at', '=', null)->get(); //->select('id','time_to_complete','cadre_id','state_id','assessment_title','activated','district_id','cadre_type')
        $copy_array = json_decode($copy, true);
        $json_copy_array = json_decode($copy_array[0]['assessment_title'], true);
        $copy_array[0]['assessment_title'] = ['en' => $json_copy_array['en'] . "_" .  now(), 'hi' => isset($json_copy_array['hi']) ? $json_copy_array['hi'] : null, 'gu' => isset($json_copy_array['gu']) ? $json_copy_array['gu'] : null, 'mr' => isset($json_copy_array['mr']) ? $json_copy_array['mr'] : null, 'ta' => isset($json_copy_array['ta']) ? $json_copy_array['ta'] : null, 'pa' => isset($json_copy_array['pa']) ? $json_copy_array['pa'] : null, 'te' => isset($json_copy_array['te']) ? $json_copy_array['te'] : null, 'kn' => isset($json_copy_array['kn']) ? $json_copy_array['kn'] : null];
        $copy_array['activated'] = 0;
        $assessment_id = $assessment->create($copy_array[0]);
        $assessment_question = DB::table('assessment_questions')->where('assessment_id', $assessment->id)->where('deleted_at', '=', null)->get(); //->select('id','assessment_id','correct_answer','order_index','question','option1','option2','option3','option4')
        $question_array = json_decode($assessment_question, true);

        foreach ($question_array as $question) {

            $json_question_array = json_decode($question['question'], true);
            $json_option1_array = json_decode($question['option1'], true);
            $json_option2_array = json_decode($question['option2'], true);
            $json_option3_array = json_decode($question['option3'], true);
            $json_option4_array = json_decode($question['option4'], true);
            $question['question'] = ['en' => $json_question_array['en'], 'hi' => isset($json_question_array['hi']) ? $json_question_array['hi'] : null, 'gu' => isset($json_question_array['gu']) ? $json_question_array['gu'] : Null, 'mr' => isset($json_copy_array['mr']) ? $json_copy_array['mr'] : null, 'ta' => isset($json_copy_array['ta']) ? $json_copy_array['ta'] : null, 'pa' => isset($json_copy_array['pa']) ? $json_copy_array['pa'] : null, 'te' => isset($json_copy_array['te']) ? $json_copy_array['te'] : null, 'kn' => isset($json_copy_array['kn']) ? $json_copy_array['kn'] : null];
            $question['option1'] = ['en' => $json_option1_array['en'], 'hi' => isset($json_option1_array['hi']) ? $json_option1_array['hi'] : null, 'gu' => isset($json_option1_array['gu']) ? $json_option1_array['gu'] : null, 'mr' => isset($json_copy_array['mr']) ? $json_copy_array['mr'] : null, 'ta' => isset($json_copy_array['ta']) ? $json_copy_array['ta'] : null, 'pa' => isset($json_copy_array['pa']) ? $json_copy_array['pa'] : null, 'te' => isset($json_copy_array['te']) ? $json_copy_array['te'] : null, 'kn' => isset($json_copy_array['kn']) ? $json_copy_array['kn'] : null];
            $question['option2'] = ['en' => $json_option2_array['en'], 'hi' => isset($json_option2_array['hi']) ? $json_option2_array['hi'] : null, 'gu' => isset($json_option2_array['gu']) ? $json_option2_array['gu'] : null, 'mr' => isset($json_copy_array['mr']) ? $json_copy_array['mr'] : null, 'ta' => isset($json_copy_array['ta']) ? $json_copy_array['ta'] : null, 'pa' => isset($json_copy_array['pa']) ? $json_copy_array['pa'] : null, 'te' => isset($json_copy_array['te']) ? $json_copy_array['te'] : null, 'kn' => isset($json_copy_array['kn']) ? $json_copy_array['kn'] : null];
            $question['option3'] = ['en' => $json_option3_array['en'], 'hi' => isset($json_option3_array['hi']) ? $json_option3_array['hi'] : null, 'gu' => isset($json_option3_array['gu']) ? $json_option3_array['gu'] : null, 'mr' => isset($json_copy_array['mr']) ? $json_copy_array['mr'] : null, 'ta' => isset($json_copy_array['ta']) ? $json_copy_array['ta'] : null, 'pa' => isset($json_copy_array['pa']) ? $json_copy_array['pa'] : null, 'te' => isset($json_copy_array['te']) ? $json_copy_array['te'] : null, 'kn' => isset($json_copy_array['kn']) ? $json_copy_array['kn'] : null];
            $question['option4'] = ['en' => $json_option4_array['en'], 'hi' => isset($json_option4_array['hi']) ? $json_option4_array['hi'] : null, 'gu' => isset($json_option4_array['gu']) ? $json_option4_array['gu'] : null, 'mr' => isset($json_copy_array['mr']) ? $json_copy_array['mr'] : null, 'ta' => isset($json_copy_array['ta']) ? $json_copy_array['ta'] : null, 'pa' => isset($json_copy_array['pa']) ? $json_copy_array['pa'] : null, 'te' => isset($json_copy_array['te']) ? $json_copy_array['te'] : null, 'kn' => isset($json_copy_array['kn']) ? $json_copy_array['kn'] : null];
            $question['assessment_id'] = $assessment_id->id;
            AssessmentQuestion::create($question);
        }
    }

    public function addAssessmentQuestionByIds($ids, $assessment)
    {
        $all_assessment_questions = collect(AssessmentQuestion::whereIn('id', explode(',', $ids))->get(['assessment_id', 'correct_answer', 'option1', 'order_index', 'category', 'question', 'option1', 'option2', 'option3', 'option4']));
        foreach ($all_assessment_questions as $key => $question) {
            $question['assessment_id'] = $assessment->id;
            // $question['category'] = "";
            // unset($all_assessment_questions[$key]['resource_url']);
            AssessmentQuestion::create($question->toArray());
        }
        return true;
        // AssessmentQuestion::insert($all_assessment_questions->toArray());
    }

    public function activeFlag(Request $request, Assessment $assessment)
    {

        if (isset($request['from_date']) && $request['from_date'] != null && strtotime($request['from_date']) <= strtotime(date("Y-m-d H:i:s"))) {
            $message = "Time Out!! You can't toggle active button";
            if ($request->ajax()) {
                session(['user_notification' => $message]);
                return [
                    'redirect' => url('admin/assessments'),
                    'message' => $message,
                ];
            }

            return redirect('admin/assessments')->with('message', $message);
        } else {
            $assessment->update(['activated' => $request['activated']]);
        }

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/assessments'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/assessments');
    }

    public function report(Assessment $assessment)
    {
        $this->authorize('admin.assessment.report');
        return Excel::download(new AssessmentResultExport($assessment, 0), 'AssessmentResult.csv');
    }

    public function assessmentQuestion(Assessment $assessment)
    {
        $this->authorize('admin.assessment.assessment-question');
        return Excel::download(new AssessmentResultExport($assessment, 1), 'AssessmentQuestionResult.csv');
    }

    public function sendInitialInvitation(Request $request, Assessment $assessment)
    {
        $message = "";
        if ($assessment->from_date != "" || $assessment->from_date != null) {
            $users = AssessmentEnrollment::where('assessment_id', $assessment->id)->pluck('user_id');
            $notification['title'] = "New Assessment";
            $notification['description'] = "Assessment added for " . $assessment->assessment_title . ", Click here to enroll";
            $notification['type'] = "Future Assessment";
            $notification['linking_url'] = Config::get('app.GENERAL.frontend_url') . "/CurrentAssessment";
        } else {
            $users = Subscriber::whereRaw("find_in_set(state_id, ?)", [$assessment['state_id']])
                ->whereRaw("find_in_set(cadre_id, ?)", [$assessment['cadre_id']])
                ->orWhereRaw("find_in_set(country_id, ?)", [$assessment['country_id']])
                ->orWhereRaw("find_in_set(district_id, ?)", [$assessment['district_id']])
                ->pluck('id'); //->get(['id'])
            $notification['title'] = "Live Now";
            $notification['description'] = "Assessment for " . $assessment->assessment_title . " is Live Now, Check out now";
            $notification['type'] = "Current Assessment";
            $notification['linking_url'] = Config::get('app.GENERAL.frontend_url') . "/FutureAssessment";
        }

        $device_id = UserDeviceToken::whereIn('user_id', $users)->get('notification_token');
        if (isset($device_id) && count($device_id) > 0) {

            $notification['subscriber_id'] = implode(',', $users->toArray());
            $notification['created_by'] = \Auth::user()->id;
            $notification['status'] = 'Pending';
            $userNotification = AutomaticNotification::create($notification);

            $notification['type_title'] = $assessment->id;
            // $response = SendNotificationController::InitalAssessmentInvitation($assessment, $device_id);
            dispatch(new sendNotification($notification, $users, 'Assessment', $notification['linking_url'], $userNotification['id'], 'true'));
            $message = "Notification queued. Check status later.";
            Assessment::where('id', $assessment->id)->update(['initial_invitation' => 1]);
            // if (isset($response['error'])) {
            //     $message = "User Not Found";
            // } else {
            //     $successCount = isset($response['successFullCount']) && $response['successFullCount'] > 0 ? $response['successFullCount'] : 0;
            //     $failCount = isset($response['failedCount']) && $response['failedCount'] > 0 ? $response['failedCount'] : 0;
            //     $message = "You have successfully added notification. Your notification is successfully send to " . $successCount . " Subscribers and Failed for " . $failCount . " Subscribers.";
            //     Assessment::where('id', $assessment->id)->update(['initial_invitation' => 1]);
            // }
        } else {
            Assessment::where('id', $assessment->id)->update(['initial_invitation' => 1]);
        }
        session(['assessment_notification' => $message]);
        if ($request->ajax()) {
            session(['assessment_notification' => $message]);
            return [
                'redirect' => url('admin/assessments'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }
        return redirect('admin/assessments')->with('message', $message);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAssessment $request
     * @param Assessment $assessment
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateAssessment $request, Assessment $assessment)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        //arrray to string for multiple cadres
        $sanitized['cadre_id'] = array_pluck($sanitized['cadre_id'], 'id');
        $sanitized['cadre_id'] = implode(",", $sanitized['cadre_id']);

        if (isset($request['country_id'])) {
            if ($request['country_id'] == NULL) {
                $sanitized['country_id'] = 0;
            } else {
                $country_id = collect($request['country_id'])->pluck('id');
                if (is_numeric($country_id[0]) && $country_id[0] > 0) {
                    $sanitized['country_id'] = $country_id[0];
                } else {
                    $sanitized['country_id'] = $request['country_id']['id'];
                }
            }
        }
        if (isset($sanitized['certificate_type']) && isset($sanitized['certificate_type'][0]) && $sanitized['certificate_type'][0] != '') {
            $sanitized['certificate_type'] = $sanitized['certificate_type'][0]['id'];
        } elseif (isset($sanitized['certificate_type']) && $sanitized['certificate_type'] != '' && $sanitized['certificate_type']['id']) {
            $sanitized['certificate_type'] = $sanitized['certificate_type']['id'];
        }
        // $sanitized['certificate_type'] = $request['certificate_type'][0]['id'];
        $sanitized['state_id'] = array_pluck($sanitized['state_id'], 'id');
        $sanitized['state_id'] = implode(",", $sanitized['state_id']);

        $sanitized['district_id'] = array_pluck($sanitized['district_id'], 'id');
        $sanitized['district_id'] = implode(",", $sanitized['district_id']);
        //updated time store in user assessment table
        UserAssessment::where('assessment_id', $assessment['id'])->update(['total_time' => $sanitized['time_to_complete']]);
        // Update changed values Assessment
        $assessment->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/assessments'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/assessments');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyAssessment $request
     * @param Assessment $assessment
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyAssessment $request, Assessment $assessment)
    {
        $assessment->delete();
        AssessmentQuestion::where('assessment_id', $assessment->id)->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyAssessment $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyAssessment $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('assessments')->whereIn('id', $bulkChunk)
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
        $assessment = Assessment::get();

        foreach ($assessment as $key => $assessments) {
            $assessments = Assessment::find($assessments->id);
            $assessments->setTranslation('assessment_json', 'en', $assessment[$key]->assessment_title)->save();
        }
    }
}
