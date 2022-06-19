<?php

namespace Database\Seeders;

use App\Models\Breaks;
use App\Models\Categories;
use App\Models\Holidays;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class categoryAndDefaultDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Categories::create([
            'name' => 'Men Haircut',
            'time_of_slot' => 10,
            'clean_up_time' => 5,
            'max_client' => 3,
            'future_days_to_book' => 7,
            'mon_fri_from_time' => '08:00:00',
            'mon_fri_to_time' => '20:00:00',
            'sat_from_time' => '10:00:00',
            'sat_to_time' => '22:00:00'
        ]);

        Breaks::create([
            'name' => 'Lunch',
            'from_time' => '12:00:00',
            'to_time' => '13:00:00'
        ]);

        Breaks::create([
            'name' => 'Cleaning',
            'from_time' => '15:00:00',
            'to_time' => '16:00:00'
        ]);

        Holidays::create([
            'name' => 'Public Holiday',
            'date' => Carbon::now()->addDays(3)->format('Y-m-d')
        ]);

        Categories::create([
            'name' => 'Woman Haircut',
            'time_of_slot' => 60,
            'clean_up_time' => 5,
            'max_client' => 3,
            'future_days_to_book' => 7,
            'mon_fri_from_time' => '08:00:00',
            'mon_fri_to_time' => '20:00:00',
            'sat_from_time' => '10:00:00',
            'sat_to_time' => '22:00:00'
        ]);
    }
}
