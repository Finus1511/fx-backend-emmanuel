<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB, Schema;

class PostAudioThumbnailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            [
                'key' => 'audio_thumbnail_placeholder',
                'value' => asset('placeholder.jpeg')
            ]
        ]);
    }
}
