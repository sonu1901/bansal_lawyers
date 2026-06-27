<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookServiceSlotPerPeopleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');
        $exists = DB::table('book_service_slot_per_people')
            ->where('person_id', 1)
            ->where('service_type', 1)
            ->exists();
        if (!$exists) {
            DB::table('book_service_slot_per_people')->insert([
                'person_id' => 1,
                'service_type' => 1,
                'book_service_id' => 1,
                'start_time' => '09:00',
                'end_time' => '17:00',
                'weekend' => 'Sun,Sat',
                'disabledates' => '',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}


