<?php

namespace Database\Seeders;
use DB;

use Illuminate\Database\Seeder;

class TrainingModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('t_module_master')->truncate();
        DB::unprepared("insert into t_module_master (name, created_at, updated_at) VALUES
            ('TITLE_SCREENING', now(), now()),
            ('TITLE_CASE_DEFINITION', now(), now()),
            ('TITLE_DIAGNOSIS_ALGORITHM', now(), now()),
            ('TITLE_GUIDANCE_ON_ADR', now(), now()),
            ('TITLE_TREATMENT_ALGORITHM', now(), now()),
            ('TITLE_LATENT_TB_INFECTION', now(), now()),
            ('TITLE_DIFFERENTIANTED_CARE', now(), now()),
            ('TITLE_CGC_INTERVENTION', now(), now()),
            ('TITLE_RESOURCE_MATERIALS', now(), now()),
            ('TITLE_REFERRAL_HEALTH_FACILITY', now(), now()),
            ('CARD_ASSESSEMENT_CURRENT_ASSESSMENTS', now(), now()),
            ('CARD_ASSESSEMENT_PAST_ASSESSMENTS', now(), now())
        ");
    }
}
