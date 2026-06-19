<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('book_services')) {
            return;
        }

        $now = now();

        DB::table('book_services')->updateOrInsert(
            ['id' => 1],
            [
                'title' => '30 Minute Consultation',
                'price' => 'aud150',
                'duration' => '30',
                'duration_for_display' => '30',
                'status' => 1,
                'description' => '30 minute legal consultation — $150 AUD (incl. GST)',
                'updated_at' => $now,
                'created_at' => DB::table('book_services')->where('id', 1)->value('created_at') ?? $now,
            ]
        );

        DB::table('book_services')->updateOrInsert(
            ['id' => 2],
            [
                'title' => '10 Minute Free Consultation',
                'price' => 'aud0',
                'duration' => '10',
                'duration_for_display' => '10',
                'status' => 1,
                'description' => 'First-time 10 minute consultation — free',
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        DB::table('book_services')->updateOrInsert(
            ['id' => 3],
            [
                'title' => '1 Hour Consultation',
                'price' => 'aud220',
                'duration' => '60',
                'duration_for_display' => '60',
                'status' => 1,
                'description' => 'Up to 1 hour legal consultation — $220 AUD (incl. GST)',
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );
    }

    public function down(): void
    {
        if (! Schema::hasTable('book_services')) {
            return;
        }

        DB::table('book_services')->where('id', 2)->delete();
        DB::table('book_services')->where('id', 3)->delete();

        DB::table('book_services')->where('id', 1)->update([
            'title' => 'Paid',
            'price' => 'aud150',
            'duration' => '30',
            'duration_for_display' => '30',
            'description' => 'Our charges are aud150 for 30 mins',
            'updated_at' => now(),
        ]);
    }
};
