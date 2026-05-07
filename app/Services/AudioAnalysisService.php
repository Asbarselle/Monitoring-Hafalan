<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use getID3;
use Exception;

/**
 * Audio Analysis Service
 * Menganalisis metadata dan kualitas audio file
 */
class AudioAnalysisService
{
    // Audio quality standards
    const MIN_BITRATE = 128; // kbps
    const MIN_DURATION = 30; // seconds
    const EXPECTED_CODEC = 'mp3'; // atau aac
    
    // Quality scoring
    const QUALITY_SCORE_WEIGHTS = [
        'bitrate' => 0.4,      // 40% dari total score
        'codec' => 0.3,        // 30%
        'duration' => 0.3,     // 30%
    ];

    /**
     * Analyze audio file dari S3 path
     */
    public function analyzeAudio(string $s3Path): array
    {
        try {
            // Download temp file dari S3
            $audioContent = Storage::disk('s3')->get($s3Path);
            $tempPath = storage_path('temp/audio_' . uniqid() . '.mp3');
            
            // Ensure temp directory exists
            if (!is_dir(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0755, true);
            }

            file_put_contents($tempPath, $audioContent);

            // Analyze using getID3
            $metadata = $this->extractMetadata($tempPath);
            
            // Calculate quality score
            $qualityScore = $this->calculateQualityScore($metadata);
            
            // Clean temp file
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

            return [
                'success' => true,
                'metadata' => $metadata,
                'quality_score' => $qualityScore,
                'analysis' => $this->generateAnalysisReport($metadata, $qualityScore),
                'analyzed_at' => now(),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Extract metadata dari audio file
     */
    private function extractMetadata(string $filePath): array
    {
        try {
            $getID3 = new getID3();
            $info = $getID3->analyze($filePath);

            if (isset($info['error'])) {
                throw new Exception('Error analyzing file: ' . implode(', ', $info['error']));
            }

            $duration = $info['playtime_seconds'] ?? 0;
            $bitrate = $info['bitrate'] ?? 0;
            $codec = $info['audio']['codec'] ?? 'unknown';
            $sampleRate = $info['audio']['sample_rate'] ?? 0;
            $channels = $info['audio']['channels'] ?? 0;

            return [
                'duration' => (int)$duration,
                'bitrate' => (int)($bitrate / 1000), // Convert to kbps
                'codec' => strtolower($codec),
                'sample_rate' => (int)$sampleRate,
                'channels' => (int)$channels,
                'file_format' => $info['file_format'] ?? 'unknown',
                'mime_type' => $info['mime_type'] ?? 'unknown',
            ];
        } catch (Exception $e) {
            throw new Exception('Failed to extract metadata: ' . $e->getMessage());
        }
    }

    /**
     * Calculate quality score berdasarkan metadata
     */
    private function calculateQualityScore(array $metadata): int
    {
        $bitrateScore = $this->scoreBitrate($metadata['bitrate']);
        $codecScore = $this->scoreCodec($metadata['codec']);
        $durationScore = $this->scoreDuration($metadata['duration']);

        $totalScore = 
            ($bitrateScore * self::QUALITY_SCORE_WEIGHTS['bitrate']) +
            ($codecScore * self::QUALITY_SCORE_WEIGHTS['codec']) +
            ($durationScore * self::QUALITY_SCORE_WEIGHTS['duration']);

        return (int)min(100, max(0, $totalScore));
    }

    /**
     * Score bitrate quality (0-100)
     */
    private function scoreBitrate(int $bitrate): float
    {
        if ($bitrate >= 192) return 100;
        if ($bitrate >= 128) return 80;
        if ($bitrate >= 96) return 60;
        if ($bitrate >= 64) return 40;
        return 20;
    }

    /**
     * Score codec quality (0-100)
     */
    private function scoreCodec(string $codec): float
    {
        $codec = strtolower($codec);
        
        return match ($codec) {
            'mp3', 'aac', 'opus' => 100,
            'vorbis', 'flac' => 90,
            'wma', 'ac3' => 70,
            default => 50,
        };
    }

    /**
     * Score duration quality (0-100)
     */
    private function scoreDuration(int $duration): float
    {
        // Optimal duration untuk hafalan: 1-5 menit (60-300 detik)
        if ($duration >= 60 && $duration <= 300) return 100;
        if ($duration >= 45 && $duration <= 360) return 80;
        if ($duration >= 30) return 60;
        if ($duration >= 20) return 40;
        return 20;
    }

    /**
     * Generate analysis report
     */
    private function generateAnalysisReport(array $metadata, int $qualityScore): array
    {
        $insights = [];
        $recommendations = [];

        // Bitrate insights
        if ($metadata['bitrate'] < 128) {
            $insights[] = 'Bitrate audio rendah (' . $metadata['bitrate'] . ' kbps)';
            $recommendations[] = 'Rekam ulang dengan kualitas bitrate minimal 128 kbps';
        } elseif ($metadata['bitrate'] >= 192) {
            $insights[] = 'Kualitas bitrate sangat baik (' . $metadata['bitrate'] . ' kbps)';
        }

        // Duration insights
        if ($metadata['duration'] < 30) {
            $insights[] = 'Audio terlalu pendek (hanya ' . $metadata['duration'] . ' detik)';
            $recommendations[] = 'Pastikan hafalan direkam dengan lengkap minimal 30 detik';
        } elseif ($metadata['duration'] > 600) {
            $insights[] = 'Audio sangat panjang (' . floor($metadata['duration'] / 60) . ' menit)';
            $recommendations[] = 'Pertimbangkan split audio menjadi beberapa file untuk efisiensi';
        } else {
            $insights[] = 'Durasi audio optimal (' . floor($metadata['duration'] / 60) . ' menit)';
        }

        // Codec insights
        if (in_array($metadata['codec'], ['mp3', 'aac'])) {
            $insights[] = 'Format codec ' . strtoupper($metadata['codec']) . ' sangat baik';
        }

        // Overall quality
        if ($qualityScore >= 80) {
            $insights[] = 'Kualitas audio: Sangat Baik';
        } elseif ($qualityScore >= 60) {
            $insights[] = 'Kualitas audio: Baik';
            $recommendations[] = 'Tingkatkan kualitas audio untuk hasil yang lebih optimal';
        } else {
            $insights[] = 'Kualitas audio: Perlu Diperbaiki';
            $recommendations[] = 'Rekam ulang dengan kondisi lingkungan yang lebih tenang';
        }

        return [
            'quality_level' => $qualityScore >= 80 ? 'Excellent' : ($qualityScore >= 60 ? 'Good' : 'Needs Improvement'),
            'insights' => $insights,
            'recommendations' => $recommendations,
            'metadata_summary' => [
                'duration_minutes' => round($metadata['duration'] / 60, 2),
                'bitrate_kbps' => $metadata['bitrate'],
                'codec' => strtoupper($metadata['codec']),
                'sample_rate' => $metadata['sample_rate'] . ' Hz',
                'channels' => $metadata['channels'],
            ],
        ];
    }

    /**
     * Batch analyze multiple audio files
     */
    public function batchAnalyzeAudios(array $s3Paths): array
    {
        $results = [];
        
        foreach ($s3Paths as $path) {
            $results[$path] = $this->analyzeAudio($path);
        }

        return $results;
    }

    /**
     * Get quality standards info
     */
    public static function getQualityStandards(): array
    {
        return [
            'min_bitrate' => self::MIN_BITRATE . ' kbps',
            'min_duration' => self::MIN_DURATION . ' seconds',
            'supported_codecs' => ['mp3', 'aac', 'opus'],
            'recommended_bitrate' => '192 kbps',
            'recommended_sample_rate' => '44100 Hz',
        ];
    }
}
