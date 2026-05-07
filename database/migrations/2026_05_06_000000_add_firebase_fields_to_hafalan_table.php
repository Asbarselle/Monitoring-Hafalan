<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tambahkan field Firebase untuk menyimpan URL dan metadata audio di Firebase Storage
     */
    public function up(): void
    {
        Schema::table('hafalan', function (Blueprint $table) {
            // Firebase Storage fields
            $table->string('firebase_audio_path')->nullable()->after('audio_duration')->comment('Path file di Firebase Storage');
            $table->longText('firebase_audio_url')->nullable()->after('firebase_audio_path')->comment('Signed URL Firebase Storage');
            $table->timestamp('firebase_uploaded_at')->nullable()->after('firebase_audio_url')->comment('Waktu upload ke Firebase');
            $table->boolean('firebase_sync_status')->default(false)->after('firebase_uploaded_at')->comment('Status sinkronisasi ke Firebase (true=berhasil)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hafalan', function (Blueprint $table) {
            $table->dropColumn([
                'firebase_audio_path',
                'firebase_audio_url',
                'firebase_uploaded_at',
                'firebase_sync_status',
            ]);
        });
    }
};
