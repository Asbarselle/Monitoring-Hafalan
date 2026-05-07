<?php

namespace App\Console\Commands;

use App\Models\Santri;
use App\Services\HafalanAnalyticsService;
use Illuminate\Console\Command;

class RecalculateHafalanPredictions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hafalan:recalculate-predictions {--santri_id=} {--limit=50}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate hafalan predictions for all or specific santri';

    /**
     * Execute the console command.
     */
    public function handle(HafalanAnalyticsService $analyticsService)
    {
        $santriId = $this->option('santri_id');
        $limit = $this->option('limit');

        $query = Santri::with('hafalan');
        
        if ($santriId) {
            $query->where('id', $santriId);
        }

        $santris = $query->limit($limit)->get();

        if ($santris->isEmpty()) {
            $this->info('Tidak ada santri yang ditemukan.');
            return 0;
        }

        $this->info("Menghitung prediksi untuk {$santris->count()} santri...");
        $bar = $this->output->createProgressBar($santris->count());
        $bar->start();

        foreach ($santris as $santri) {
            try {
                $prediction = $analyticsService->predictCompletion($santri->id);
                
                // Update hafalan records dengan prediction data
                $santri->hafalan()->update([
                    'progress_percentage' => $prediction['completion_percentage'],
                    'days_to_completion' => $prediction['days_remaining'],
                    'completion_status' => $this->determineCompletionStatus($prediction),
                    'last_analyzed_at' => now(),
                ]);

            } catch (\Exception $e) {
                $this->error("Error calculating prediction for santri {$santri->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info('✓ Prediksi berhasil diperbarui untuk semua santri.');

        return 0;
    }

    /**
     * Determine completion status dari prediction data
     */
    private function determineCompletionStatus(array $prediction): string
    {
        $completion = $prediction['completion_percentage'];

        if ($completion >= 100) {
            return 'completed';
        }

        if ($completion >= 80) {
            return 'on_track';
        }

        if ($completion >= 50) {
            return 'in_progress';
        }

        return 'at_risk';
    }
}
