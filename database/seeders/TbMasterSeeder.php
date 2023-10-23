<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class TbMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tb_master')->insert([
            [
                'id' => 1,
                'title' => 'Presumptive Pulmonary TB',
                'short_name' => 'PTB',
            ],
            [
                'id' => 2,
                'title' => 'Presumptive Extra-Pulmonary TB',
                'short_name' => 'PETB',
            ],
            [
                'id' => 3,
                'title' => 'Presumptive Pediatric TB',
                'short_name' => 'PPTB',
            ],
            [
                'id' => 4,
                'title' => 'Presumptive Extra-Pediatric TB',
                'short_name' => 'PEPTB',
            ]
        ]);
    }
}
