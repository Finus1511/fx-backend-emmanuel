<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB, Schema;

class InAppPurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        \DB::table('settings')->insert([
            [
		        'key' => 'in_app_purchase_enabled',
		        'value' => NO
		    ]
		]);
    }
}
