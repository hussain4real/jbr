<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('reference')->unique();
            $table->string('submission_hash', 64)->unique();
            $table->string('kind');
            $table->string('locale', 2);
            $table->string('name');
            $table->string('email');
            $table->string('phone', 32);
            $table->foreignId('accommodation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('accommodation_label')->nullable();
            $table->date('arrival')->nullable();
            $table->date('departure')->nullable();
            $table->unsignedSmallInteger('guests')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->default('new')->index();
            $table->string('outcome')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('staff_notes')->nullable();
            $table->timestamp('first_contacted_at')->nullable();
            $table->timestamp('follow_up_at')->nullable()->index();
            $table->string('reservation_reference')->nullable();
            $table->timestamp('closed_at')->nullable()->index();
            $table->timestamp('anonymized_at')->nullable();
            $table->string('staff_notification_status')->default('pending')->index();
            $table->string('guest_notification_status')->default('pending')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_requests');
    }
};
