<?php

namespace App\Console\Commands;

use App\Models\Hafalan;
use App\Services\AudioAnalysisService;
use Illuminate\Console\Command;

class AnalyzeHafalanAudios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hafalan:analyze-audios {--limit=10} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze unprocessed audio files for hafalan records';

    /**
     * Execute the console command.
     */
    public function handle(AudioAnalysisService $audioService)
    {
        $limit = $this->option('limit');
        $force = $this->option('force');

        $query = Hafalan::where('s3_audio_path', '!=', null);
        
        if (!$force) {
            $query->where('is_audio_analyzed', false);
        }

        $hafalans = $query->limit($limit)->get();

        if ($hafalans->isEmpty()) {
            $this->info('Tidak ada audio yang perlu dianalisis.');
            return 0;
        }

        $this->info("Menganalisis {$hafalans->count()} audio file...");
        $bar = $this->output->createProgressBar($hafalans->count());
        $bar->start();

        $processed = 0;
        $failed = 0;

        foreach ($hafalans as $hafalan) {
            try {
                $result = $audioService->analyzeAudio($hafalan->s3_audio_path);

                if ($result['success']) {
                    $metadata = $result['metadata'];
                    $analysis = $result['analysis'];

                    $hafalan->update([
                        'audio_quality_score' => $result['quality_score'],
                        'audio_bitrate' => $metadata['bitrate'],
                        'audio_codec' => $metadata['codec'],
                        'is_audio_analyzed' => true,
                        'analytics_insights' => json_encode($analysis),
                        'last_analyzed_at' => now(),
                    ]);

                    $processed++;
                } else {
                    $this->warn("Gagal analisis hafalan {$hafalan->id}: {$result['error']}");
                    $failed++;
                }
            } catch (\Exception $e) {
                $this->error("Error analyzing hafalan {$hafalan->id}: {$e->getMessage()}");
                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("✓ Berhasil dianalisis: {$processed}");
        if ($failed > 0) {
            $this->warn("✗ Gagal dianalisis: {$failed}");
        }

        return 0;
    }
}
