<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hide legacy migration/visa enquiry types from the public booking form.
     */
    public function up(): void
    {
        if (! Schema::hasTable('nature_of_enquiry')) {
            return;
        }

        DB::table('nature_of_enquiry')
            ->whereIn('id', [9, 10, 11, 12])
            ->update(['status' => 0]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('nature_of_enquiry')) {
            return;
        }

        DB::table('nature_of_enquiry')
            ->whereIn('id', [9, 10, 11, 12])
            ->update(['status' => 1]);
    }
};
