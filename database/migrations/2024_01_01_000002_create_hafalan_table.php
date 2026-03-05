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
        Schema::create('hafalan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri')->onDelete('cascade');
            $table->foreignId('ustadz_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('juz')->nullable();
            $table->string('surat')->nullable();
            $table->string('ayat_dari')->nullable();
            $table->string('ayat_sampai')->nullable();
            $table->enum('status', ['belum', 'sedang', 'selesai', 'mengulang'])->default('belum');
            $table->text('catatan')->nullable();
            $table->date('tanggal_setoran')->nullable();
            $table->integer('nilai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hafalan');
    }
};
