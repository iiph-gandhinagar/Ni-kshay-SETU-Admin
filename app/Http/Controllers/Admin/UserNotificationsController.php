<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserNotification\BulkDestroyUserNotification;
use App\Http\Requests\Admin\UserNotification\DestroyUserNotification;
use App\Http\Requests\Admin\UserNotification\IndexUserNotification;
use App\Http\Requests\Admin\UserNotification\StoreUserNotification;
use App\Http\Requests\Admin\UserNotification\UpdateUserNotification;
use App\Models\UserNotification;
use App\Models\Subscriber;
use App\Models\State;
use App\Models\Cadre;
use Brackets\AdminListing\Facades\AdminListing;
use Carbon\Carbon;
use Exception;
use App\Models\Assessment;
use App\Models\AutomaticNotification;
use App\Models\CaseDefinition;
use App\Models\CgcInterventionsAlgorithm;
use App\Models\DiagnosesAlgorithm;
use App\Models\DifferentialCareAlgorithm;
use App\Models\DynamicAlgoMaster;
use App\Models\DynamicAlgorithm;
use App\Models\GuidanceOnAdverseDrugReaction;
use App\Models\LatentTbInfection;
use App\Models\ResourceMaterial;
use App\Models\TreatmentAlgorithm;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Log;
use App\Jobs\sendNotification;
use Config;

class UserNotificationsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexUserNotification $request
     * @return array|Factory|View
     */
    public function index(IndexUserNotification $request)
    {
        $state = State::get(['id', 'title']);
        $subscriber = Subscriber::get(['id', 'name']);
        $assessment = Assessment::get(['id', 'assessment_title']);
        $resource_material = ResourceMaterial::get(['id', 'title']);
        $case_definition = CaseDefinition::get(['id', 'title']);
        $dignosis_algo = DiagnosesAlgorithm::get(['id', 'title']);
        $treatment_algo = TreatmentAlgorithm::get(['id', 'title']);
        $guidance_on_adr = GuidanceOnAdverseDrugReaction::get(['id', 'title']);
        $latent_tb_infection = LatentTbInfection::get(['id', 'title']);
        $differential_care_algo = DifferentialCareAlgorithm::get(['id', 'title']);
        $cgc_algo = CgcInterventionsAlgorithm::get(['id', 'title']);
        $dynamic_algo_master = DynamicAlgorithm::get(['id', 'title']);
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(UserNotification::class)
            ->modifyQuery(function ($query) {
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
                if (\Auth::user()->roles[0]['id'] == 10 || \Auth::user()->roles[0]['id'] == 11) {
                    $query->where('created_by', \Auth::user()->id);
                }
                // $assignedRole = \Auth::user()->roles[0]['id'];
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
            })
            ->processRequestAndGet(
                // pass the request with params
                $request,

                // set columns to query
                ['automatic_notification_type', 'cadre_id', 'cadre_type', 'country_id', 'description', 'user_id', 'id', 'is_deeplinking', 'state_id', 'title', 'type', 'automatic_notification_type', 'type_title', 'created_at', 'successful_count', 'failed_count', 'created_by'],

                // set columns to searchIn
                ['automatic_notification_type', 'cadre_id', 'cadre_type', 'description', 'district_id', 'id', 'state_id', 'title', 'type', 'type_title', 'user_id', 'automatic_notification_type', 'created_by'],
                function ($query) {
                    $query->with(['admin_user']);
                }
            );

        if ($request->ajax()) {
            $request->session()->forget('user_notification');
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }
        // session(["user_notification" => $request['page']]);//store session
        // session('user_notification');//get session

        return view('admin.user-notification.index', [
            'data' => $data,
            'subscriber' => $subscriber,
            'state' => $state,
            'message' => session('user_notification'),
            'assessment' => $assessment,
            'resource_material' => $resource_material,
            'case_definition' => $case_definition,
            'dignosis_algo' => $dignosis_algo,
            'treatment_algo' => $treatment_algo,
            'guidance_on_adr' => $guidance_on_adr,
            'latent_tb_infection' => $latent_tb_infection,
            'differential_care_algo' => $differential_care_algo,
            'cgc_algo' => $cgc_algo,
            'dynamic_algo_master' => $dynamic_algo_master
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
        $this->authorize('admin.user-notification.create');
        $login_user = \Auth::user()->id;
        $assignedState = \Auth::user()->state;
        $cadres = Cadre::get(['id', 'title', 'cadre_type']);
        $masterData = \StateWiseFilterData::getStateWiseFilterDataWithSubscriber();
        $states = $masterData['state'];
        $districts = $masterData['district'];
        $subscriber = $masterData['subscriber'];
        $cadres = $masterData['cadres'];
        $country = $masterData['country'];

        if (\Auth::user()->roles[0]['id'] == 1 || \Auth::user()->roles[0]['id'] == 2) {

            $resource_material = ResourceMaterial::get(['id', 'title']);
            $case_definition = CaseDefinition::where('activated', 1)->get(['id', 'title']);
            $dignosis_algo = DiagnosesAlgorithm::where('activated', 1)->get(['id', 'title']);
            $cgc_algo = CgcInterventionsAlgorithm::where('activated', 1)->get(['id', 'title']);
            $differential_care_algo = DifferentialCareAlgorithm::where('activated', 1)->get(['id', 'title']);
            $guidance_on_adr = GuidanceOnAdverseDrugReaction::where('activated', 1)->get(['id', 'title']);
            $latent_tb_infection = LatentTbInfection::where('activated', 1)->get(['id', 'title']);
            $treatment_algo = TreatmentAlgorithm::where('activated', 1)->get(['id', 'title']);
            $dynamic_algo_master = DynamicAlgoMaster::where('active', 1)->get(['id', 'name as title']);
            $assessment = Assessment::where('activated', 1)->get(['id', 'assessment_title->en as title']);
        } else {
            $resource_material = ResourceMaterial::whereRaw("substr('" . $assignedState . "',state)")->get(['id', 'title']);
            $case_definition = CaseDefinition::whereRaw("substr('" . $assignedState . "',state_id)")->where('activated', 1)->get(['id', 'title']);
            $dignosis_algo = DiagnosesAlgorithm::whereRaw("substr('" . $assignedState . "',state_id)")->where('activated', 1)->get(['id', 'title']);
            $cgc_algo = CgcInterventionsAlgorithm::whereRaw("substr('" . $assignedState . "',state_id)")->where('activated', 1)->get(['id', 'title']);
            $differential_care_algo = DifferentialCareAlgorithm::whereRaw("substr('" . $assignedState . "',state_id)")->where('activated', 1)->get(['id', 'title']);
            $guidance_on_adr = GuidanceOnAdverseDrugReaction::whereRaw("substr('" . $assignedState . "',state_id)")->where('activated', 1)->get(['id', 'title']);
            $latent_tb_infection = LatentTbInfection::whereRaw("substr('" . $assignedState . "',state_id)")->where('activated', 1)->get(['id', 'title']);
            $treatment_algo = TreatmentAlgorithm::whereRaw("substr('" . $assignedState . "',state_id)")->where('activated', 1)->get(['id', 'title']);
            $dynamic_algo_master = DynamicAlgoMaster::where('active', 1)->get(['id', 'name as title']);
            $assessment = Assessment::where('created_by', $login_user)->where('activated', 1)->get(['id', 'assessment_title->en as title']);
        }

        if (\Auth::user()->roles[0]['id'] == 10) {
            $cadres = Cadre::whereIn('id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->get(['id', 'title', 'cadre_type']);
        }

        return view('admin.user-notification.create', [
            'subscriber' => $subscriber,
            'cadre' => $cadres,
            'state' => $states,
            'district' => $districts,
            'country' => $country,
            'user_state' => \Auth::user()->state,
            'user_role' => \Auth::user()->roles[0]['id'],
            'assessment' => $assessment,
            'resource_material' => $resource_material,
            'case_definition' => $case_definition,
            'dignosis_algo' => $dignosis_algo,
            'cgc_algo' => $cgc_algo,
            'differential_care_algo' => $differential_care_algo,
            'guidance_on_adr' => $guidance_on_adr,
            'latent_tb_infection' => $latent_tb_infection,
            'treatment_algo' => $treatment_algo,
            'dynamic_algo_master' => $dynamic_algo_master,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUserNotification $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreUserNotification $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        $specificUserIds = collect();
        foreach ($request['user_id'] as $user) {
            $specificUserIds->push($user['id']);
        }
        $sanitized['user_id'] = implode(',', $specificUserIds->toArray());

        $selectedCadres = array_pluck($sanitized['cadre_id'], 'id');
        $sanitized['cadre_id'] = implode(",", $selectedCadres);

        if (isset($request['country_id']) && $request['country_id'] != '') {

            $sanitized['country_id'] = $request['country_id']['id'];
        } else {
            $sanitized['country_id'] = 0;
        }

        $selectedStates = array_pluck($sanitized['state_id'], 'id');
        $sanitized['state_id'] = implode(",", $selectedStates);

        $selectedDistricts = array_pluck($sanitized['district_id'], 'id');
        $sanitized['district_id'] = implode(",", $selectedDistricts);

        if ($request['type'] == 'user-specific') {
            $userIds = $specificUserIds;
        } elseif ($request['type'] == 'multiple-filters') {
            $userIds = Subscriber::whereIn('cadre_id', $selectedCadres);

            if (count($selectedDistricts) > 0) { //ignore State selection when district selected.
                $userIds = $userIds->whereIn('district_id', $selectedDistricts);
            } else {
                $userIds = $userIds->whereIn('state_id', $selectedStates);
            }
            $userIds = $userIds->pluck('id');
        } else {
            $userIds = Subscriber::pluck('id');
        }

        if ($request['is_deeplinking'] == 1) {
            $sanitized['type_title'] = $request['type_title']['id'];
        } else {
            $sanitized['type_title'] = "general";
        }

        $newRequest['subscriber_id'] = implode(",", $userIds->toArray());
        $newRequest['title'] = $request['title'];
        $newRequest['description'] = $request['description'];
        $newRequest['type'] = $request['automatic_notification_type'];
        if ($request['automatic_notification_type'] == "Assessment") {
            $newRequest['linking_url'] = Config::get('app.GENERAL.frontend_url') . "/FutureAssessment"; //https://app.nikshay-setu.in
            $newRequest['type'] = "Assessment";
            $type = "Assessment";
        } else if ($request['automatic_notification_type'] == "Resource Material") {
            $material_parentid = ResourceMaterial::where('id', $request['type_title']['id'])->get(['title', 'type_of_materials', 'parent_id'])[0];
            $newRequest['linking_url'] = Config::get('app.GENERAL.frontend_url') . "/Materials/$material_parentid->parent_id/$material_parentid->type_of_materials/$material_parentid->title";
            $newRequest['type'] = "Assessment";
            $type = "Resource Material";
        } else if ($request['automatic_notification_type'] == "Case Definitions") {
            $newRequest['linking_url'] = Config::get('app.GENERAL.frontend_url') . "/AlgorithmList/TITLE_CASE_DEFINITION/Case Definition/null";
            $newRequest['type'] = "Algorithm";
            $type = "Algorithm";
        } else if ($request['automatic_notification_type'] == "Diagnosis Algorithms") {
            $newRequest['linking_url'] = Config::get('app.GENERAL.frontend_url') . "/AlgorithmList/TITLE_DIAGNOSIS_ALGORITHM/Diagnosis Algorithm/null";
            $newRequest['type'] = "Algorithm";
            $type = "Algorithm";
        } else if ($request['automatic_notification_type'] == "Treatment Algorithms") {
            $newRequest['linking_url'] = Config::get('app.GENERAL.frontend_url') . "/AlgorithmList/TITLE_TREATMENT_ALGORITHM/Treatment Algorithm/null";
            $newRequest['type'] = "Algorithm";
            $type = "Algorithm";
        } else if ($request['automatic_notification_type'] == "Guidance On Adverse Drug Reactions") {
            $newRequest['linking_url'] = Config::get('app.GENERAL.frontend_url') . "/AlgorithmList/TITLE_GUIDANCE_ON_ADR/Guidance on ADR/null";
            $newRequest['type'] = "Algorithm";
            $type = "Algorithm";
        } else if ($request['automatic_notification_type'] == "PMTPT") {
            $newRequest['linking_url'] = Config::get('app.GENERAL.frontend_url') . "/AlgorithmList/TITLE_LATENT_TB_INFECTION/Latent TB Infection/null";
            $newRequest['type'] = "Algorithm";
            $type = "Algorithm";
        } else if ($request['automatic_notification_type'] == "Differential Care Algorithms") {
            $newRequest['linking_url'] = Config::get('app.GENERAL.frontend_url') . "/AlgorithmList/TITLE_DIFFERENTIANTED_CARE/Differentiated Care Of TB Patients/null";
            $newRequest['type'] = "Assessment";
            $type = "Algorithm";
        } else if ($request['automatic_notification_type'] == "NTEP Interventions Algorithms") {
            $newRequest['linking_url'] = Config::get('app.GENERAL.frontend_url') . "/Algorithms/TITLE_CGC_INTERVENTION/NTEP";
            $newRequest['type'] = "Algorithm";
            $type = "Algorithm";
        } else if ($request['automatic_notification_type'] == "Dynamic Algorithm") {
            // $material_parentid = DynamicAlgoMaster::where('id',$request['type_title']['id'])->get(['title','type_of_materials','parent_id'])[0];
            $title = $request['type_title']['title'];
            $id = $request['type_title']['id'];
            $newRequest['linking_url'] = Config::get('app.GENERAL.frontend_url') . "/AlgorithmList/$title/Dynamic/$id";
            $newRequest['type'] = "Dynamic";
            $type = "Dynamic";
        } else {
            $newRequest['linking_url'] = Config::get('app.GENERAL.frontend_url') . "/HomeScreen";
            $newRequest['type'] = "General";
            $type = "General";
        }
        // Store the UserNotification
        $sanitized['status'] = 'Pending';
        $userNotification = UserNotification::create($sanitized);
        $newRequest['created_by'] = \Auth::user()->id;
        AutomaticNotification::create($newRequest);
        $message = $this->sendNotification($userNotification, $userIds, $type, $newRequest['linking_url'], $userNotification['id']);
        Log::info($message);

        // $message = "Successfully send notification";
        // if(isset($response['error'])){
        //     $message = "User Not Found";
        // }else{
        //     $successCount = isset($response['successFullCount']) && $response['successFullCount'] > 0 ? $response['successFullCount'] : 0; 
        //     $failCount = isset($response['failedCount']) && $response['failedCount'] > 0 ? $response['failedCount'] : 0; 
        //     $message = "You have successfully added notification. Your notification is successfully send to ".$successCount ." Subscribers and Failed for " .$failCount . " Subscribers.";
        // }

        if ($request->ajax()) {

            session(['user_notification' => $message]);
            return [
                'redirect' => url('admin/user-notifications'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/user-notifications')->with('message', $message);
    }

    public function sendNotification($notification, $user, $type, $link, $notification_id)
    {
        // $device_id = UserDeviceToken::whereIn('user_id',$user)->get('notification_token');
        // if($type == "Resource Material"){

        //     return SendNotificationController::resourceMaterial($notification,$device_id,$link);

        // }else if($type == "Algorithm"){

        //     return SendNotificationController::newModules($notification,$device_id,$link);
        // }else if($type == "Dynamic"){

        //     return SendNotificationController::newModules($notification,$device_id,$link);
        // }else if($type == "Assessment"){
        //     $assessment = Assessment::where('id',$notification['type_title'])->get(['id','assessment_title','time_to_complete'])[0];
        //     $assessment->title = $notification->title;
        //     $assessment->description = $notification->description;
        //     return SendNotificationController::InitalAssessmentInvitation($assessment,$device_id);
        // }else{

        //     return SendNotificationController::sendNotification($notification,$device_id);
        // }
        log::info('calling job');
        dispatch(new sendNotification($notification, $user, $type, $link, $notification_id));
        Log::info('job called');
        $message = "Notification queued. Check status later.";
        return $message;
    }

    /**
     * Display the specified resource.
     *
     * @param UserNotification $userNotification
     * @throws AuthorizationException
     * @return void
     */
    public function show(UserNotification $userNotification)
    {
        $this->authorize('admin.user-notification.show', $userNotification);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param UserNotification $userNotification
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(UserNotification $userNotification)
    {
        $this->authorize('admin.user-notification.edit', $userNotification);
        $cadres = Cadre::get(['id', 'title', 'cadre_type']);
        $masterData = \StateWiseFilterData::getStateWiseFilterDataWithSubscriber();
        $states = $masterData['state'];
        $districts = $masterData['district'];
        $subscriber = $masterData['subscriber'];
        $assessment = Assessment::where('activated', 1)->get(['id', 'assessment_title']);
        $resource_material = ResourceMaterial::get(['id', 'assessment_title']);
        $case_definition = CaseDefinition::where('activated', 1)->get(['id', 'title']);
        $dignosis_algo = DiagnosesAlgorithm::where('activated', 1)->get(['id', 'title']);
        $cgc_algo = CgcInterventionsAlgorithm::where('activated', 1)->get(['id', 'title']);
        $differential_care_algo = DifferentialCareAlgorithm::where('activated', 1)->get(['id', 'title']);
        $guidance_on_adr = GuidanceOnAdverseDrugReaction::where('activated', 1)->get(['id', 'title']);
        $latent_tb_infection = LatentTbInfection::where('activated', 1)->get(['id', 'title']);
        $treatment_algo = TreatmentAlgorithm::where('activated', 1)->get(['id', 'title']);

        if (isset($userNotification['user_id']) && $userNotification['user_id'] != "") {
            $userNotification['user_id'] = explode(',', $userNotification['user_id']);
        }

        $userNotification['all_subscriber'] = $subscriber;

        return view('admin.user-notification.edit', [
            'userNotification' => $userNotification,
            'subscriber' => $subscriber,
            'cadre' => $cadres,
            'state' => $states,
            'district' => $districts,
            'assessment' => $assessment,
            'resource_material' => $resource_material,
            'case_definition' => $case_definition,
            'dignosis_algo' => $dignosis_algo,
            'cgc_algo' => $cgc_algo,
            'differential_care_algo' => $differential_care_algo,
            'guidance_on_adr' => $guidance_on_adr,
            'latent_tb_infection' => $latent_tb_infection,
            'treatment_algo' => $treatment_algo
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserNotification $request
     * @param UserNotification $userNotification
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateUserNotification $request, UserNotification $userNotification)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        $sanitized['user_id'] = implode(",", $sanitized['user_id']);

        $sanitized['cadre_id'] = array_pluck($sanitized['cadre_id'], 'id');
        $sanitized['cadre_id'] = implode(",", $sanitized['cadre_id']);

        $sanitized['state_id'] = array_pluck($sanitized['state_id'], 'id');
        $sanitized['state_id'] = implode(",", $sanitized['state_id']);

        $sanitized['district_id'] = array_pluck($sanitized['district_id'], 'id');
        $sanitized['district_id'] = implode(",", $sanitized['district_id']);

        // Update changed values UserNotification
        $userNotification->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/user-notifications'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/user-notifications');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyUserNotification $request
     * @param UserNotification $userNotification
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyUserNotification $request, UserNotification $userNotification)
    {
        $userNotification->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyUserNotification $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyUserNotification $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('userNotifications')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
