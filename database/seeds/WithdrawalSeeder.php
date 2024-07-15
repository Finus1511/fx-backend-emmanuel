<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB, Schema;

class WithdrawalSeeder extends Seeder
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
		        'key' => 'user_withdrawals_min_amount',
		        'value' => '0'
		    ],
    		[
		        'key' => 'user_withdrawals_min_balance',
		        'value' => '0'
            ]
		]);
    }
}
