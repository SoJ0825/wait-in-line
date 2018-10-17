<?php

use Illuminate\Database\Seeder;

class DesksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Desk::truncate();

        for ($i = 1; $i < 6; $i++) {
            \App\Desk::create([
                'id' => $i,
            ]);
        }
    }
}
