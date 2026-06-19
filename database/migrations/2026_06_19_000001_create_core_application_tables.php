<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('blog_categories')) {
            Schema::create('blog_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->boolean('status')->default(1);
                $table->timestamps();

                $table->index(['parent_id']);
                $table->index(['status']);
            });
        }

        if (! Schema::hasTable('blogs')) {
            Schema::create('blogs', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('alias')->nullable();
                $table->text('content')->nullable();
                $table->string('slug')->unique();
                $table->string('image')->nullable();
                $table->longText('description')->nullable();
                $table->string('short_description')->nullable();
                $table->unsignedBigInteger('parent_category')->nullable();
                $table->boolean('status')->default(0);
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->string('meta_keyword')->nullable();
                $table->string('image_alt')->nullable();
                $table->text('youtube_url')->nullable();
                $table->string('pdf_doc')->nullable();
                $table->timestamps();

                $table->index(['status', 'parent_category']);
                $table->index(['slug', 'status']);
                $table->index(['created_at', 'status']);
                $table->index(['parent_category']);
            });
        }

        if (! Schema::hasTable('cms_pages')) {
            Schema::create('cms_pages', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('alias')->nullable();
                $table->longText('content')->nullable();
                $table->string('image')->nullable();
                $table->string('image_alt')->nullable();
                $table->string('slug')->unique();
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->string('meta_keyward')->nullable();
                $table->text('youtube_url')->nullable();
                $table->string('pdf_doc')->nullable();
                $table->boolean('status')->default(0);
                $table->unsignedInteger('user_id')->nullable();
                $table->string('service_type')->nullable();
                $table->unsignedBigInteger('service_cat_id')->nullable();
                $table->timestamps();

                $table->index(['slug', 'status']);
                $table->index(['service_cat_id']);
                $table->index(['status']);
            });
        }

        if (! Schema::hasTable('contacts')) {
            Schema::create('contacts', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('contact_email')->nullable();
                $table->string('contact_phone')->nullable();
                $table->string('department')->nullable();
                $table->string('subject')->nullable();
                $table->text('message')->nullable();
                $table->string('image')->nullable();
                $table->string('branch')->nullable();
                $table->string('fax')->nullable();
                $table->string('position')->nullable();
                $table->string('primary_contact')->nullable();
                $table->string('countrycode')->nullable();
                $table->unsignedInteger('user_id')->nullable();
                $table->enum('status', ['unread', 'read', 'resolved', 'archived'])->default('unread');
                $table->string('forwarded_to')->nullable();
                $table->timestamp('forwarded_at')->nullable();
                $table->timestamps();

                $table->index(['created_at']);
                $table->index(['contact_email']);
                $table->index(['status']);
                $table->index(['forwarded_at']);
            });
        }

        if (! Schema::hasTable('enquiries')) {
            Schema::create('enquiries', function (Blueprint $table) {
                $table->id();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('country')->nullable();
                $table->string('city')->nullable();
                $table->text('address')->nullable();
                $table->string('subject')->nullable();
                $table->text('message')->nullable();
                $table->timestamps();

                $table->index(['created_at']);
                $table->index(['email']);
            });
        }

        if (! Schema::hasTable('book_services')) {
            Schema::create('book_services', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('price')->nullable();
                $table->string('duration')->nullable();
                $table->string('duration_for_display')->nullable();
                $table->boolean('status')->default(1);
                $table->text('description')->nullable();
                $table->timestamps();

                $table->index(['status']);
            });
        }

        if (! Schema::hasTable('appointments')) {
            Schema::create('appointments', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id')->nullable();
                $table->unsignedInteger('client_id')->nullable();
                $table->string('client_unique_id')->nullable();
                $table->string('timezone')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->unsignedBigInteger('noe_id')->nullable();
                $table->unsignedBigInteger('service_id')->nullable();
                $table->unsignedInteger('assignee')->nullable();
                $table->string('full_name')->nullable();
                $table->date('date')->nullable();
                $table->string('time')->nullable();
                $table->string('timeslot_full')->nullable();
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->unsignedTinyInteger('invites')->default(0);
                $table->unsignedTinyInteger('status')->default(0);
                $table->boolean('reminder_sent')->default(false);
                $table->boolean('immediate_reminder_sent')->default(false);
                $table->timestamp('reminder_sent_at')->nullable();
                $table->timestamp('immediate_reminder_sent_at')->nullable();
                $table->json('notification_preferences')->nullable();
                $table->text('appointment_details')->nullable();
                $table->string('order_hash')->nullable();
                $table->string('related_to')->nullable();
                $table->timestamps();

                $table->index(['status', 'date']);
                $table->index(['client_id', 'status']);
                $table->index(['date', 'time']);
                $table->index(['service_id']);
                $table->index(['noe_id']);
                $table->index(['created_at']);
            });
        }

        if (! Schema::hasTable('book_service_disable_slots')) {
            Schema::create('book_service_disable_slots', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('book_service_id')->nullable();
                $table->unsignedBigInteger('book_service_slot_per_person_id')->nullable();
                $table->date('disabledates')->nullable();
                $table->string('slots')->nullable();
                $table->boolean('block_all')->default(false);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('tbl_paid_appointment_payment')) {
            Schema::create('tbl_paid_appointment_payment', function (Blueprint $table) {
                $table->id();
                $table->string('order_hash')->nullable();
                $table->string('payer_email')->nullable();
                $table->decimal('amount', 10, 2)->nullable();
                $table->string('currency', 10)->nullable();
                $table->string('payment_type')->nullable();
                $table->dateTime('order_date')->nullable();
                $table->string('order_status')->nullable();
                $table->text('notes')->nullable();
                $table->string('name')->nullable();
                $table->text('address')->nullable();
                $table->string('country')->nullable();
                $table->string('postal_code')->nullable();
                $table->string('stripe_payment_intent_id')->nullable();
                $table->string('payment_status')->nullable();
                $table->string('stripe_payment_status')->nullable();
                $table->json('stripe_payment_response')->nullable();

                $table->index(['order_hash']);
            });
        }

        if (Schema::hasTable('recent_cases')) {
            Schema::table('recent_cases', function (Blueprint $table) {
                if (! Schema::hasColumn('recent_cases', 'title')) {
                    $table->string('title')->nullable();
                }
                if (! Schema::hasColumn('recent_cases', 'alias')) {
                    $table->string('alias')->nullable();
                }
                if (! Schema::hasColumn('recent_cases', 'content')) {
                    $table->longText('content')->nullable();
                }
                if (! Schema::hasColumn('recent_cases', 'description')) {
                    $table->longText('description')->nullable();
                }
                if (! Schema::hasColumn('recent_cases', 'short_description')) {
                    $table->string('short_description')->nullable();
                }
                if (! Schema::hasColumn('recent_cases', 'slug')) {
                    $table->string('slug')->nullable();
                }
                if (! Schema::hasColumn('recent_cases', 'image')) {
                    $table->string('image')->nullable();
                }
                if (! Schema::hasColumn('recent_cases', 'image_alt')) {
                    $table->string('image_alt')->nullable();
                }
                if (! Schema::hasColumn('recent_cases', 'status')) {
                    $table->boolean('status')->default(0);
                }
                if (! Schema::hasColumn('recent_cases', 'meta_title')) {
                    $table->string('meta_title')->nullable();
                }
                if (! Schema::hasColumn('recent_cases', 'meta_description')) {
                    $table->text('meta_description')->nullable();
                }
                if (! Schema::hasColumn('recent_cases', 'meta_keyword')) {
                    $table->string('meta_keyword')->nullable();
                }
                if (! Schema::hasColumn('recent_cases', 'youtube_url')) {
                    $table->text('youtube_url')->nullable();
                }
                if (! Schema::hasColumn('recent_cases', 'pdf_doc')) {
                    $table->string('pdf_doc')->nullable();
                }
            });
        }

        $this->seedDefaults();
    }

    private function seedDefaults(): void
    {
        $now = now();

        if (Schema::hasTable('book_services') && DB::table('book_services')->count() === 0) {
            DB::table('book_services')->insert([
                [
                    'id' => 1,
                    'title' => '30 Minute Consultation',
                    'price' => 'aud150',
                    'duration' => '30',
                    'duration_for_display' => '30',
                    'status' => 1,
                    'description' => '30 minute legal consultation — $150 AUD (incl. GST)',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id' => 2,
                    'title' => '10 Minute Free Consultation',
                    'price' => 'aud0',
                    'duration' => '10',
                    'duration_for_display' => '10',
                    'status' => 1,
                    'description' => 'First-time 10 minute consultation — free',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id' => 3,
                    'title' => '1 Hour Consultation',
                    'price' => 'aud220',
                    'duration' => '60',
                    'duration_for_display' => '60',
                    'status' => 1,
                    'description' => 'Up to 1 hour legal consultation — $220 AUD (incl. GST)',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);
        }

        if (Schema::hasTable('nature_of_enquiry') && DB::table('nature_of_enquiry')->count() === 0) {
            $types = [
                'Immigration Law',
                'Family Law',
                'Property Law',
                'Commercial Law',
                'Criminal Law',
            ];

            foreach ($types as $index => $title) {
                DB::table('nature_of_enquiry')->insert([
                    'id' => $index + 1,
                    'title' => $title,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_paid_appointment_payment');
        Schema::dropIfExists('book_service_disable_slots');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('book_services');
        Schema::dropIfExists('enquiries');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('cms_pages');
        Schema::dropIfExists('blogs');
        Schema::dropIfExists('blog_categories');
    }
};
