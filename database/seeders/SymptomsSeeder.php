<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class SymptomsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('symptoms')->insert([
            [
                'id' => 1,
                'category' => '1',
                'symptoms_title' => '{"en": "Cough for more than 2 weeks", "gu": null}',
                'created_at' => '2021-07-05 03:45:35',
                'updated_at' => '2021-07-05 03:45:35',
            ],
            [
                'id' => 2,
                'category' => '1',
                'symptoms_title' => '{"en": "Unexplained significant weight loss / no weight gain", "gu": null}',
                'created_at' => '2021-07-05 03:45:35',
                'updated_at' => '2021-07-05 03:45:35',
            ],
            [
                'id' => 3,
                'category' => '1',
                'symptoms_title' => '{"en": "History of contact with infectious TB case", "gu": null}',
                'created_at' => '2021-07-05 03:45:35',
                'updated_at' => '2021-07-05 03:45:35',
            ],
            [
                'id' => 4,
                'category' => '1',
                'symptoms_title' => '{"en": "Hemoptysis (Coughing of Blood)", "gu": null}',
                'created_at' => '2021-07-05 03:45:35',
                'updated_at' => '2021-07-05 03:45:35',
            ],
            [
                'id' => 5,
                'category' => '1',
                'symptoms_title' => '{"en": "Unexplained Night sweats", "gu": null}',
                'created_at' => '2021-07-05 03:45:35',
                'updated_at' => '2021-07-05 03:45:35',
            ],
            [
                'id' => 6,
                'category' => '1',
                'symptoms_title' => '{"en": "Any abnormality in chest radiograph", "gu": null}',
                'created_at' => '2021-07-05 03:45:35',
                'updated_at' => '2021-07-05 03:45:35',
            ],
            [
                'id' => 7,
                'category' => '1',
                'symptoms_title' => '{"en": "Unexplained Fever for more than 2 weeks", "gu": null}',
                'created_at' => '2021-07-05 03:45:35',
                'updated_at' => '2021-07-05 03:45:35',
            ],
            [
                'id' => 8,
                'category' => '2',
                'symptoms_title' => '{"en": "Swelling of lymph node (neck region, axilla)", "gu": null}',
                'created_at' => '2021-07-05 03:45:35',
                'updated_at' => '2021-07-05 03:45:35',
            ],
            [
                'id' => 9,
                'category' => '2',
                'symptoms_title' => '{"en": "pain and swelling in joints", "gu": null}',
                'created_at' => '2021-07-05 03:45:35',
                'updated_at' => '2021-07-05 03:45:35',
            ],
            [
                'id' => 10,
                'category' => '2',
                'symptoms_title' => '{"en": "neck stiffness, disorientation, confusion, dizziness", "gu": null}',
                'created_at' => '2021-07-05 03:45:35',
                'updated_at' => '2021-07-05 03:45:35',
            ],
            [
                'id' => 11,
                'category' => '2',
                'symptoms_title' =>  '{"en": "Unexplained Pain in abdominal region", "gu": null}',
                'created_at' => '2021-07-05 03:45:35',
                'updated_at' => '2021-07-05 03:45:35',
            ],
            [
                'id' => 12,
                'category' => '2',
                'symptoms_title' => '{"en": "Unexplained swelling in any body region (except nails and hairs)", "gu": null}',
                'created_at' => '2021-07-05 03:45:35',
                'updated_at' => '2021-07-05 03:45:35',
            ]
        ]);
    }
}
