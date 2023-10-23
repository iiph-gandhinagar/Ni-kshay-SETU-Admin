<?php

/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Brackets\AdminAuth\Models\AdminUser::class, function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->email,
        'password' => bcrypt($faker->password),
        'remember_token' => null,
        'activated' => true,
        'forbidden' => $faker->boolean(),
        'language' => 'en',
        'state' => $faker->sentence,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        'last_login_at' => $faker->dateTime,

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Cadre::class, static function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\State::class, static function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\District::class, static function (Faker\Generator $faker) {
    return [
        'state_id' => $faker->randomNumber(5),
        'title' => $faker->sentence,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Block::class, static function (Faker\Generator $faker) {
    return [
        'state_id' => $faker->randomNumber(5),
        'district_id' => $faker->randomNumber(5),
        'title' => $faker->sentence,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\AssessmentQuestion::class, static function (Faker\Generator $faker) {
    return [
        'assessment_id' => $faker->randomNumber(5),
        'question' => $faker->sentence,
        'option1' => $faker->sentence,
        'option2' => $faker->sentence,
        'option3' => $faker->sentence,
        'option4' => $faker->sentence,
        'correct_answer' => $faker->sentence,
        'order_index' => $faker->randomNumber(5),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\UserAssessment::class, static function (Faker\Generator $faker) {
    return [
        'assessment_id' => $faker->randomNumber(5),
        'user_id' => $faker->randomNumber(5),
        'total_marks' => $faker->randomNumber(5),
        'obtained_marks' => $faker->randomNumber(5),
        'attempted' => $faker->randomNumber(5),
        'right_answers' => $faker->randomNumber(5),
        'wrong_answers' => $faker->randomNumber(5),
        'skipped' => $faker->randomNumber(5),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\ResourceMaterial::class, static function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence,
        'type_of_materials' => $faker->text(),
        'state' => $faker->sentence,
        'cadre' => $faker->sentence,
        'parent_id' => $faker->sentence,
        'icon_type' => $faker->sentence,
        'index' => $faker->sentence,
        'created_by' => $faker->randomNumber(5),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\CgcIntervention::class, static function (Faker\Generator $faker) {
    return [
        'chapter_title' => $faker->sentence,
        'video_title' => $faker->sentence,
        'description' => $faker->sentence,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Symptom::class, static function (Faker\Generator $faker) {
    return [
        'category' => $faker->sentence,
        'symptoms_title' => $faker->sentence,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
$factory->define(App\Models\HealthFacility::class, static function (Faker\Generator $faker) {
    return [
        'state_id' => $faker->randomNumber(5),
        'district_id' => $faker->randomNumber(5),
        'block_id' => $faker->randomNumber(5),
        'health_facility_code' => $faker->sentence,
        'DMC' => $faker->boolean(),
        'TRUNAT' => $faker->boolean(),
        'CBNAAT' => $faker->boolean(),
        'X_RAY' => $faker->boolean(),
        'ICTC' => $faker->boolean(),
        'CDST/LPA_Lab' => $faker->boolean(),
        'DM_SCREENING/CONFIRMATION_CENTER' => $faker->boolean(),
        'Tobacco_Cessation_clinic' => $faker->boolean(),
        'ANC_Clinic' => $faker->boolean(),
        'Nutritional_Rehabilitation_centre' => $faker->boolean(),
        'De_addiction_centres' => $faker->boolean(),
        'ART_Centre' => $faker->boolean(),
        'District_DRTB_Centre' => $faker->boolean(),
        'NODAL_DRTB_CENTER' => $faker->boolean(),
        'IRL' => $faker->boolean(),
        'Pediatric_Care_Facility' => $faker->boolean(),
        'longitude' => $faker->randomFloat,
        'latitude' => $faker->randomFloat,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Enquiry::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'email' => $faker->email,
        'phone' => $faker->sentence,
        'subject' => $faker->sentence,
        'message' => $faker->sentence,

    ];
});

$factory->define(App\Models\ChatKeyword::class, static function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence,
        'hit' => $faker->sentence,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\ChatQuestionHit::class, static function (Faker\Generator $faker) {
    return [
        'question_id' => $faker->sentence,
        'subscriber_id' => $faker->sentence,
        'session_token' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Subscriber::class, static function (Faker\Generator $faker) {
    return [
        'api_token' => $faker->sentence,
        'name' => $faker->firstName,
        'phone_no' => $faker->sentence,
        'password' => bcrypt($faker->password),
        'cadre_type' => $faker->sentence,
        'is_verified' => $faker->boolean(),
        'cadre_id' => $faker->randomNumber(5),
        'block_id' => $faker->randomNumber(5),
        'district_id' => $faker->randomNumber(5),
        'state_id' => $faker->randomNumber(5),
        'health_facility_id' => $faker->randomNumber(5),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});

/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Otp::class, static function (Faker\Generator $faker) {
    return [
        'phone_no' => $faker->sentence,
        'user_id' => $faker->randomNumber(5),
        'otp' => $faker->randomNumber(6),
        'is_verified' => $faker->boolean(),
        'message_body' => $faker->sentence,
        'is_delivered' => $faker->boolean(),
        'via' => $faker->sentence,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Assessment::class, static function (Faker\Generator $faker) {
    return [
        'assessment_title' => $faker->sentence,
        'time_to_complete' => $faker->randomNumber(5),
        'cadre_id' => $faker->sentence,
        'state_id' => $faker->sentence,
        'activated' => $faker->boolean(),
        'district_id' => $faker->sentence,
        'cadre_type' => $faker->sentence,
        'created_by' => $faker->randomNumber(5),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        'assessment_json' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\AssessmentQuestion::class, static function (Faker\Generator $faker) {
    return [
        'assessment_id' => $faker->randomNumber(5),
        'question' => $faker->sentence,
        'option1' => $faker->sentence,
        'option2' => $faker->sentence,
        'option3' => $faker->sentence,
        'option4' => $faker->sentence,
        'correct_answer' => $faker->sentence,
        'order_index' => $faker->randomNumber(5),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'question_value_json' => ['en' => $faker->sentence],
        'option1_value_json' => ['en' => $faker->sentence],
        'option2_value_json' => ['en' => $faker->sentence],
        'option3_value_json' => ['en' => $faker->sentence],
        'option4_value_json' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\CaseDefinition::class, static function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence,
        'node_type' => $faker->sentence,
        'is_expandable' => $faker->boolean(),
        'has_options' => $faker->boolean(),
        'parent_id' => $faker->sentence,
        'description' => $faker->text(),
        'index' => $faker->randomNumber(5),
        'redirect_algo_type' => $faker->sentence,
        'redirect_node_id' => $faker->sentence,
        'activated' => $faker->boolean(),
        'header' => $faker->sentence,
        'sub_header' => $faker->sentence,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'title' => ['en' => $faker->sentence],
        'description' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\DiagnosesAlgorithm::class, static function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence,
        'node_type' => $faker->sentence,
        'is_expandable' => $faker->boolean(),
        'has_options' => $faker->boolean(),
        'parent_id' => $faker->sentence,
        'description' => $faker->text(),
        'index' => $faker->randomNumber(5),
        'redirect_algo_type' => $faker->sentence,
        'redirect_node_id' => $faker->sentence,
        'header' => $faker->sentence,
        'sub_header' => $faker->sentence,
        'activated' => $faker->boolean(),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'title_value_json' => ['en' => $faker->sentence],
        'description_value_json' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\TreatmentAlgorithm::class, static function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence,
        'node_type' => $faker->sentence,
        'is_expandable' => $faker->boolean(),
        'has_options' => $faker->boolean(),
        'parent_id' => $faker->sentence,
        'description' => $faker->text(),
        'index' => $faker->randomNumber(5),
        'redirect_algo_type' => $faker->sentence,
        'redirect_node_id' => $faker->sentence,
        'header' => $faker->sentence,
        'sub_header' => $faker->sentence,
        'activated' => $faker->boolean(),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'title_value_json' => ['en' => $faker->sentence],
        'description_value_json' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\GuidanceOnAdverseDrugReaction::class, static function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence,
        'node_type' => $faker->sentence,
        'is_expandable' => $faker->boolean(),
        'has_options' => $faker->boolean(),
        'parent_id' => $faker->sentence,
        'description' => $faker->text(),
        'index' => $faker->randomNumber(5),
        'redirect_algo_type' => $faker->sentence,
        'redirect_node_id' => $faker->sentence,
        'header' => $faker->sentence,
        'sub_header' => $faker->sentence,
        'activated' => $faker->boolean(),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'title' => ['en' => $faker->sentence],
        'description' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\LatentTbInfection::class, static function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence,
        'node_type' => $faker->sentence,
        'is_expandable' => $faker->boolean(),
        'has_options' => $faker->boolean(),
        'parent_id' => $faker->sentence,
        'description' => $faker->text(),
        'index' => $faker->randomNumber(5),
        'redirect_algo_type' => $faker->sentence,
        'redirect_node_id' => $faker->sentence,
        'header' => $faker->sentence,
        'sub_header' => $faker->sentence,
        'activated' => $faker->boolean(),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'title' => ['en' => $faker->sentence],
        'description' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\MasterCm::class, static function (Faker\Generator $faker) {
    return [
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'title' => ['en' => $faker->sentence],
        'description' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\MasterCm::class, static function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'description' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Symptom::class, static function (Faker\Generator $faker) {
    return [
        'category' => $faker->sentence,
        'symptoms_title' => $faker->sentence,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'symptoms_title_json' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\ChatKeyword::class, static function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence,
        'hit' => $faker->sentence,
        'modules' => $faker->sentence,
        'sub_modules' => $faker->sentence,
        'resource_material' => $faker->sentence,
        'custom_ordering' => $faker->sentence,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'title_json' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\CgcInterventionsAlgorithm::class, static function (Faker\Generator $faker) {
    return [
        'node_type' => $faker->sentence,
        'is_expandable' => $faker->boolean(),
        'has_options' => $faker->boolean(),
        'parent_id' => $faker->sentence,
        'index' => $faker->randomNumber(5),
        'redirect_algo_type' => $faker->sentence,
        'redirect_node_id' => $faker->sentence,
        'activated' => $faker->boolean(),
        'header' => $faker->sentence,
        'sub_header' => $faker->sentence,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'title' => ['en' => $faker->sentence],
        'description' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\DifferentialCareAlgorithm::class, static function (Faker\Generator $faker) {
    return [
        'node_type' => $faker->sentence,
        'is_expandable' => $faker->boolean(),
        'has_options' => $faker->boolean(),
        'parent_id' => $faker->sentence,
        'index' => $faker->randomNumber(5),
        'redirect_algo_type' => $faker->sentence,
        'redirect_node_id' => $faker->sentence,
        'header' => $faker->sentence,
        'sub_header' => $faker->sentence,
        'activated' => $faker->boolean(),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'title' => ['en' => $faker->sentence],
        'description' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\ChatQuestion::class, static function (Faker\Generator $faker) {
    return [
        'hit' => $faker->sentence,
        'cadre_id' => $faker->sentence,
        'category' => $faker->sentence,
        'activated' => $faker->boolean(),
        'like_count' => $faker->randomNumber(5),
        'dislike_count' => $faker->randomNumber(5),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'question' => ['en' => $faker->sentence],
        'answer' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\PatientAssessment::class, static function (Faker\Generator $faker) {
    return [
        'nikshay_id' => $faker->sentence,
        'patient_name' => $faker->sentence,
        'age' => $faker->sentence,
        'gender' => $faker->sentence,
        'PULSE_RATE' => $faker->randomNumber(5),
        'TEMPERATURE' => $faker->randomNumber(5),
        'BLOOD_PRESSURE' => $faker->sentence,
        'RESPIRATORY_RATE' => $faker->randomNumber(5),
        'OXYGEN_SATURATION' => $faker->randomNumber(5),
        'TEXT_BMI' => $faker->randomNumber(5),
        'TEXT_MUAC' => $faker->randomNumber(5),
        'PEDAL_OEDEMA' => $faker->sentence,
        'GENERAL_CONDITION' => $faker->sentence,
        'TEXT_ICTERUS' => $faker->sentence,
        'TEXT_HEMOGLOBIN' => $faker->randomNumber(5),
        'COUNT_WBC' => $faker->randomNumber(5),
        'TEXT_RBS' => $faker->randomNumber(5),
        'TEXT_HIV' => $faker->sentence,
        'TEXT_XRAY' => $faker->sentence,
        'TEXT_HEMOPTYSIS' => $faker->sentence,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'patient_selected_data' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\ModuleMappingToName::class, static function (Faker\Generator $faker) {
    return [
        'module_name' => $faker->sentence,
        'mapping_name' => $faker->sentence,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\UserNotification::class, static function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence,
        'description' => $faker->text(),
        'type' => $faker->sentence,
        'user_id' => $faker->randomNumber(5),
        'state_id' => $faker->sentence,
        'district_id' => $faker->sentence,
        'cadre_type' => $faker->sentence,
        'cadre_id' => $faker->sentence,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Role::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'guard_name' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});

/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\RoleHasPermission::class, static function (Faker\Generator $faker) {
    return [
        'permission_id' => $faker->sentence,
        'role_id' => $faker->sentence,
    ];
});

/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Permission::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'guard_name' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});

/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\MessageNotification::class, static function (Faker\Generator $faker) {
    return [
        'user_name' => $faker->sentence,
        'phone_no' => $faker->sentence,
        'notification_message' => $faker->text(),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});

$factory->define(App\Models\ChatbotActivity::class, static function (Faker\Generator $faker) {
    return [
        'user_id' => $faker->randomNumber(5),
        'action' => $faker->sentence,
        'payload' => $faker->sentence,
        'plateform' => $faker->sentence,
        'ip_address' => $faker->sentence,
        'tag_id' => $faker->randomNumber(5),
        'question_id' => $faker->randomNumber(5),
        'like' => $faker->randomNumber(5),
        'dislike' => $faker->randomNumber(5),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});

/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\AppManagementFlag::class, static function (Faker\Generator $faker) {
    return [
        'variable' => $faker->sentence,
        'type' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
        'value' => ['en' => $faker->sentence],

    ];
});

/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\TModuleMaster::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});

$factory->define(App\Models\TSubModuleMaster::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'module_id' => $faker->sentence,
        'existing_module_ref' => $faker->sentence,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\TTrainingTag::class, static function (Faker\Generator $faker) {
    return [
        'tag' => $faker->sentence,
        'is_fix_response' => $faker->boolean(),
        'response' => $faker->text(),
        'pattern' => $faker->sentence,
        'like_count' => $faker->randomNumber(5),
        'dislike_count' => $faker->randomNumber(5),
        'questions' => $faker->sentence,
        'modules' => $faker->sentence,
        'sub_modules' => $faker->sentence,
        'resource_material' => $faker->sentence,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\DynamicAlgoMaster::class, static function (Faker\Generator $faker) {
    return [
        'name' => $faker->firstName,
        'section' => $faker->sentence,
        'active' => $faker->boolean(),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\DynamicAlgorithm::class, static function (Faker\Generator $faker) {
    return [
        'algo_key' => $faker->randomNumber(5),
        'node_type' => $faker->sentence,
        'is_expandable' => $faker->boolean(),
        'has_options' => $faker->boolean(),
        'parent_id' => $faker->sentence,
        'index' => $faker->randomNumber(5),
        'redirect_algo_type' => $faker->sentence,
        'redirect_node_id' => $faker->sentence,
        'header' => $faker->sentence,
        'sub_header' => $faker->sentence,
        'activated' => $faker->boolean(),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'title' => ['en' => $faker->sentence],
        'description' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Country::class, static function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence,
        'response_json' => ['en' => $faker->sentence],
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});
$factory->define(App\Models\TTrainingTag::class, static function (Faker\Generator $faker) {
    return [
        'tag' => $faker->sentence,
        'pattern' => $faker->text(),
        'is_fix_response' => $faker->boolean(),
        'like_count' => $faker->randomNumber(5),
        'dislike_count' => $faker->randomNumber(5),
        'response' => $faker->text(),
        'questions' => $faker->sentence,
        'modules' => $faker->sentence,
        'sub_modules' => $faker->sentence,
        'resource_material' => $faker->sentence,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\ChatQuestion::class, static function (Faker\Generator $faker) {
    return [
        'hit' => $faker->sentence,
        'cadre_id' => $faker->sentence,
        'category' => $faker->sentence,
        'activated' => $faker->boolean(),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'question' => ['en' => $faker->sentence],
        'answer' => ['en' => $faker->sentence],
        'training_question1' => ['en' => $faker->sentence],
        'training_question2' => ['en' => $faker->sentence],
        'training_question3' => ['en' => $faker->sentence],
        'training_question4' => ['en' => $faker->sentence],
        'training_question5' => ['en' => $faker->sentence],
        'training_question6' => ['en' => $faker->sentence],
        'training_question7' => ['en' => $faker->sentence],
        'training_question8' => ['en' => $faker->sentence],
        'training_question9' => ['en' => $faker->sentence],
        'training_question10' => ['en' => $faker->sentence],
        'modules' => ['en' => $faker->sentence],
        'sub_modules' => ['en' => $faker->sentence],

    ];
});

/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\UserAppVersion::class, static function (Faker\Generator $faker) {
    return [
        'user_id' => $faker->randomNumber(5),
        'user_name' => $faker->sentence,
        'app_version' => $faker->sentence,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\ResourceMaterial::class, static function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence,
        'type_of_materials' => $faker->sentence,
        'country_id' => $faker->randomNumber(5),
        'state' => $faker->sentence,
        'cadre' => $faker->sentence,
        'parent_id' => $faker->sentence,
        'icon_type' => $faker->sentence,
        'index' => $faker->sentence,
        'created_by' => $faker->randomNumber(5),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'title_json' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Tour::class, static function (Faker\Generator $faker) {
    return [
        'active' => $faker->boolean(),
        'created_at' => $faker->dateTime,
        'default' => $faker->boolean(),
        'deleted_at' => null,
        'title' => $faker->sentence,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\TourSlide::class, static function (Faker\Generator $faker) {
    return [
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'description' => $faker->sentence,
        'title' => $faker->sentence,
        'tour_id' => $faker->randomNumber(5),
        'type' => $faker->sentence,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Tour::class, static function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence,
        'active' => $faker->boolean(),
        'default' => $faker->boolean(),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Tour::class, static function (Faker\Generator $faker) {
    return [
        'active' => $faker->boolean(),
        'default' => $faker->boolean(),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'title' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\TourSlide::class, static function (Faker\Generator $faker) {
    return [
        'tour_id' => $faker->randomNumber(5),
        'type' => $faker->sentence,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'title' => ['en' => $faker->sentence],
        'description' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\StaticBlog::class, static function (Faker\Generator $faker) {
    return [
        'active' => $faker->boolean(),
        'author' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'keywords' => $faker->sentence,
        'order_index' => $faker->randomNumber(5),
        'source' => $faker->sentence,
        'updated_at' => $faker->dateTime,

        'description' => ['en' => $faker->sentence],
        'short_description' => ['en' => $faker->sentence],
        'title' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\StaticFaq::class, static function (Faker\Generator $faker) {
    return [
        'active' => $faker->boolean(),
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'order_index' => $faker->randomNumber(5),
        'updated_at' => $faker->dateTime,

        'description' => ['en' => $faker->sentence],
        'question' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\StaticAppConfig::class, static function (Faker\Generator $faker) {
    return [
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'key' => $faker->sentence,
        'updated_at' => $faker->dateTime,

        'value_json' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\KeyFeature::class, static function (Faker\Generator $faker) {
    return [
        'active' => $faker->boolean(),
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'order_index' => $faker->randomNumber(5),
        'updated_at' => $faker->dateTime,

        'description' => ['en' => $faker->sentence],
        'title' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\StaticTestimonial::class, static function (Faker\Generator $faker) {
    return [
        'active' => $faker->boolean(),
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'order_index' => $faker->randomNumber(5),
        'updated_at' => $faker->dateTime,

        'description' => ['en' => $faker->sentence],
        'name' => ['en' => $faker->firstName],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\StaticRelease::class, static function (Faker\Generator $faker) {
    return [
        'active' => $faker->boolean(),
        'created_at' => $faker->dateTime,
        'date' => $faker->sentence,
        'deleted_at' => null,
        'order_index' => $faker->randomNumber(5),
        'updated_at' => $faker->dateTime,

        'bugs_fix' => ['en' => $faker->sentence],
        'features' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\StaticEnquiry::class, static function (Faker\Generator $faker) {
    return [
        'subject' => $faker->sentence,
        'email' => $faker->email,
        'message' => $faker->sentence,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\StaticWhatWeDo::class, static function (Faker\Generator $faker) {
    return [
        'active' => $faker->boolean(),
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'order_index' => $faker->randomNumber(5),
        'updated_at' => $faker->dateTime,

        'location' => ['en' => $faker->sentence],
        'title' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\StaticBlog::class, static function (Faker\Generator $faker) {
    return [
        'active' => $faker->boolean(),
        'author' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'keywords' => $faker->sentence,
        'order_index' => $faker->randomNumber(5),
        'slug' => $faker->unique()->slug,
        'source' => $faker->sentence,
        'updated_at' => $faker->dateTime,

        'description' => ['en' => $faker->sentence],
        'short_description' => ['en' => $faker->sentence],
        'title' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\StaticModule::class, static function (Faker\Generator $faker) {
    return [
        'active' => $faker->boolean(),
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'order_index' => $faker->randomNumber(5),
        'updated_at' => $faker->dateTime,

        'description' => ['en' => $faker->sentence],
        'title' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\StaticResourceMaterial::class, static function (Faker\Generator $faker) {
    return [
        'active' => $faker->boolean(),
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'order_index' => $faker->randomNumber(5),
        'updated_at' => $faker->dateTime,

        'title' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\StaticResourceMaterial::class, static function (Faker\Generator $faker) {
    return [
        'active' => $faker->boolean(),
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'order_index' => $faker->randomNumber(5),
        'type' => $faker->sentence,
        'updated_at' => $faker->dateTime,

        'title' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\StaticResourceMaterial::class, static function (Faker\Generator $faker) {
    return [
        'active' => $faker->boolean(),
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'order_index' => $faker->randomNumber(5),
        'type_of_materials' => $faker->sentence,
        'updated_at' => $faker->dateTime,

        'title' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\StaticModule::class, static function (Faker\Generator $faker) {
    return [
        'slug' => $faker->unique()->slug,
        'order_index' => $faker->randomNumber(5),
        'active' => $faker->boolean(),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'title' => ['en' => $faker->sentence],
        'description' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\StaticAppConfig::class, static function (Faker\Generator $faker) {
    return [
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'key' => $faker->sentence,
        'type' => $faker->sentence,
        'updated_at' => $faker->dateTime,

        'value_json' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\LbLevel::class, static function (Faker\Generator $faker) {
    return [
        'level' => $faker->sentence,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\LbBadge::class, static function (Faker\Generator $faker) {
    return [
        'level_id' => $faker->randomNumber(5),
        'badge' => $faker->sentence,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\LbTaskList::class, static function (Faker\Generator $faker) {
    return [
        'level' => $faker->randomNumber(5),
        'badges' => $faker->randomNumber(5),
        'mins_spent' => $faker->sentence,
        'sub_module_usage_count' => $faker->sentence,
        'App_opended_count' => $faker->randomNumber(5),
        'chatbot_usage_count' => $faker->randomNumber(5),
        'resource_material_accessed_count' => $faker->randomNumber(5),
        'total_task' => $faker->randomNumber(5),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});

/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\LbSubscriberRankingHistory::class, static function (Faker\Generator $faker) {
    return [
        'lb_subscriber_rankings_id' => $faker->randomNumber(5),
        'subscriber_id' => $faker->randomNumber(5),
        'level_id' => $faker->randomNumber(5),
        'badge_id' => $faker->randomNumber(5),
        'mins_spent_count' => $faker->sentence,
        'sub_module_usage_count' => $faker->sentence,
        'App_opended_count' => $faker->randomNumber(5),
        'chatbot_usage_count' => $faker->randomNumber(5),
        'resource_material_accessed_count' => $faker->randomNumber(5),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\LbSubModuleUsage::class, static function (Faker\Generator $faker) {
    return [
        'subscriber_id' => $faker->randomNumber(5),
        'sub_module' => $faker->sentence,
        'total_time' => $faker->sentence,
        'mins_spent' => $faker->sentence,
        'completed_flag' => $faker->boolean(),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\LbLevel::class, static function (Faker\Generator $faker) {
    return [
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'level' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\LbBadge::class, static function (Faker\Generator $faker) {
    return [
        'level_id' => $faker->randomNumber(5),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'badge' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\UserFeedbackQuestion::class, static function (Faker\Generator $faker) {
    return [
        'feedback_question' => $faker->sentence,
        'feedback_description' => $faker->sentence,
        'feedback_value' => $faker->sentence,
        'feedback_time' => $faker->sentence,
        'feedback_type' => $faker->sentence,
        'is_active' => $faker->boolean(),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\UserFeedbackDetail::class, static function (Faker\Generator $faker) {
    return [
        'subscriber_id' => $faker->randomNumber(5),
        'feedback_id' => $faker->randomNumber(5),
        'ratings' => $faker->randomNumber(5),
        'review' => $faker->text(),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\UserFeedbackHistory::class, static function (Faker\Generator $faker) {
    return [
        'subscriber_id' => $faker->randomNumber(5),
        'feedback_id' => $faker->randomNumber(5),
        'rating' => $faker->randomNumber(5),
        'review' => $faker->text(),
        'skip' => $faker->boolean(),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\UserFeedbackQuestion::class, static function (Faker\Generator $faker) {
    return [
        'feedback_value' => $faker->sentence,
        'feedback_time' => $faker->sentence,
        'feedback_type' => $faker->sentence,
        'is_active' => $faker->boolean(),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'feedback_question' => ['en' => $faker->sentence],
        'feedback_description' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\UserFeedbackQuestion::class, static function (Faker\Generator $faker) {
    return [
        'feedback_value' => $faker->sentence,
        'feedback_time' => $faker->sentence,
        'feedback_type' => $faker->sentence,
        'feedback_days' => $faker->randomNumber(5),
        'is_active' => $faker->boolean(),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'feedback_question' => ['en' => $faker->sentence],
        'feedback_description' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\FlashNews::class, static function (Faker\Generator $faker) {
    return [
        'active' => $faker->boolean(),
        'author' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'order_index' => $faker->randomNumber(5),
        'source' => $faker->sentence,
        'updated_at' => $faker->dateTime,

        'description' => ['en' => $faker->sentence],
        'title' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\CaseDefinition::class, static function (Faker\Generator $faker) {
    return [
        'node_type' => $faker->sentence,
        'is_expandable' => $faker->boolean(),
        'has_options' => $faker->boolean(),
        'parent_id' => $faker->sentence,
        'index' => $faker->randomNumber(5),
        'time_spent' => $faker->sentence,
        'redirect_algo_type' => $faker->sentence,
        'redirect_node_id' => $faker->sentence,
        'activated' => $faker->boolean(),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'title' => ['en' => $faker->sentence],
        'description' => ['en' => $faker->sentence],
        'header' => ['en' => $faker->sentence],
        'sub_header' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\DiagnosesAlgorithm::class, static function (Faker\Generator $faker) {
    return [
        'node_type' => $faker->sentence,
        'is_expandable' => $faker->boolean(),
        'has_options' => $faker->boolean(),
        'parent_id' => $faker->sentence,
        'index' => $faker->randomNumber(5),
        'time_spent' => $faker->sentence,
        'redirect_algo_type' => $faker->sentence,
        'redirect_node_id' => $faker->sentence,
        'activated' => $faker->boolean(),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'title' => ['en' => $faker->sentence],
        'description' => ['en' => $faker->sentence],
        'header' => ['en' => $faker->sentence],
        'sub_header' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\GuidanceOnAdverseDrugReaction::class, static function (Faker\Generator $faker) {
    return [
        'node_type' => $faker->sentence,
        'is_expandable' => $faker->boolean(),
        'has_options' => $faker->boolean(),
        'parent_id' => $faker->sentence,
        'index' => $faker->randomNumber(5),
        'time_spent' => $faker->sentence,
        'redirect_algo_type' => $faker->sentence,
        'redirect_node_id' => $faker->sentence,
        'activated' => $faker->boolean(),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'title' => ['en' => $faker->sentence],
        'description' => ['en' => $faker->sentence],
        'header' => ['en' => $faker->sentence],
        'sub_header' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\TreatmentAlgorithm::class, static function (Faker\Generator $faker) {
    return [
        'node_type' => $faker->sentence,
        'is_expandable' => $faker->boolean(),
        'has_options' => $faker->boolean(),
        'parent_id' => $faker->sentence,
        'index' => $faker->randomNumber(5),
        'time_spent' => $faker->sentence,
        'redirect_algo_type' => $faker->sentence,
        'redirect_node_id' => $faker->sentence,
        'activated' => $faker->boolean(),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'title' => ['en' => $faker->sentence],
        'description' => ['en' => $faker->sentence],
        'header' => ['en' => $faker->sentence],
        'sub_header' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\LatentTbInfection::class, static function (Faker\Generator $faker) {
    return [
        'node_type' => $faker->sentence,
        'is_expandable' => $faker->boolean(),
        'has_options' => $faker->boolean(),
        'parent_id' => $faker->sentence,
        'index' => $faker->randomNumber(5),
        'time_spent' => $faker->sentence,
        'redirect_algo_type' => $faker->sentence,
        'redirect_node_id' => $faker->sentence,
        'activated' => $faker->boolean(),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'title' => ['en' => $faker->sentence],
        'description' => ['en' => $faker->sentence],
        'header' => ['en' => $faker->sentence],
        'sub_header' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\DifferentialCareAlgorithm::class, static function (Faker\Generator $faker) {
    return [
        'node_type' => $faker->sentence,
        'is_expandable' => $faker->boolean(),
        'has_options' => $faker->boolean(),
        'parent_id' => $faker->sentence,
        'time_spent' => $faker->sentence,
        'index' => $faker->randomNumber(5),
        'redirect_algo_type' => $faker->sentence,
        'redirect_node_id' => $faker->sentence,
        'activated' => $faker->boolean(),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'title' => ['en' => $faker->sentence],
        'description' => ['en' => $faker->sentence],
        'header' => ['en' => $faker->sentence],
        'sub_header' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\CgcInterventionsAlgorithm::class, static function (Faker\Generator $faker) {
    return [
        'node_type' => $faker->sentence,
        'is_expandable' => $faker->boolean(),
        'has_options' => $faker->boolean(),
        'parent_id' => $faker->sentence,
        'time_spent' => $faker->sentence,
        'index' => $faker->randomNumber(5),
        'redirect_algo_type' => $faker->sentence,
        'redirect_node_id' => $faker->sentence,
        'activated' => $faker->boolean(),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'title' => ['en' => $faker->sentence],
        'description' => ['en' => $faker->sentence],
        'header' => ['en' => $faker->sentence],
        'sub_header' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\FlashNewsWebsiteContent::class, static function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence,
        'website' => $faker->sentence,
        'href' => $faker->sentence,
        'author' => $faker->sentence,
        'date' => $faker->sentence,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\FlashNewsWebsiteContent::class, static function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence,
        'source' => $faker->sentence,
        'href' => $faker->sentence,
        'author' => $faker->sentence,
        'publish_date' => $faker->sentence,
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\LbLevel::class, static function (Faker\Generator $faker) {
    return [
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'level' => ['en' => $faker->sentence],
        'content' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\FlashNews::class, static function (Faker\Generator $faker) {
    return [
        'active' => $faker->boolean(),
        'author' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'href' => $faker->sentence,
        'order_index' => $faker->randomNumber(5),
        'publish_date' => $faker->sentence,
        'source' => $faker->sentence,
        'updated_at' => $faker->dateTime,

        'description' => ['en' => $faker->sentence],
        'title' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\FlashNews::class, static function (Faker\Generator $faker) {
    return [
        'active' => $faker->boolean(),
        'author' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'description' => $faker->text(),
        'href' => $faker->sentence,
        'order_index' => $faker->randomNumber(5),
        'publish_date' => $faker->sentence,
        'source' => $faker->sentence,
        'updated_at' => $faker->dateTime,

        'title' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\FlashSimilarApp::class, static function (Faker\Generator $faker) {
    return [
        'active' => $faker->boolean(),
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'href' => $faker->sentence,
        'order_index' => $faker->randomNumber(5),
        'sub_title' => $faker->sentence,
        'title' => $faker->sentence,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Assessment::class, static function (Faker\Generator $faker) {
    return [
        'time_to_complete' => $faker->randomNumber(5),
        'cadre_id' => $faker->sentence,
        'country_id' => $faker->randomNumber(5),
        'state_id' => $faker->sentence,
        'assessment_type' => $faker->sentence,
        'from_date' => $faker->sentence,
        'to_date' => $faker->sentence,
        'initial_invitation' => $faker->boolean(),
        'activated' => $faker->boolean(),
        'district_id' => $faker->text(),
        'cadre_type' => $faker->sentence,
        'created_by' => $faker->randomNumber(5),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'assessment_title' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\AssessmentEnrollment::class, static function (Faker\Generator $faker) {
    return [
        'assessment_id' => $faker->randomNumber(5),
        'user_id' => $faker->randomNumber(5),
        'response' => $faker->sentence,
        'send_inital_invitation' => $faker->boolean(),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\DiagnosesAlgorithm::class, static function (Faker\Generator $faker) {
    return [
        'activated' => $faker->boolean(),
        'cadre_id' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'has_options' => $faker->boolean(),
        'index' => $faker->randomNumber(5),
        'is_expandable' => $faker->boolean(),
        'master_node_id' => $faker->sentence,
        'node_type' => $faker->sentence,
        'parent_id' => $faker->sentence,
        'redirect_algo_type' => $faker->sentence,
        'redirect_node_id' => $faker->sentence,
        'state_id' => $faker->sentence,
        'time_spent' => $faker->sentence,
        'updated_at' => $faker->dateTime,

        'description' => ['en' => $faker->sentence],
        'header' => ['en' => $faker->sentence],
        'sub_header' => ['en' => $faker->sentence],
        'title' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\GuidanceOnAdverseDrugReaction::class, static function (Faker\Generator $faker) {
    return [
        'activated' => $faker->boolean(),
        'cadre_id' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'has_options' => $faker->boolean(),
        'index' => $faker->randomNumber(5),
        'is_expandable' => $faker->boolean(),
        'master_node_id' => $faker->sentence,
        'node_type' => $faker->sentence,
        'parent_id' => $faker->sentence,
        'redirect_algo_type' => $faker->sentence,
        'redirect_node_id' => $faker->sentence,
        'state_id' => $faker->sentence,
        'time_spent' => $faker->sentence,
        'updated_at' => $faker->dateTime,

        'description' => ['en' => $faker->sentence],
        'header' => ['en' => $faker->sentence],
        'sub_header' => ['en' => $faker->sentence],
        'title' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\TreatmentAlgorithm::class, static function (Faker\Generator $faker) {
    return [
        'activated' => $faker->boolean(),
        'cadre_id' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'has_options' => $faker->boolean(),
        'index' => $faker->randomNumber(5),
        'is_expandable' => $faker->boolean(),
        'master_node_id' => $faker->sentence,
        'node_type' => $faker->sentence,
        'parent_id' => $faker->sentence,
        'redirect_algo_type' => $faker->sentence,
        'redirect_node_id' => $faker->sentence,
        'state_id' => $faker->sentence,
        'time_spent' => $faker->sentence,
        'updated_at' => $faker->dateTime,

        'description' => ['en' => $faker->sentence],
        'header' => ['en' => $faker->sentence],
        'sub_header' => ['en' => $faker->sentence],
        'title' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\CaseDefinition::class, static function (Faker\Generator $faker) {
    return [
        'activated' => $faker->boolean(),
        'cadre_id' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'has_options' => $faker->boolean(),
        'index' => $faker->randomNumber(5),
        'is_expandable' => $faker->boolean(),
        'master_node_id' => $faker->sentence,
        'node_type' => $faker->sentence,
        'parent_id' => $faker->sentence,
        'redirect_algo_type' => $faker->sentence,
        'redirect_node_id' => $faker->sentence,
        'state_id' => $faker->sentence,
        'time_spent' => $faker->sentence,
        'updated_at' => $faker->dateTime,

        'description' => ['en' => $faker->sentence],
        'header' => ['en' => $faker->sentence],
        'sub_header' => ['en' => $faker->sentence],
        'title' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\LatentTbInfection::class, static function (Faker\Generator $faker) {
    return [
        'activated' => $faker->boolean(),
        'cadre_id' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'has_options' => $faker->boolean(),
        'index' => $faker->randomNumber(5),
        'is_expandable' => $faker->boolean(),
        'master_node_id' => $faker->sentence,
        'node_type' => $faker->sentence,
        'parent_id' => $faker->sentence,
        'redirect_algo_type' => $faker->sentence,
        'redirect_node_id' => $faker->sentence,
        'state_id' => $faker->sentence,
        'time_spent' => $faker->sentence,
        'updated_at' => $faker->dateTime,

        'description' => ['en' => $faker->sentence],
        'header' => ['en' => $faker->sentence],
        'sub_header' => ['en' => $faker->sentence],
        'title' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\CgcInterventionsAlgorithm::class, static function (Faker\Generator $faker) {
    return [
        'activated' => $faker->boolean(),
        'cadre_id' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'has_options' => $faker->boolean(),
        'index' => $faker->randomNumber(5),
        'is_expandable' => $faker->boolean(),
        'master_node_id' => $faker->sentence,
        'node_type' => $faker->sentence,
        'parent_id' => $faker->sentence,
        'redirect_algo_type' => $faker->sentence,
        'redirect_node_id' => $faker->sentence,
        'state_id' => $faker->sentence,
        'time_spent' => $faker->sentence,
        'updated_at' => $faker->dateTime,

        'description' => ['en' => $faker->sentence],
        'header' => ['en' => $faker->sentence],
        'sub_header' => ['en' => $faker->sentence],
        'title' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\DifferentialCareAlgorithm::class, static function (Faker\Generator $faker) {
    return [
        'activated' => $faker->boolean(),
        'cadre_id' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'has_options' => $faker->boolean(),
        'index' => $faker->randomNumber(5),
        'is_expandable' => $faker->boolean(),
        'master_node_id' => $faker->sentence,
        'node_type' => $faker->sentence,
        'parent_id' => $faker->sentence,
        'redirect_algo_type' => $faker->sentence,
        'redirect_node_id' => $faker->sentence,
        'state_id' => $faker->sentence,
        'time_spent' => $faker->sentence,
        'updated_at' => $faker->dateTime,

        'description' => ['en' => $faker->sentence],
        'header' => ['en' => $faker->sentence],
        'sub_header' => ['en' => $faker->sentence],
        'title' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\DynamicAlgorithm::class, static function (Faker\Generator $faker) {
    return [
        'activated' => $faker->boolean(),
        'algo_key' => $faker->sentence,
        'cadre_id' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'has_options' => $faker->boolean(),
        'index' => $faker->randomNumber(5),
        'is_expandable' => $faker->boolean(),
        'master_node_id' => $faker->sentence,
        'node_type' => $faker->sentence,
        'parent_id' => $faker->sentence,
        'redirect_algo_type' => $faker->sentence,
        'redirect_node_id' => $faker->sentence,
        'state_id' => $faker->sentence,
        'updated_at' => $faker->dateTime,

        'description' => ['en' => $faker->sentence],
        'header' => ['en' => $faker->sentence],
        'sub_header' => ['en' => $faker->sentence],
        'title' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\SurveyMaster::class, static function (Faker\Generator $faker) {
    return [
        'active' => $faker->boolean(),
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'order_index' => $faker->randomNumber(5),
        'title' => $faker->sentence,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\SurveyMasterQuestion::class, static function (Faker\Generator $faker) {
    return [
        'active' => $faker->boolean(),
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'option1' => $faker->sentence,
        'option2' => $faker->sentence,
        'option3' => $faker->sentence,
        'option4' => $faker->sentence,
        'order_index' => $faker->randomNumber(5),
        'question' => $faker->sentence,
        'survey_master_id' => $faker->randomNumber(5),
        'type' => $faker->sentence,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\SurveyMasterHistory::class, static function (Faker\Generator $faker) {
    return [
        'answer' => $faker->sentence,
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'survey_id' => $faker->randomNumber(5),
        'survey_question_id' => $faker->randomNumber(5),
        'updated_at' => $faker->dateTime,
        'user_id' => $faker->randomNumber(5),


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\SurveyMaster::class, static function (Faker\Generator $faker) {
    return [
        'active' => $faker->boolean(),
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'order_index' => $faker->randomNumber(5),
        'updated_at' => $faker->dateTime,

        'title' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\SurveyMasterQuestion::class, static function (Faker\Generator $faker) {
    return [
        'active' => $faker->boolean(),
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'order_index' => $faker->randomNumber(5),
        'survey_master_id' => $faker->randomNumber(5),
        'type' => $faker->sentence,
        'updated_at' => $faker->dateTime,

        'option1' => ['en' => $faker->sentence],
        'option2' => ['en' => $faker->sentence],
        'option3' => ['en' => $faker->sentence],
        'option4' => ['en' => $faker->sentence],
        'question' => ['en' => $faker->sentence],

    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\SurveyMaster::class, static function (Faker\Generator $faker) {
    return [
        'country_id' => $faker->sentence,
        'cadre_id' => $faker->sentence,
        'state_id' => $faker->sentence,
        'district_id' => $faker->sentence,
        'cadre_type' => $faker->sentence,
        'order_index' => $faker->randomNumber(5),
        'active' => $faker->boolean(),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,

        'title' => ['en' => $faker->sentence],

    ];
});

/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\LbSubscriberRanking::class, static function (Faker\Generator $faker) {
    return [
        'subscriber_id' => $faker->randomNumber(5),
        'level_id' => $faker->randomNumber(5),
        'badge_id' => $faker->randomNumber(5),
        'mins_spent_count' => $faker->sentence,
        'sub_module_usage_count' => $faker->sentence,
        'App_opended_count' => $faker->randomNumber(5),
        'chatbot_usage_count' => $faker->randomNumber(5),
        'resource_material_accessed_count' => $faker->randomNumber(5),
        'total_task_count' => $faker->randomNumber(5),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\LbSubscriberRankingHistory::class, static function (Faker\Generator $faker) {
    return [
        'App_opended_count' => $faker->randomNumber(5),
        'badge_id' => $faker->randomNumber(5),
        'chatbot_usage_count' => $faker->randomNumber(5),
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'lb_subscriber_rankings_id' => $faker->randomNumber(5),
        'level_id' => $faker->randomNumber(5),
        'mins_spent_count' => $faker->sentence,
        'resource_material_accessed_count' => $faker->randomNumber(5),
        'sub_module_usage_count' => $faker->sentence,
        'subscriber_id' => $faker->randomNumber(5),
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\LbSubscriberRanking::class, static function (Faker\Generator $faker) {
    return [
        'App_opended_count' => $faker->randomNumber(5),
        'badge_id' => $faker->randomNumber(5),
        'chatbot_usage_count' => $faker->randomNumber(5),
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'level_id' => $faker->randomNumber(5),
        'mins_spent_count' => $faker->sentence,
        'resource_material_accessed_count' => $faker->randomNumber(5),
        'sub_module_usage_count' => $faker->sentence,
        'subscriber_id' => $faker->randomNumber(5),
        'total_task_count' => $faker->randomNumber(5),
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\UserFeedbackDetail::class, static function (Faker\Generator $faker) {
    return [
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'feedback_id' => $faker->randomNumber(5),
        'ratings' => $faker->randomNumber(5),
        'review' => $faker->text(),
        'subscriber_id' => $faker->randomNumber(5),
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\UserFeedbackHistory::class, static function (Faker\Generator $faker) {
    return [
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'feedback_id' => $faker->randomNumber(5),
        'ratings' => $faker->randomNumber(5),
        'review' => $faker->text(),
        'skip' => $faker->boolean(),
        'subscriber_id' => $faker->randomNumber(5),
        'updated_at' => $faker->dateTime,


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\UserNotification::class, static function (Faker\Generator $faker) {
    return [
        'automatic_notification_type' => $faker->sentence,
        'cadre_id' => $faker->sentence,
        'cadre_type' => $faker->sentence,
        'country_id' => $faker->randomNumber(5),
        'created_at' => $faker->dateTime,
        'deleted_at' => null,
        'description' => $faker->text(),
        'district_id' => $faker->text(),
        'is_deeplinking' => $faker->boolean(),
        'state_id' => $faker->sentence,
        'title' => $faker->sentence,
        'type' => $faker->sentence,
        'type_title' => $faker->sentence,
        'updated_at' => $faker->dateTime,
        'user_id' => $faker->text(),


    ];
});
/** @var  \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\AssessmentCertificate::class, static function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence,
        'top' => $faker->randomNumber(5),
        'left' => $faker->randomNumber(5),
        'deleted_at' => null,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,


    ];
});