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
            // Audio Analysis Fields
            $table->integer('audio_quality_score')->nullable()->comment('Score kualitas audio 0-100');
            $table->integer('audio_bitrate')->nullable()->comment('Bitrate audio dalam kbps');
            $table->string('audio_codec')->nullable()->comment('Codec audio (mp3, aac, etc)');
            $table->boolean('is_audio_analyzed')->default(false)->comment('Flag untuk tracking audio analysis');
            
            // Analytics Fields
            $table->decimal('progress_percentage', 5, 2)->default(0)->comment('Persentase progres hafalan');
            $table->integer('days_to_completion')->nullable()->comment('Prediksi hari sampai selesai');
            $table->string('completion_status')->default('in_progress')->comment('Status: in_progress, on_track, at_risk, completed');
            $table->text('analytics_insights')->nullable()->comment('JSON insights & recommendations');
            
            // Metadata
            $table->timestamp('last_analyzed_at')->nullable()->comment('Waktu terakhir analytics dijalankan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hafalan', function (Blueprint $table) {
            $table->dropColumn([
                'audio_quality_score',
                'audio_bitrate',
                'audio_codec',
                'is_audio_analyzed',
                'progress_percentage',
                'days_to_completion',
                'completion_status',
                'analytics_insights',
                'last_analyzed_at',
            ]);
        });
    }
};
