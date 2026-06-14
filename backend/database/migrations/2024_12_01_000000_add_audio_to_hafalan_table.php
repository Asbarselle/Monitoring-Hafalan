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
        Schema::table('hafalan', function (Blueprint $table) {
            $table->string('audio_path')->nullable()->after('nilai');
            $table->string('audio_filename')->nullable()->after('audio_path');
            $table->integer('audio_duration')->nullable()->after('audio_filename'); // duration in seconds
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hafalan', function (Blueprint $table) {
            $table->dropColumn(['audio_path', 'audio_filename', 'audio_duration']);
        });
    }
};
