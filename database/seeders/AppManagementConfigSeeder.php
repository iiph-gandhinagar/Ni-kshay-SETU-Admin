<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class AppManagementConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('app_management_flags')->insert([
            [
                'id' => 1,
                'variable' => 'IS_IN_MAINTENANCE',
                'value' => '{"en": "false", "gu": null}',
                'type' => 'boolean', 
                'created_at' => '2022-02-11 08:16:39',
                'updated_at' => '2022-02-11 10:43:42'
            ],
            [
                'id' => 2,
                'variable' => 'MAINTENANCE_MESSAGE',
                'value' => '{"en": "We are in a planned maintenance window between 10 Jan 7 PM to 10 Jan 10 PM", "gu": "અમે 10 જાન્યુઆરી 7 PM થી 10 જાન્યુઆરી 10 PM વચ્ચે આયોજિત જાળવણી વિંડોમાં છીએ", "hi": "हम 10 जनवरी 7 बजे से 10 जनवरी 10 बजे के बीच एक नियोजित रखरखाव विंडो में हैं"}',
                'type' => 'string', 
                'created_at' => '2022-02-11 08:16:39',
                'updated_at' => '2022-02-11 10:43:42'
            ],
            [
                'id' => 3,
                'variable' => 'ANDROID_APP_LATEST_VERSION',
                'value' => '{"en": "1.9", "gu": null}',
                'type' => 'float', 
                'created_at' => '2022-02-11 08:16:39',
                'updated_at' => '2022-02-11 10:43:42'
            ],
            [
                'id' => 4,
                'variable' => 'ANDROID_APP_LATEST_VERSION_MESSAGE',
                'value' => '{"en": "Newer version available on app store. Please update the app to get the latest features.", "gu": null}',
                'type' => 'string', 
                'created_at' => '2022-02-11 08:16:39',
                'updated_at' => '2022-02-11 10:43:42'
            ],
            [
                'id' => 5,
                'variable' => 'ANDROID_APP_MINIMUM_ALLOWED_VERSION',
                'value' => '{"en": "1.8", "gu": null}',
                'type' => 'float',
                'created_at' => '2022-02-11 08:16:39',
                'updated_at' => '2022-02-11 10:43:42'
            ],
            [
                'id' => 6,
                'variable' => 'ANDROID_APP_MINIMUM_ALLOWED_VERSION_MESSAGE',
                'value' => '{"en": "Newer version available on app store. Please update the app to continue using the app.", "gu": null}',
                'type' => 'string', 
                'created_at' => '2022-02-11 08:16:39',
                'updated_at' => '2022-02-11 10:43:42'
            ],
            [
                'id' => 7,
                'variable' => 'GERNERAL',
                'value' => '{"en": "Happy Birthday!!!", "gu": "જન્મદિવસ ની શુભકામના!!!", "hi": "जन्मदिन मुबारक!!!"}',
                'type' => 'string', 
                'created_at' => '2022-02-11 08:16:39',
                'updated_at' => '2022-02-11 10:43:42'
            ]

        ]);
    }
}
