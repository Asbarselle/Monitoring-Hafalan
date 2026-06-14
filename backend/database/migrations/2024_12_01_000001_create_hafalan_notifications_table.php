<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hafalan_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hafalan_id')->constrained('hafalan')->onDelete('cascade');
            $table->foreignId('santri_id')->constrained('santri')->onDelete('cascade');
            $table->foreignId('ustadz_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('parent_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('type', ['hafalan_addition', 'hafalan_statusupdate', 'hafalan_reminder'])->default('hafalan_addition');
            $table->enum('notification_channel', ['whatsapp', 'app_notification', 'email'])->default('whatsapp');
            $table->enum('status', ['pending', 'sent', 'failed', 'read'])->default('pending');
            $table->text('message')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('external_id')->nullable(); // untuk tracking WhatsApp message ID
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hafalan_notifications');
    }
};
