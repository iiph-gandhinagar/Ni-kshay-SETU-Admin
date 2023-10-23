<?php

namespace Database\Seeders;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Seeder;

class ModuleMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_mapping_to_names')->insert([
            [
                'id' => 1,
                'module_name' => 'module_case_defintion',
                'mapping_name' => 'Case Definition',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
            ],
            [
                'id' => 2,
                'module_name' => 'module_screening_tool',
                'mapping_name' => 'Screening Tool',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
            ],
            [
                'id' => 3,
                'module_name' => 'module_differentiated_care_tb_patient',
                'mapping_name' => 'Differentiated Care TB Patient',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
            ],
            [
                'id' => 4,
                'module_name' => 'module_past_assessments',
                'mapping_name' => 'Past Assessments',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
            ],
            [
                'id' => 5,
                'module_name' => 'module_current_assessments',
                'mapping_name' => 'Current Assessments',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
            ],
            [
                'id' => 6,
                'module_name' => 'module_latent_tb',
                'mapping_name' => 'Latent Tb Infection',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
            ],
            [
                'id' => 7,
                'module_name' => 'module_guidance_on_adr',
                'mapping_name' => 'Guidance on ADR',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
            ],
            [
                'id' => 8,
                'module_name' => 'module_Resource_Materials_videos',
                'mapping_name' => 'Resource Material-Videos',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
            ],
            [
                'id' => 9,
                'module_name' => 'module_Resource_Materials_document',
                'mapping_name' => 'Resource Material-Documents',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
            ],
            [
                'id' => 10,
                'module_name' => 'module_Resource_Materials_ppt',
                'mapping_name' => 'Resource Material-PPT',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
            ],
            [
                'id' => 11,
                'module_name' => 'module_Resource_Materials_pdf_office_orders',
                'mapping_name' => 'Resource Material-PDF Officer Orders',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
            ],
            [
                'id' => 12,
                'module_name' => 'module_Referral-Health Facility',
                'mapping_name' => 'Referral Health Facility ',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
            ],
            [
                'id' => 13,
                'module_name' => 'module_treatment_care_cascade',
                'mapping_name' => 'Treatment Algorithms ',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
            ],
            [
                'id' => 14,
                'module_name' => 'module_cgc',
                'mapping_name' => 'CGC Module',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
            ],
            [
                'id' => 15,
                'module_name' => 'module_diagnostic_care_cascade',
                'mapping_name' => 'Diagnoses Algortihms',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
            ],
            [
                'id' => 16,
                'module_name' => 'module_Resource_Materials_image',
                'mapping_name' => 'Resource Material-Images',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
            ],
            [
                'id' => 17,
                'module_name' => 'module_Resource_Materials_pdfs',
                'mapping_name' => 'Resource Material-PDFs',
                'created_at' =>  Carbon::now(),
                'updated_at' =>  Carbon::now(),
            ]

        ]);
    }
}
