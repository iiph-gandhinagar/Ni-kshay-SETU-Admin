<?php

return [
    'appConfig' => [
        'DMC' => 'DMC',
        'TRUNAT' => 'TRUNAT',
        'CBNAAT' => 'CBNAAT',
        'X_RAY' => 'X Ray',
        'ICTC' => 'ICTC',
        'LPA_Lab' => 'LPA Lab',
        'CONFIRMATION_CENTER' => 'Confirmation Center',
        'Tobacco_Cessation_clinic' => 'Tobacco Cessation clinic',
        'ANC_Clinic' => 'ANC Clinic',
        'Nutritional_Rehabilitation_centre' => 'Nutritional Rehabilitation centre',
        'De_addiction_centres' => 'De Addiction Centres',
        'ART_Centre' => 'ART Centre',
        'District_DRTB_Centre' => 'District DRTB Centre',
        'NODAL_DRTB_CENTER' => 'Nodal DRTB Center',
        'IRL' => 'IRL',
        'Pediatric_Care_Facility' => 'Pediatric Care Facility',
    ],

    'userActivities' => [
        // 'api/store-user-activity' => 'User Activity Fetched',
        'api/store-user-screening' => 'User Screening Fetched',
        'api/get-case-definitions-master-nodes' => 'Case Definition Fetched',
        'api/get-case-definitions-master-nodes-v2' => 'Case Definition Fetched',
        'api/get-diagnoses-algorithms-master-nodes' => 'Diagnoses Algortihms Fetched',
        'api/get-diagnoses-algorithms-master-nodes-v2' => 'Diagnoses Algortihms Fetched',
        'api/get-treatment-algorithms-master-nodes' => 'Treatment Algorithms Fetched',
        'api/get-treatment-algorithms-master-nodes-v2' => 'Treatment Algorithms Fetched',
        'api/get-guidance-on-adverse-drug-reactions-master-nodes' => 'Guidance on ADR Fetched',
        'api/get-guidance-on-adverse-drug-reactions-master-nodes-v2' => 'Guidance on ADR Fetched',
        'api/get-latent-tb-infection-master-nodes' => 'Latent Tb Infection Fetched',
        'api/get-latent-tb-infection-master-nodes-v2' => 'Latent Tb Infection Fetched',
        'api/get-differential-care-algorithms-master-nodes' => 'Differentiated Care Of TB Patients',
        'api/get-differential-care-algorithms-master-nodes-v2' => 'Differentiated Care Of TB Patients',
        'api/get-material/{type}' => 'Resource Material Fetched',
        'api/get-all-assessment' => 'User Assessment Submitted Fetched',
        'api/get-all-past-assessment' => 'User Past Assessment Submitted Fetched',
        'api/get-all-chapters' => 'CGC Intervention Fetched',
        'api/store-user-enquiry' => 'Contact Us Submitted Fetched',
        'api/get-health-facilities' => 'Referral Health Facility Fetched',
        'api/get-keywords' => 'Chat Keyword Fetched',
        'api/get-questions-by-keyword/{keyword}' => 'Chat Questions By Keyword Fetched',
        'api/get-questions-by-keyword-v2/{keyword}' => 'Chat Questions By Keyword Fetched',
        'api/get-questions-by-keyword-v3/{keyword}' => 'Chat Questions By Keyword Fetched',
        'api/search-by-keyword/{keyword}' => 'Search By Keyword Fetched',
        'api/search-by-keyword-v2' => 'Search By Keyword Fetched',
        'api/search-by-keyword-v2/{keyword}' => 'Search By Keyword Fetched',
        'api/get-user-v3' => 'User Detail Fetched',
        'api/update-user-details-v2' => 'Update User Profile',
        'api/get-all-future-assessment' => 'Fetch All Future Assessment',
        'api/get-assessment-performace' => 'Fetch Assessment performace',
        'api/store-assessment-enrollnment' => 'Assessment Enrollnment',
        'api/get-leaderboard-details' => 'Leader Board User Activity Fetched',
        'api/get-leaderboard-task-list' => 'Leader Board Task Fetched',
        'api/get-leaderboard-achivements' => 'Leader Board Achivements Fetched',
        'api/store-feedback-details' => 'Store Feedback Details',
        'api/get-module-master-search' => 'Module Master Search',
        'api/get-sub-module-master-search' => 'Sub Module Master Search',
        'api/get-resource-material-master-search' => 'Resource Material Master Search',
        'api/get-chat-question-master-search' => 'Chat Question Master Search',
        'api/get-all-certificates' => 'Certificates Fetched',
        'api/get-certificate-pdf/{assessment_id}' => 'Download Certificate',
        'api/get-survey-forms' => 'Survey Forms Fetched',
        'api/store-survey-details' => 'Store Survey Details',
        
    ],
    'general' => [
        'app_title' => 'IIPHG - Nikshay SETU',
        'home_title' => 'Home'
    ],
    'admin-user' => [
        'title' => 'Users',

        'actions' => [
            'index' => 'Users',
            'create' => 'New User',
            'edit' => 'Edit :name',
            'edit_profile' => 'Edit Profile',
            'edit_password' => 'Edit Password',
        ],

        'columns' => [
            'id' => 'ID',
            'last_login_at' => 'Last login',
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'email' => 'Email',
            'password' => 'Password',
            'password_repeat' => 'Password Confirmation',
            'activated' => 'Activated',
            'forbidden' => 'Forbidden',
            'language' => 'Language',
            'state' => 'State',
            'role_type' => 'Data View',
            'country' => 'Country',
            'district' => 'District',
            'cadre_type' => 'Cadre Type',
            'cadre' => 'Cadre',

            //Belongs to many relations
            'roles' => 'Admin Rights',
        ],
    ],

    'cadre' => [
        'title' => 'Cadre',

        'actions' => [
            'index' => 'Cadre',
            'create' => 'New Cadre',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'title' => 'Title',
            'cadre_type' => 'Cadre Type',  
        ],
    ],

    'state' => [
        'title' => 'State',

        'actions' => [
            'index' => 'State',
            'create' => 'New State',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'title' => 'Title',
            'country_id' => 'Country',
        ],
    ],

    'district' => [
        'title' => 'Districts',

        'actions' => [
            'index' => 'Districts',
            'create' => 'New District',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'state_id' => 'State',
            'title' => 'Title',
            'country_id' => 'Country',
        ],
    ],

    'block' => [
        'title' => 'Blocks',

        'actions' => [
            'index' => 'Blocks',
            'create' => 'New Block',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'state_id' => 'State',
            'district_id' => 'District',
            'title' => 'Title',
            'country_id' => 'Country',
        ],
    ],

    'assessment' => [
        'title' => 'Assessments',

        'actions' => [
            'index' => 'Assessments',
            'create' => 'New Assessment',
            'edit' => 'Edit :name',
            'copy' => 'Copy Assessment',
            'assessment_report' => 'Assessment Report',
        ],

        'columns' => [
            'id' => 'ID',
            'assessment_title' => 'Assessment title',
            'time_to_complete' => 'Time to complete(In Minutes)',
            'cadre_id' => 'Cadre',
            'country_id' => 'Country',
            'state_id' => 'State',
            'assessment_type' => 'Assessment type',
            'from_date' => 'From date',
            'to_date' => 'To date',
            'initial_invitation' => 'Initial invitation',
            'activated' => 'Activated',
            'district_id' => 'District',
            'cadre_type' => 'Cadre type',
            'created_by' => 'Created by',
            // 'assessment_json' => 'Assessment json',
            'certificate_type' => 'Certificate Format',
        ],
        
    ],

    'assessment-question' => [
        'title' => 'Assessment Questions',
        'short-title' => 'Assessment Quiz',
        
        'actions' => [
            'index' => 'Assessment Questions',
            'create' => 'New Assessment Question',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'assessment_id' => 'Assessment',
            'question' => 'Question',
            'option1' => 'Option1',
            'option2' => 'Option2',
            'option3' => 'Option3',
            'option4' => 'Option4',
            'correct_answer' => 'Correct answer',
            'order_index' => 'Order index',
            'category' => 'Category',
            // 'question_value_json' => 'Question value json',
            // 'option1_value_json' => 'Option1 value json',
            // 'option2_value_json' => 'Option2 value json',
            // 'option3_value_json' => 'Option3 value json',
            // 'option4_value_json' => 'Option4 value json',
            
        ],
    ],

    'user-assessment' => [
        'title' => 'User Assessments',

        'actions' => [
            'index' => 'User Assessments',
            'create' => 'New User Assessment',
            'edit' => 'Edit :name',
            'export' => 'Export',
        ],

        'columns' => [
            'id' => 'ID',
            'assessment_id' => 'Assessment',
            'user_id' => 'User',
            'total_marks' => 'Total marks',
            'obtained_marks' => 'Obtained marks',
            'attempted' => 'Attempted',
            'right_answers' => 'Right answers',
            'wrong_answers' => 'Wrong answers',
            'skipped' => 'Skipped',
            'state' => 'State',
            'district' => 'District',
            'block' => 'Block',
            'health_facility' => 'Health Facility',
            'assesment_submit_date' => 'Assesment Submit Date',
            'cadre' => 'Cadre',
            
            
        ],
    ],

   'resource-material' => [
        'title' => 'Resource Materials',

        'actions' => [
            'index' => 'Resource Materials',
            'create' => 'New Resource Folder/Material',
            'edit' => 'Edit :name',
            'back' => 'Go back'
        ],

        'columns' => [
            'id' => 'ID',
            'title' => 'Title',
            'type_of_materials' => 'Type of materials', 
            'material' => 'Material',
            'video_thumb' => 'Video Image',
            'country_id' => 'Country',
            'state' => 'State',
            'cadre' => 'Cadre',
            'parent_id' => 'Parent',
            'icon_type' => 'Icon type',
            'index' => 'Index',
            'created_by' => 'Created by',
            
        ],
    ],

    'cgc-intervention' => [
        'title' => 'Cgc Interventions',

        'actions' => [
            'index' => 'Cgc Interventions',
            'create' => 'New Cgc Intervention',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'chapter_title' => 'Chapter title',
            'video_title' => 'Video title',
            'description' => 'Description',
            'chapter_video' => 'chapter Video',
            'reference_links' => 'Refrenece Media',
            'assessment_id' => 'Assessment',
            'reference_title' => 'Reference Media Title',
            'video_image' => 'Video Image',
        ],
    ],

    'symptom' => [
        'title' => 'Symptoms',

        'actions' => [
            'index' => 'Symptoms',
            'create' => 'New Symptom',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'category' => 'Category',
            'symptoms_title' => 'Symptoms title',
            'symptoms_image' => 'Image',
        ],
    ],

    'diagnoses-algorithm' => [
        'title' => 'Diagnosis Algorithms',

        'actions' => [
            'index' => 'Diagnosis Algorithms',
            'create' => 'New Diagnosis Algorithm',
            'edit' => 'Edit :name',
            'back' => 'Go back',
        ],

        'columns' => [
            'id' => 'ID',
            'title' => 'Title',
            'node_type' => 'Node type',
            'is_expandable' => 'Is expandable',
            'has_options' => 'Has Options',
            'parent_id' => 'Parent',
            'description' => 'Description',
            'index' => 'Index',
            'node_icon' => 'Node Icon',
            'time_spent' => 'Time spent',
            'redirect_algo_type' => 'Redirect Algorithm Type',
            'redirect_node_id' => 'Redirect Node',
            'activated' => 'Activated',
            'header' => 'Milestone Header',
            'sub_header' => 'Milestone Sub Header',
            'master_node_id' => 'Master Node Id',
            'state_id' => 'State Id',
            'cadre_id' => 'Cadre id',
        ],
    ],
    'health-facility' => [
        'title' => 'Health Facilities',

        'actions' => [
            'index' => 'Health Facilities',
            'create' => 'New Health Facility',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'state_id' => 'State',
            'district_id' => 'District',
            'block_id' => 'Block',
            'health_facility_code' => 'Health facility code',
            'DMC' => 'DMC',
            'TRUNAT' => 'TRUNAT',
            'CBNAAT' => 'CBNAAT',
            'X_RAY' => 'X RAY',
            'ICTC' => 'ICTC',
            'LPA_Lab' => 'CDST/LPA Lab',
            'CONFIRMATION_CENTER' => 'DM SCREENING/CONFIRMATION CENTER',
            'Tobacco_Cessation_clinic' => 'Tobacco Cessation clinic',
            'ANC_Clinic' => 'ANC Clinic',
            'Nutritional_Rehabilitation_centre' => 'Nutritional Rehabilitation centre',
            'De_addiction_centres' => 'De addiction centres',
            'ART_Centre' => 'ART Centre',
            'District_DRTB_Centre' => 'District DRTB Centre',
            'NODAL_DRTB_CENTER' => 'NODAL DRTB CENTER',
            'IRL' => 'IRL',
            'Pediatric_Care_Facility' => 'Pediatric Care Facility',
            'longitude' => 'Longitude',
            'latitude' => 'Latitude',
            'country_id' => 'Country',
        ],
    ],

    'treatment-algorithm' => [
        'title' => 'Treatment Algorithms',

        'actions' => [
            'index' => 'Treatment Algorithms',
            'create' => 'New Treatment Algorithm',
            'edit' => 'Edit :name',
            'back' => 'Go back',
        ],

        'columns' => [
            'id' => 'ID',
            'title' => 'Title',
            'node_type' => 'Node type',
            'is_expandable' => 'Is expandable',
            'has_options' => 'Has options',
            'parent_id' => 'Parent',
            'description' => 'Description',
            'index' => 'Index',
            'node_icon' => 'Node Icon',
            'redirect_algo_type' => 'Redirect Algorithm Type',
            'redirect_node_id' => 'Redirect Node',
            'time_spent' => 'Time spent',
            'activated' => 'Activated',
            'header' => 'Milestone Header',
            'sub_header' => 'Milestone Sub header',
            'master_node_id' => 'Master Node Id',
            'state_id' => 'State Id',
            'cadre_id' => 'Cadre id',
        ],
    ],

    'guidance-on-adverse-drug-reaction' => [
        'title' => 'Guidance On Adverse Drug Reactions',
        'nav-title' => 'Guidance On ADR',

        'actions' => [
            'index' => 'Guidance On Adverse Drug Reactions',
            'create' => 'New Guidance On Adverse Drug Reaction',
            'edit' => 'Edit :name',
            'back' => 'Go back',
        ],

        'columns' => [
            'id' => 'ID',
            'title' => 'Title',
            'node_type' => 'Node type',
            'is_expandable' => 'Is expandable',
            'has_options' => 'Has options',
            'parent_id' => 'Parent',
            'description' => 'Description',
            'index' => 'Index',
            'node_icon' => 'Node Icon',
            'time_spent' => 'Time spent',
            'redirect_algo_type' => 'Redirect Algorithm Type',
            'redirect_node_id' => 'Redirect Node',
            'header' => 'Milestone Header',
            'sub_header' => 'Milestone Sub header',
            'activated' => 'Activated',
            'master_node_id' => 'Master Node Id',
            'state_id' => 'State Id',
            'cadre_id' => 'Cadre id',
        ],
    ],

    'case-definition' => [
        'title' => 'Case Definitions',

        'actions' => [
            'index' => 'Case Definitions',
            'create' => 'New Case Definition',
            'edit' => 'Edit :name',
            'back' => 'Go back',
        ],

        'columns' => [
            'id' => 'ID',
            'title' => 'Title',
            'node_type' => 'Node type',
            'is_expandable' => 'Is expandable',
            'has_options' => 'Has options',
            'parent_id' => 'Parent',
            'description' => 'Description',
            'index' => 'Index',
            'node_icon' => 'Node Icon',
            'time_spent' => 'Time spent',
            'redirect_algo_type' => 'Redirect Algorithm Type',
            'redirect_node_id' => 'Redirect Node',
            'activated' => 'Activated',
            'header' => 'Milestone Header',
            'sub_header' => 'Milestone Sub Header',
            'master_node_id' => 'Master Node Id',
            'state_id' => 'State Id',
            'cadre_id' => 'Cadre id',
        ],
    ],

    'latent-tb-infection' => [
        'title' => 'PMTPT',

        'actions' => [
            'index' => 'Latent TB Infections',
            'create' => 'New Latent TB Infection',
            'edit' => 'Edit :name',
            'back' => 'Go back',
        ],

        'columns' => [
            'id' => 'ID',
            'title' => 'Title',
            'node_type' => 'Node type',
            'is_expandable' => 'Is expandable',
            'has_options' => 'Has options',
            'parent_id' => 'Parent',
            'description' => 'Description',
            'index' => 'Index',
            'node_icon' => 'Node Icon',
            'time_spent' => 'Time spent',
            'redirect_algo_type' => 'Redirect Algorithm Type',
            'redirect_node_id' => 'Redirect Node',
            'header' => 'Milestone Header',
            'sub_header' => 'Milestone Sub header',
            'activated' => 'Activated',
            'master_node_id' => 'Master Node Id',
            'state_id' => 'State Id',
            'cadre_id' => 'Cadre id',
        ],
    ],


    'enquiry' => [
        'title' => 'Enquiries',

        'actions' => [
            'index' => 'Enquiries',
            'create' => 'New Enquiry',
            'edit' => 'Edit :name',
            'export' => 'Export',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'subject' => 'Subject',
            'message' => 'Message',
            'ticket_id' => 'Ticket_id',
            'priority' => 'Priority',
            'status' => 'Status',
            'created_at' => 'Created At',
            
        ],
    ],

    'chat-keyword' => [
        'title' => 'Chat Keywords',

        'actions' => [
            'index' => 'Chat Keywords',
            'create' => 'New Chat Keyword',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'title' => 'Title',
            'hit' => 'Hit',
            'modules' => 'Modules',
            'sub_modules' => 'Sub modules',
            'resource_material' => 'Resource material',
            'custom_ordering' => 'Custom Ordering',
            
        ],
    ],

    'chat-question' => [
        'title' => 'Chat Questions',

        'actions' => [
            'index' => 'Chat Questions',
            'create' => 'New Chat Question',
            'edit' => 'Edit :name',
            'export' => 'Export',
        ],

        'columns' => [
            'id' => 'ID',
            'question' => 'Question',
            'answer' => 'Answer',
            'hit' => 'Hit',
            'keyword_id' => 'Keywords',
            'cadre_id' => 'Cadre',
            'category' => 'Category',
            'activated' => 'Activated',
            'like_count' => 'Like count',
            'dislike_count' => 'Dislike count',
            'created_at' => 'Created At',
        ],
    ],

    'chat-question-hit' => [
        'title' => 'Chat Question Hits',

        'actions' => [
            'index' => 'Chat Question Hits',
            'create' => 'New Chat Question Hit',
            'edit' => 'Edit :name',
            'export' => 'Export',
        ],

        'columns' => [
            'id' => 'ID',
            'question_id' => 'Question',
            'subscriber_id' => 'Subscriber',
            'session_token' => 'Session token',
            
        ],
    ],

    'subscriber' => [
        'title' => 'Subscribers',

        'actions' => [
            'index' => 'Subscribers',
            'create' => 'New Subscriber',
            'edit' => 'Edit :name',
            'export' => 'Export',
        ],

        'columns' => [
            'id' => 'ID',
            'api_token' => 'Api token',
            'name' => 'Name',
            'phone_no' => 'Phone no',
            'password' => 'Password',
            'cadre_type' => 'Cadre type',
            'is_verified' => 'Is verified',
            'cadre_id' => 'Cadre',
            'country_id' => 'Country',
            'block_id' => 'Block',
            'district_id' => 'District',
            'state_id' => 'State',
            'health_facility_id' => 'Health facility',
            
        ],
    ],

    'app-config' => [
        'title' => 'App Config',

        'actions' => [
            'index' => 'App Config',
            'create' => 'New App Config',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'key' => 'Key',
            'value_json' => 'Value json',
            
        ],
    ],

    'chat-keyword-hit' => [

        'title' => 'Chat Keyword Hits',

    'actions' => [
        'index' => 'Chat Keyword Hits',
        'create' => 'New Chat Keyword Hits',
        'edit' => 'Edit :name',
        'export' => 'Export',
    ],

    'columns' => [
        'id' => 'ID',
        'keyword_id' => 'Keyword',
        'subscriber_id' => 'Subscriber',
        
    ],
],

    'subscriber-activity' => [
        'title' => 'Subscriber Activities',

        'actions' => [
            'index' => 'Subscriber Activities',
            'create' => 'New Subscriber Activity',
            'edit' => 'Edit :name',
            'export' => 'Export',
        ],

        'columns' => [
            'id' => 'ID',
            'user_id' => 'User',
            'action' => 'Action',
            'ip_address' => 'Ip address',
            'plateform' => 'PlateForm',
            
        ],
    ],

    'chat-keyword-hit' => [
        'title' => 'Chat Keyword Hits',

        'actions' => [
            'index' => 'Chat Keyword Hits',
            'create' => 'New Chat Keyword Hit',
            'edit' => 'Edit :name',
            'export' => 'Export',
        ],

        'columns' => [
            'id' => 'ID',
            'keyword_id' => 'Keyword',
            'subscriber_id' => 'Subscriber',
            
        ],
    ],

    'master-cm' => [
        'title' => 'Master Cms',

        'actions' => [
            'index' => 'Master Cms',
            'create' => 'New Master Cm',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            
        ],
    ],


    'cgc-interventions-algorithm' => [
        'title' => 'NTEP Interventions',

        'actions' => [
            'index' => 'NTEP Interventions Algorithms',
            'create' => 'New NTEP Interventions Algorithm',
            'edit' => 'Edit :name',
            'back' => 'Go back',
        ],

        'columns' => [
            'id' => 'ID',
            'title' => 'Title',
            'node_type' => 'Node type',
            'is_expandable' => 'Is expandable',
            'has_options' => 'Has options',
            'parent_id' => 'Parent',
            'description' => 'Description',
            'time_spent' => 'Time spent',
            'index' => 'Index',
            'node_icon' => 'Node Icon',
            'redirect_algo_type' => 'Redirect Algorithm Type',
            'redirect_node_id' => 'Redirect node',
            'activated' => 'Activated',
            'header' => 'Milestone Header',
            'sub_header' => 'Milestone Sub Header',
            'master_node_id' => 'Master Node Id',
            'state_id' => 'State Id',
            'cadre_id' => 'Cadre id',
        ],
    ],

    'differential-care-algorithm' => [
        'title' => 'Differential Care Algo.',

        'actions' => [
            'index' => 'Differential Care Algorithms',
            'create' => 'New Differential Care Algorithm',
            'edit' => 'Edit :name',
            'back' => 'Go back',
        ],

        'columns' => [
            'id' => 'ID',
            'title' => 'Title',
            'node_type' => 'Node type',
            'is_expandable' => 'Is expandable',
            'has_options' => 'Has options',
            'parent_id' => 'Parent',
            'description' => 'Description',
            'time_spent' => 'Time spent',
            'index' => 'Index',
            'node_icon' => 'Node Icon',
            'redirect_algo_type' => 'Redirect Algorithm Type',
            'redirect_node_id' => 'Redirect node',
            'activated' => 'Activated',
            'header' => 'Milestone Header',
            'sub_header' => 'Milestone Sub Header',
            'master_node_id' => 'Master Node Id',
            'state_id' => 'State Id',
            'cadre_id' => 'Cadre id',
        ],
    ],

    'patient-assessment' => [
        'title' => 'Patient Assessments',

        'actions' => [
            'index' => 'Patient Assessments',
            'create' => 'New Patient Assessment',
            'edit' => 'Edit :name',
            'export' => 'Export',
        ],

        'columns' => [
            'id' => 'ID',
            'nikshay_id' => 'Nikshay',
            'patient_name' => 'Patient name',
            'age' => 'Age',
            'gender' => 'Gender',
            'patient_selected_data' => 'Patient selected data',
            'PULSE_RATE' => 'PULSE RATE',
            'TEMPERATURE' => 'TEMPERATURE',
            'BLOOD_PRESSURE' => 'BLOOD PRESSURE',
            'RESPIRATORY_RATE' => 'RESPIRATORY RATE',
            'OXYGEN_SATURATION' => 'OXYGEN SATURATION',
            'TEXT_BMI' => 'TEXT BMI',
            'TEXT_MUAC' => 'TEXT MUAC',
            'PEDAL_OEDEMA' => 'PEDAL OEDEMA',
            'GENERAL_CONDITION' => 'GENERAL CONDITION',
            'TEXT_ICTERUS' => 'TEXT ICTERUS',
            'TEXT_HEMOGLOBIN' => 'TEXT HEMOGLOBIN',
            'COUNT_WBC' => 'COUNT WBC',
            'TEXT_RBS' => 'TEXT RBS',
            'TEXT_HIV' => 'TEXT HIV',
            'TEXT_XRAY' => 'TEXT XRAY',
            'TEXT_HEMOPTYSIS' => 'TEXT HEMOPTYSIS',
            
        ],
    ],

    'module-mapping-to-name' => [
        'title' => 'Modules Mapping',

        'actions' => [
            'index' => 'Module Mapping To Names',
            'create' => 'New Module Mapping To Name',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'module_name' => 'Module name',
            'mapping_name' => 'Mapping name',
            
        ],
    ],

    'user-notification' => [
        'title' => 'User Notifications',

        'actions' => [
            'index' => 'User Notifications',
            'create' => 'New User Notification',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'type' => 'Type',
            'user_id' => 'User',
            'country_id' => 'Country',
            'state_id' => 'State',
            'district_id' => 'District',
            'cadre_type' => 'Cadre type',
            'cadre_id' => 'Cadre',
            'is_deeplinking' => 'Is deeplinking',
            'automatic_notification_type' => 'Automatic notification type',
            'type_title' => 'Type title',
            'created_by' => 'Created By',
        ],
    ],
    'message-notification' => [
        'title' => 'Message Notifications',

        'actions' => [
            'index' => 'Message Notifications',
            'create' => 'New Message Notification',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'user_name' => 'User name',
            'phone_no' => 'Phone no',
            'notification_message' => 'Notification message',
            'message' => 'Message',
        ],
    ],

    'chatbot-activity' => [
        'title' => 'Chatbot Activity',

        'actions' => [
            'index' => 'Chatbot Activity',
            'create' => 'New Chatbot Activity',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'user_id' => 'User',
            'action' => 'Action',
            'payload' => 'Payload',
            'plateform' => 'Plateform',
            'ip_address' => 'Ip address',
            'tag_id' => 'Tag',
            'question_id' => 'Question',
            'like' => 'Like',
            'dislike' => 'Dislike',
            'response' => 'Response',
            
        ],
    ],

    'app-management-flag' => [
        'title' => 'App Management Flags',
        'short-title' => 'App Management',

        'actions' => [
            'index' => 'App Management Flags',
            'create' => 'New App Management Flag',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'variable' => 'Variable',
            'value' => 'Value',
            'type' => 'Type',
            
        ],
    ],

    'dynamic-algo-master' => [
        'title' => 'Dynamic Algorithm Master',
        'short-title' => 'Dynamic Algorithm',

        'actions' => [
            'index' => 'Dynamic Algorithm Master',
            'create' => 'New Dynamic Algorithm Master',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'section' => 'Section',
            'node_icon' => 'Node Icon',
            'active' => 'Active',
            
        ],
    ],

    'dynamic-algorithm' => [
        'title' => 'Dynamic Algorithm',

        'actions' => [
            'index' => 'Dynamic Algorithm',
            'create' => 'New Dynamic Algorithm',
            'edit' => 'Edit :name',
            'back' => 'Go back',
        ],

        'columns' => [
            'id' => 'ID',
            'algo_key' => 'Algorithm Master Key',
            'title' => 'Title',
            'node_type' => 'Node type',
            'is_expandable' => 'Is expandable',
            'has_options' => 'Has options',
            'parent_id' => 'Parent',
            'index' => 'Index',
            'node_icon' => 'Node Icon',
            'description' => 'Description',
            'redirect_algo_type' => 'Redirect algo type',
            'redirect_node_id' => 'Redirect node',
            'header' => 'Milestone Header',
            'sub_header' => 'Milestone Sub Header',
            'activated' => 'Activated',
            'master_node_id' => 'Master Node Id',
            'state_id' => 'State Id',
            'cadre_id' => 'Cadre id',
        ],
    ],
    
    't-module-master' => [
        'title' => 'Module Master',

        'actions' => [
            'index' => 'Module Master',
            'create' => 'New Module Master',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
        ],
    ],
    
    't-sub-module-master' => [
        'title' => 'Sub Module Master',

        'actions' => [
            'index' => 'Sub Module Master',
            'create' => 'New Sub Module Master',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'module_id' => 'Module',
            'existing_module_ref' => 'Existing module ref',
            
        ],
    ],

    't-training-tag' => [
        'title' => 'Training Tag',

        'actions' => [
            'index' => 'Training Tag',
            'create' => 'New Training Tag',
            'edit' => 'Edit :name',
            'tag' => 'Tag',
        ],

        'columns' => [
            'id' => 'ID',
            'tag' => 'Tag',
            'pattern' => 'Pattern',
            'count' => 'Count',
            'is_fix_response' => 'Is Fix Response',
            'response' => 'Response',
            'like_count' => 'Like count',
            'dislike_count' => 'Dislike count',
            'questions' => 'Questions',
            'modules' => 'Modules',
            'sub_modules' => 'Sub modules',
            'resource_material' => 'Resource material',
            'created_at' => 'Created Date',
            'updated_at' => 'Updated Date',
           
            
        ],
    ],

    'role' => [
        'title' => 'Roles',

        'actions' => [
            'index' => 'Roles',
            'create' => 'New Role',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'guard_name' => 'Guard name',
            
        ],
    ],

    'role-has-permission' => [
        'title' => 'Role Has Permissions',

        'actions' => [
            'index' => 'Role Has Permissions',
            'create' => 'New Role Has Permission',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'permission_id' => 'Permission',
            'role_id' => 'Role',
            
        ],
    ],

    'permission' => [
        'title' => 'Permissions',

        'actions' => [
            'index' => 'Permissions',
            'create' => 'New Permission',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'guard_name' => 'Guard name',
            
        ],
    ],

    'country' => [
        'title' => 'Country',

        'actions' => [
            'index' => 'Country',
            'create' => 'New Country',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'title' => 'Title',
            
        ],
    ],

    'user-app-version' => [
        'title' => 'User App Version',

        'actions' => [
            'index' => 'User App Version',
            'create' => 'New User App Version',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'user_id' => 'User',
            'user_name' => 'User name',
            'app_version' => 'App version',
            'current_plateform' => 'Current Plateform',
            'has_ios' => 'I-phone',
            'has_web' => 'Web',
            'has_android' => 'Android',
            'created_at' => 'Created At',
        ],
    ],

     'activity-log' => [
        'title' => 'Activity Log',

        'actions' => [
            'index' => 'Activity Log',
            'create' => 'New Activity Log',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'log_name' => 'Log name',
            'description' => 'Description',
            'subject_type' => 'Subject type',
            'subject_id' => 'Subject',
            'causer_type' => 'Causer type',
            'causer_id' => 'Causer',
            'properties' => 'Properties',
            'created_at' => 'Created At',
        ],
    ],

    'tour' => [
        'title' => 'Tours',

        'actions' => [
            'index' => 'Tours',
            'create' => 'New Tour',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'title' => 'Title',
            'active' => 'Active',
            'default' => 'Default',
            'created_at' => 'Created At'
            
        ],
    ],

    'tour-slide' => [
        'title' => 'Tour Slides',

        'actions' => [
            'index' => 'Tour Slides',
            'create' => 'New Tour Slide',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'tour_id' => 'Tour',
            'title' => 'Title',
            'description' => 'Description',
            'tour_video' => 'Tour Video',
            'tour_image' => 'Tour Image (380 * 380)',
            'type' => 'Type',
            'created_at' => 'Created At'
        ],
    ],

    'static-blog' => [
        'title' => 'Blogs',

        'actions' => [
            'index' => 'Static Blogs',
            'create' => 'New Static Blog',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'active' => 'Active',
            'author' => 'Author',
            'description' => 'Description',
            'keywords' => 'Keywords',
            'order_index' => 'Order index',
            'short_description' => 'Short description',
            'slug' => 'Slug',
            'source' => 'Source',
            'title' => 'Title',
            'blog_thumb_image1' => 'Blog Image(1241x443)',
            'blog_thumb_image2' => 'Blog Image(591x261)',
            'blog_thumb_image3' => 'Blog Image(713x445)',
            'created_at' => 'Created At'
        ],
    ],

    'static-faq' => [
        'title' => 'Static Faq',

        'actions' => [
            'index' => 'Static Faq',
            'create' => 'New Static Faq',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'active' => 'Active',
            'description' => 'Description',
            'order_index' => 'Order index',
            'question' => 'Question',
            'created_at' => 'Created At'
        ],
    ],

    'static-app-config' => [
        'title' => 'App Config',

        'actions' => [
            'index' => 'Static App Config',
            'create' => 'New Static App Config',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'key' => 'Key',
            'value_json' => 'Value json',
            'type' => 'Type',
            'created_at' => 'Created At'
        ],
    ],

    'key-feature' => [
        'title' => 'Key Features',

        'actions' => [
            'index' => 'Key Features',
            'create' => 'New Key Feature',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'active' => 'Active',
            'description' => 'Description',
            'order_index' => 'Order index',
            'title' => 'Title',
            'icon' => 'Icon',
            'icon_bg' => 'Backgroun Icon',
            'created_at' => 'Created At'
        ],
    ],

    'static-testimonial' => [
        'title' => 'Testimonials',

        'actions' => [
            'index' => 'Static Testimonials',
            'create' => 'New Static Testimonial',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'active' => 'Active',
            'description' => 'Description',
            'name' => 'Name',
            'order_index' => 'Order index',
            'icon' => 'Icon',
            'created_at' => 'Created At'
        ],
    ],

    'static-release' => [
        'title' => 'Releases',

        'actions' => [
            'index' => 'Static Releases',
            'create' => 'New Static Release',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'active' => 'Active',
            'bugs_fix' => 'Bugs fix',
            'date' => 'Date',
            'features' => 'Features',
            'order_index' => 'Order index',
            'created_at' => 'Created At'
        ],
    ],

    'static-enquiry' => [
        'title' => 'Enquiries',

        'actions' => [
            'index' => 'Static Enquiries',
            'create' => 'New Static Enquiry',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'subject' => 'Subject',
            'email' => 'Email',
            'message' => 'Message',
            'created_at' => 'Created At'
        ],
    ],

    'static-what-we-do' => [
        'title' => 'What We Do',

        'actions' => [
            'index' => 'Static What We Do',
            'create' => 'New Static What We Do',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'active' => 'Active',
            'location' => 'Location',
            'order_index' => 'Order index',
            'title' => 'Title',
            'cover_image' => 'Cover Image',
            'created_at' => 'Created At'
        ],
    ],

    

    'static-module' => [
        'title' => 'Module',

        'actions' => [
            'index' => 'Static Module',
            'create' => 'New Static Module',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'active' => 'Active',
            'description' => 'Description',
            'order_index' => 'Order index',
            'title' => 'Title',
            'module_image' => 'image',
            'slug' => 'Slug',
            'created_at' => 'Created At'
        ],
    ],

    'static-resource-material' => [
        'title' => 'Resource Materials',

        'actions' => [
            'index' => 'Static Resource Materials',
            'create' => 'New Static Resource Material',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'active' => 'Active',
            'order_index' => 'Order index',
            'title' => 'Title',
            'type' => 'Type',
            'material' => 'Material',
            'type_of_materials' => 'Type of materials',
            'created_at' => 'Created At'
        ],
    ],

    'lb-level' => [
        'title' => 'Lb Levels',

        'actions' => [
            'index' => 'Lb Levels',
            'create' => 'New Lb Level',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'level' => 'Level',
            'content' => 'Content',
            'created_at' => 'Created At'
            
        ],
    ],

    'lb-badge' => [
        'title' => 'Lb Badges',

        'actions' => [
            'index' => 'Lb Badges',
            'create' => 'New Lb Badge',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'level_id' => 'Level',
            'badge' => 'Badge',
            'created_at' => 'Created At'
            
        ],
    ],

    'lb-task-list' => [
        'title' => 'Lb Task Lists',

        'actions' => [
            'index' => 'Lb Task Lists',
            'create' => 'New Lb Task List',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'level' => 'Level',
            'badges' => 'Badges',
            'mins_spent' => 'Mins spent',
            'sub_module_usage_count' => 'Sub module usage count',
            'App_opended_count' => 'App opended count',
            'chatbot_usage_count' => 'Chatbot usage count',
            'resource_material_accessed_count' => 'Resource material accessed count',
            'total_task' => 'Total task',
            'created_at' => 'Created At'
            
        ],
    ],

    'lb-subscriber-ranking' => [
        'title' => 'Lb Subscriber Rank',

        'actions' => [
            'index' => 'Lb Subscriber Rankings',
            'create' => 'New Lb Subscriber Ranking',
            'edit' => 'Edit :name',
            'export' => 'Export',
        ],

        'columns' => [
            'id' => 'ID',
            'subscriber_id' => 'Subscriber',
            'level_id' => 'Level',
            'badge_id' => 'Badge',
            'mins_spent_count' => 'Mins spent count',
            'sub_module_usage_count' => 'Sub module usage count',
            'App_opended_count' => 'App opended count',
            'chatbot_usage_count' => 'Chatbot usage count',
            'resource_material_accessed_count' => 'Resource material accessed count',
            'total_task_count' => 'Total task count',
            'created_at' => 'Created At'
        ],
    ],

    'lb-subscriber-ranking-history' => [
        'title' => 'Lb sub Rank History',

        'actions' => [
            'index' => 'Lb Subscriber Ranking History',
            'create' => 'New Lb Subscriber Ranking History',
            'edit' => 'Edit :name',
            'export' => 'Export',
        ],

        'columns' => [
            'id' => 'ID',
            'lb_subscriber_rankings_id' => 'Lb subscriber rankings',
            'subscriber_id' => 'Subscriber',
            'level_id' => 'Level',
            'badge_id' => 'Badge',
            'mins_spent_count' => 'Mins spent count',
            'sub_module_usage_count' => 'Sub module usage count',
            'App_opended_count' => 'App opended count',
            'chatbot_usage_count' => 'Chatbot usage count',
            'resource_material_accessed_count' => 'Resource material accessed count',
            'created_at' => 'Created At'
        ],
    ],

    'lb-sub-module-usage' => [
        'title' => 'Lb Sub-Module',

        'actions' => [
            'index' => 'Lb Sub Module Usages',
            'create' => 'New Lb Sub Module Usage',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'subscriber_id' => 'Subscriber',
            'module_id' => "Module",
            'sub_module' => 'Sub module',
            'total_time' => 'Total time',
            'mins_spent' => 'Mins spent',
            'completed_flag' => 'Completed flag',
            'created_at' => 'Created At'
        ],
    ],

    'user-feedback-question' => [
        'title' => 'Feedback Questions',

        'actions' => [
            'index' => 'User Feedback Questions',
            'create' => 'New User Feedback Question',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'feedback_question' => 'Feedback question',
            'feedback_description' => 'Feedback description',
            'feedback_value' => 'Feedback value',
            'feedback_time' => 'Feedback time',
            'feedback_type' => 'Feedback type',
            'feedback_days' => 'Feedback days',
            'is_active' => 'Is active',
            'feedback_question_icon' => 'Feedback Icon(35 *35)',
            'created_at' => 'Created At'
        ],
    ],

    'user-feedback-detail' => [
        'title' => 'Feedback Details',

        'actions' => [
            'index' => 'User Feedback Details',
            'create' => 'New User Feedback Detail',
            'edit' => 'Edit :name',
            'export' => 'Export',
        ],

        'columns' => [
            'id' => 'ID',
            'subscriber_id' => 'Subscriber',
            'feedback_id' => 'Feedback',
            'ratings' => 'Ratings',
            'review' => 'Review',
            'created_at' => 'Created At'
            
        ],
    ],

    'user-feedback-history' => [
        'title' => 'Feedback History',

        'actions' => [
            'index' => 'User Feedback History',
            'create' => 'New User Feedback History',
            'edit' => 'Edit :name',
            'export' => 'Export',
        ],

        'columns' => [
            'id' => 'ID',
            'subscriber_id' => 'Subscriber',
            'feedback_id' => 'Feedback',
            'ratings' => 'Ratings',
            'review' => 'Review',
            'skip' => 'Skip',
            'created_at' => 'Created At'
        ],
    ],

    'flash-news' => [
        'title' => 'News Content',

        'actions' => [
            'index' => 'Flash News',
            'create' => 'New Flash News',
            'edit' => 'Edit :name',
            'export' => 'Export',
        ],

        'columns' => [
            'id' => 'ID',
            'active' => 'Active',
            'author' => 'Author',
            'description' => 'Description',
            'href' => 'Href',
            'order_index' => 'Order index',
            'publish_date' => 'Publish date',
            'source' => 'Source',
            'title' => 'Title',
            'flash_news_icon' => 'Flash News Icon(90 * 90)',  
            'created_at' => 'Created At'
        ],
    ],

    'flash-similar-app' => [
        'title' => 'Flash Similar Apps',

        'actions' => [
            'index' => 'Flash Similar Apps',
            'create' => 'New Flash Similar App',
            'edit' => 'Edit :name',
            'export' => 'Export',
        ],

        'columns' => [
            'id' => 'ID',
            'active' => 'Active',
            'href' => 'Href',
            'href_web' => 'Href web',
            'href_ios' => 'Href ios',
            'order_index' => 'Order index',
            'sub_title' => 'Sub title',
            'title' => 'Title',
            'flash_app_icon' => 'App Icon (45 * 45)',  
            'created_at' => 'Created At'
        ],
    ],

    'assessment-enrollment' => [
        'title' => 'Ass. Enrollments',

        'actions' => [
            'index' => 'Assessment Enrollments',
            'create' => 'New Assessment Enrollment',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'assessment_id' => 'Assessment',
            'user_id' => 'User',
            'response' => 'Response',
            'send_inital_invitation' => 'Send inital invitation',
            'created_at' => 'Created At'
        ],
    ],

    'survey-master' => [
        'title' => 'Survey Master',

        'actions' => [
            'index' => 'Survey Master',
            'create' => 'New Survey Master',
            'edit' => 'Edit :name',
            'export' => 'Export',
        ],

        'columns' => [
            'id' => 'ID',
            'active' => 'Active',
            'country_id' => 'Country',
            'cadre_id' => 'Cadre',
            'state_id' => 'State',
            'district_id' => 'District',
            'cadre_type' => 'Cadre type',
            'order_index' => 'Order index',
            'title' => 'Title',
            'created_at' => 'Created At'
        ],
    ],

    'survey-master-question' => [
        'title' => 'Survey Questions',

        'actions' => [
            'index' => 'Survey Master Questions',
            'create' => 'New Survey Master Question',
            'edit' => 'Edit :name',
            'export' => 'Export',
        ],

        'columns' => [
            'id' => 'ID',
            'active' => 'Active',
            'option1' => 'Option1',
            'option2' => 'Option2',
            'option3' => 'Option3',
            'option4' => 'Option4',
            'order_index' => 'Order index',
            'question' => 'Question',
            'survey_master_id' => 'Survey master',
            'type' => 'Type',
            'created_at' => 'Created At'
        ],
    ],

    'survey-master-history' => [
        'title' => 'Survey Histories',

        'actions' => [
            'index' => 'Survey Master Histories',
            'create' => 'New Survey Master History',
            'edit' => 'Edit :name',
            'export' => 'Export',
        ],

        'columns' => [
            'id' => 'ID',
            'answer' => 'Answer',
            'survey_id' => 'Survey',
            'survey_question_id' => 'Survey question',
            'user_id' => 'User',
            'created_at' => 'Created At'
        ],
    ],

    'automatic-notification' => [
        'title' => 'Automatic Notify',

        'actions' => [
            'index' => 'Automatic Notifications',
            'create' => 'New Automatic Notification',
            'edit' => 'Edit :name',
            'export' => 'Export',
        ],

        'columns' => [
            'id' => 'ID',
            'description' => 'Description',
            'linking_url' => 'Linking url',
            'subscriber_id' => 'Subscriber',
            'title' => 'Title',
            'type' => 'Type',
            'created_at' => 'Created Date',
            'created_by' => 'Created By',
            'successful_count' => 'Successful Count',
            'failed_count' => 'Failed Count',
            'status' => 'Status',
        ],
    ],

    'assessment-certificate' => [
        'title' => 'Ass. Certificate',

        'actions' => [
            'index' => 'Assessment Certificates',
            'create' => 'New Assessment Certificate',
            'edit' => 'Edit :name',
        ],

        'columns' => [
            'id' => 'ID',
            'title' => 'Title',
            'top' => 'Top',
            'left' => 'Left',
            'certificate' => 'Certificate (2000 * 1414)',
            'created_by' => 'Created By',
        ],
    ],

    // Do not delete me :) I'm used for auto-generation
];
