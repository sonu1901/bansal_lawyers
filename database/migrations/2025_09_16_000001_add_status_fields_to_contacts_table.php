<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('contacts')) {
            return;
        }

        Schema::table('contacts', function (Blueprint $table) {
            $table->enum('status', ['unread', 'read', 'resolved', 'archived'])->default('unread')->after('message');
            $table->string('forwarded_to')->nullable()->after('status');
            $table->timestamp('forwarded_at')->nullable()->after('forwarded_to');
            
            // Add indexes for better performance
            $table->index(['status'], 'idx_contacts_status');
            $table->index(['forwarded_at'], 'idx_contacts_forwarded_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasTable('contacts')) {
            return;
        }

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex('idx_contacts_status');
            $table->dropIndex('idx_contacts_forwarded_at');
            $table->dropColumn(['status', 'forwarded_to', 'forwarded_at']);
        });
    }
};
