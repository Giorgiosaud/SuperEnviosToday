<?php

use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Setting::class)->create(['key'=>'venezuelanBankTax', 'value'=>'2']);
        factory(\App\Setting::class)->create(['key'=>'status', 'value'=>'1']);
        //
    }
}
