<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AnalyticsController;

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // Analytics Routes
    Route::prefix('analytics')->name('analytics.')->group(function () {
        // Santri Analytics
        Route::get('/santri/{id}/progress', [AnalyticsController::class, 'getSantriProgress'])
            ->name('santri.progress');
        Route::get('/santri/{id}/prediction', [AnalyticsController::class, 'getSantriPrediction'])
            ->name('santri.prediction');
        Route::get('/santri/{id}/report', [AnalyticsController::class, 'getSantriReport'])
            ->name('santri.report');

        // Class Analytics
        Route::get('/class/{ustadzId}/insights', [AnalyticsController::class, 'getClassInsights'])
            ->name('class.insights');

        // Audio Quality
        Route::get('/audio/{hafalanId}/quality', [AnalyticsController::class, 'getAudioQuality'])
            ->name('audio.quality');

        // Admin Only
        Route::middleware('role:admin')->group(function () {
            Route::get('/struggling-students', [AnalyticsController::class, 'getStrugglingStudents'])
                ->name('struggling-students');
            Route::post('/process-audios', [AnalyticsController::class, 'processAudios'])
                ->name('process-audios');
        });

        // Metrics Definition
        Route::get('/metrics-definition', [AnalyticsController::class, 'getMetricsDefinition'])
            ->name('metrics-definition');
    });
});
