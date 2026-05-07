<?php

namespace App\Services;

use App\Models\Hafalan;
use App\Models\Santri;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Hafalan Analytics Service
 * Menganalisis progress hafalan dengan metode komputasi
 * (predictive analytics, velocity calculation, recommendations)
 */
class HafalanAnalyticsService
{
    // Target verses per surah (Al-Qur'an memiliki 30 juz)
    const TOTAL_VERSES = 6236;
    const TOTAL_SURAHS = 114;
    const TOTAL_JUZS = 30;
    
    // Analytics thresholds
    const ON_TRACK_PERCENTAGE = 0.8; // 80% of expected progress
    const AT_RISK_PERCENTAGE = 0.5;  // 50% of expected progress
    const EXCELLENT_PERCENTAGE = 1.0; // 100% or more of expected

    /**
     * Calculate progress velocity untuk satu santri
     * Velocity = average verses per day/week/month
     */
    public function calculateVelocity(int $santriId, string $period = 'week'): array
    {
        $santri = Santri::with('hafalan')->findOrFail($santriId);
        $hafalanData = $santri->hafalan()
            ->whereNotNull('ayat_sampai')
            ->orderBy('tanggal_setoran')
            ->get();

        if ($hafalanData->isEmpty()) {
            return [
                'santri_id' => $santriId,
                'period' => $period,
                'total_verses' => 0,
                'average_per_period' => 0,
                'total_days' => 0,
                'start_date' => null,
                'end_date' => null,
            ];
        }

        // Calculate total verses
        $totalVerses = $hafalanData->sum(function ($item) {
            return ($item->ayat_sampai - $item->ayat_dari) + 1;
        });

        // Calculate date range
        $startDate = $hafalanData->first()->tanggal_setoran;
        $endDate = $hafalanData->last()->tanggal_setoran;
        $totalDays = $startDate->diffInDays($endDate) + 1;

        // Calculate average based on period
        $average = match ($period) {
            'day' => round($totalVerses / $totalDays, 2),
            'week' => round(($totalVerses / $totalDays) * 7, 2),
            'month' => round(($totalVerses / $totalDays) * 30, 2),
            default => 0,
        };

        return [
            'santri_id' => $santriId,
            'period' => $period,
            'total_verses' => $totalVerses,
            'average_per_period' => $average,
            'total_days' => $totalDays,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'data_points' => $hafalanData->count(),
        ];
    }

    /**
     * Predict kapan hafalan akan selesai
     */
    public function predictCompletion(int $santriId): array
    {
        $santri = Santri::with('hafalan')->findOrFail($santriId);
        
        // Get velocity
        $velocity = $this->calculateVelocity($santriId, 'day');
        
        if ($velocity['total_verses'] === 0 || $velocity['average_per_period'] === 0) {
            return [
                'santri_id' => $santriId,
                'total_verses_completed' => 0,
                'verses_remaining' => self::TOTAL_VERSES,
                'predicted_completion_date' => null,
                'days_remaining' => null,
                'confidence' => 0,
                'message' => 'Data tidak cukup untuk prediksi',
            ];
        }

        $versesCompleted = $velocity['total_verses'];
        $versesRemaining = max(0, self::TOTAL_VERSES - $versesCompleted);
        $daysPerVerse = $velocity['total_days'] / $versesCompleted;
        $daysRemaining = (int)ceil($versesRemaining * $daysPerVerse);
        
        // Calculate confidence based on data consistency
        $hafalanData = $santri->hafalan()
            ->whereNotNull('tanggal_setoran')
            ->orderBy('tanggal_setoran')
            ->get();
        
        $consistency = $this->calculateConsistency($hafalanData);
        
        $predictedDate = Carbon::now()->addDays($daysRemaining);
        
        // Validate prediction is reasonable (between 1 month and 5 years)
        if ($daysRemaining < 30 || $daysRemaining > 1825) {
            $confidence = 20;
            $message = 'Prediksi tidak reliabel karena data tidak konsisten';
        } else {
            $confidence = min(95, (int)($consistency * 100));
            $message = 'Prediksi berdasarkan pola hafalan saat ini';
        }

        return [
            'santri_id' => $santriId,
            'total_verses_completed' => $versesCompleted,
            'verses_remaining' => $versesRemaining,
            'completion_percentage' => round(($versesCompleted / self::TOTAL_VERSES) * 100, 2),
            'average_verses_per_day' => round($velocity['average_per_period'], 2),
            'predicted_completion_date' => $predictedDate->toDateString(),
            'days_remaining' => $daysRemaining,
            'weeks_remaining' => ceil($daysRemaining / 7),
            'months_remaining' => ceil($daysRemaining / 30),
            'confidence' => $confidence,
            'consistency_score' => round($consistency, 2),
            'message' => $message,
        ];
    }

    /**
     * Calculate consistency score (0-1)
     * Mengukur seberapa konsisten santri dalam hafalan
     */
    private function calculateConsistency(Collection $hafalanData): float
    {
        if ($hafalanData->count() < 2) {
            return 0;
        }

        $intervals = [];
        $dates = $hafalanData->pluck('tanggal_setoran')->values();
        
        for ($i = 1; $i < count($dates); $i++) {
            $intervals[] = $dates[$i]->diffInDays($dates[$i - 1]);
        }

        if (empty($intervals)) {
            return 0;
        }

        // Calculate standard deviation
        $mean = array_sum($intervals) / count($intervals);
        $squareDiffs = array_map(function ($x) use ($mean) {
            return pow($x - $mean, 2);
        }, $intervals);
        
        $stdDev = sqrt(array_sum($squareDiffs) / count($squareDiffs));
        $cv = $stdDev / $mean; // Coefficient of variation
        
        // Convert to consistency score (lower CV = higher consistency)
        return max(0, 1 - ($cv / 2)); // Cap at 1
    }

    /**
     * Identify santri yang struggling (progress lambat)
     */
    public function identifyStrugglingStudents(string $ustadzId = null): array
    {
        $query = Santri::with('hafalan');
        
        if ($ustadzId) {
            $query->whereHas('hafalan', function ($q) use ($ustadzId) {
                $q->where('ustadz_id', $ustadzId);
            });
        }

        $santris = $query->get();
        $struggling = [];
        $onTrack = [];
        $excellent = [];

        foreach ($santris as $santri) {
            $prediction = $this->predictCompletion($santri->id);
            $status = $this->determineStatus($prediction);
            
            $analysis = [
                'santri_id' => $santri->id,
                'nama' => $santri->nama,
                'verses_completed' => $prediction['total_verses_completed'],
                'completion_percentage' => $prediction['completion_percentage'],
                'predicted_date' => $prediction['predicted_completion_date'],
                'days_remaining' => $prediction['days_remaining'],
                'confidence' => $prediction['confidence'],
                'status' => $status,
            ];

            match ($status) {
                'at_risk' => $struggling[] = $analysis,
                'on_track' => $onTrack[] = $analysis,
                'excellent' => $excellent[] = $analysis,
            };
        }

        return [
            'at_risk' => $struggling,
            'on_track' => $onTrack,
            'excellent' => $excellent,
            'summary' => [
                'total_students' => count($santris),
                'at_risk_count' => count($struggling),
                'on_track_count' => count($onTrack),
                'excellent_count' => count($excellent),
            ],
        ];
    }

    /**
     * Determine status santri berdasarkan prediction
     */
    private function determineStatus(array $prediction): string
    {
        $completion = $prediction['completion_percentage'];
        $confidence = $prediction['confidence'];

        if ($completion >= 100) {
            return 'excellent';
        }

        // Expected progress dari hari ini
        if ($prediction['days_remaining'] === null) {
            return 'at_risk';
        }

        // Heuristic: jika sudah 1+ tahun tapi progress < 30%, at risk
        if ($prediction['days_remaining'] > 365 && $completion < 30) {
            return 'at_risk';
        }

        if ($completion >= (self::EXCELLENT_PERCENTAGE * 100)) {
            return 'excellent';
        } elseif ($completion >= (self::ON_TRACK_PERCENTAGE * 100)) {
            return 'on_track';
        } else {
            return 'at_risk';
        }
    }

    /**
     * Get analytics untuk class/ustadz tertentu
     */
    public function getClassInsights(int $ustadzId): array
    {
        $ustadz = User::findOrFail($ustadzId);
        
        $hafalanRecords = Hafalan::where('ustadz_id', $ustadzId)
            ->with('santri')
            ->get();

        if ($hafalanRecords->isEmpty()) {
            return [
                'ustadz_id' => $ustadzId,
                'ustadz_name' => $ustadz->name,
                'message' => 'Belum ada data hafalan',
            ];
        }

        // Calculate class statistics
        $totalVerses = $hafalanRecords->sum(function ($item) {
            return ($item->ayat_sampai - $item->ayat_dari) + 1;
        });

        $totalRecords = $hafalanRecords->count();
        $completedCount = $hafalanRecords->whereIn('status', ['selesai'])->count();
        $averageVersesPerRecord = round($totalVerses / $totalRecords, 2);

        // Get predictions for all students
        $studentPredictions = [];
        $santrisId = $hafalanRecords->pluck('santri_id')->unique();
        
        foreach ($santrisId as $id) {
            $studentPredictions[] = $this->predictCompletion($id);
        }

        // Calculate class metrics
        $avgCompletion = collect($studentPredictions)->avg('completion_percentage');
        $medianDaysRemaining = $this->getMedian(
            collect($studentPredictions)->pluck('days_remaining')->filter()->toArray()
        );

        // Identify struggling students
        $struggling = collect($studentPredictions)
            ->where('completion_percentage', '<', self::AT_RISK_PERCENTAGE * 100)
            ->count();

        return [
            'ustadz_id' => $ustadzId,
            'ustadz_name' => $ustadz->name,
            'total_students' => $santrisId->count(),
            'total_verses_recorded' => $totalVerses,
            'total_records' => $totalRecords,
            'average_verses_per_record' => $averageVersesPerRecord,
            'completion_stats' => [
                'completed' => $completedCount,
                'in_progress' => $totalRecords - $completedCount,
            ],
            'class_metrics' => [
                'average_completion_percentage' => round($avgCompletion, 2),
                'median_days_remaining' => $medianDaysRemaining,
                'students_at_risk' => $struggling,
                'students_on_track' => $santrisId->count() - $struggling,
            ],
        ];
    }

    /**
     * Generate comprehensive report untuk satu santri
     */
    public function generateStudentReport(int $santriId): array
    {
        $santri = Santri::with('hafalan', 'orangTua')->findOrFail($santriId);
        
        $velocity = $this->calculateVelocity($santriId);
        $prediction = $this->predictCompletion($santriId);
        $status = $this->determineStatus($prediction);

        // Get recent hafalans
        $recentHafalans = $santri->hafalan()
            ->latest('tanggal_setoran')
            ->limit(5)
            ->get();

        // Calculate trend (last 2 weeks vs before)
        $twoWeeksAgo = Carbon::now()->subWeeks(2);
        $recent = $santri->hafalan()
            ->where('tanggal_setoran', '>=', $twoWeeksAgo)
            ->sum(DB::raw('ayat_sampai - ayat_dari + 1'));
        
        $trend = $recent > $velocity['average_per_period'] * 14 ? 'increasing' : 'stable';

        return [
            'santri' => [
                'id' => $santri->id,
                'nama' => $santri->nama,
                'nis' => $santri->nis,
            ],
            'progress' => [
                'total_verses' => $prediction['total_verses_completed'],
                'completion_percentage' => $prediction['completion_percentage'],
                'status' => $status,
            ],
            'velocity' => $velocity,
            'prediction' => $prediction,
            'trend' => $trend,
            'recent_activity' => $recentHafalans->map(function ($h) {
                return [
                    'juz' => $h->juz,
                    'surat' => $h->surat,
                    'verses' => ($h->ayat_sampai - $h->ayat_dari) + 1,
                    'date' => $h->tanggal_setoran->toDateString(),
                ];
            }),
            'recommendations' => $this->generateRecommendations($prediction, $status, $trend),
        ];
    }

    /**
     * Generate recommendations berdasarkan analytics
     */
    private function generateRecommendations(array $prediction, string $status, string $trend): array
    {
        $recommendations = [];

        if ($status === 'at_risk') {
            $recommendations[] = 'Santri memiliki progress lambat. Pertimbangkan untuk memberikan motivasi atau bantuan tambahan.';
            $recommendations[] = 'Review metode pembelajaran dengan santri dan cari tahu hambatan yang dihadapi.';
        }

        if ($prediction['days_remaining'] !== null && $prediction['days_remaining'] > 730) {
            $recommendations[] = 'Estimasi completion membutuhkan waktu lebih dari 2 tahun. Tingkatkan frekuensi setoran hafalan.';
        }

        if ($trend === 'stable') {
            $recommendations[] = 'Pertahankan konsistensi yang sudah bagus dalam hafalan.';
        }

        if ($status === 'excellent') {
            $recommendations[] = 'Santri memiliki progress yang sangat baik! Pertahankan momentum ini.';
        }

        if ($prediction['confidence'] < 50) {
            $recommendations[] = 'Data masih kurang untuk memberikan prediksi akurat. Terus monitor perkembangan santri.';
        }

        return $recommendations;
    }

    /**
     * Helper: calculate median
     */
    private function getMedian(array $values): ?int
    {
        if (empty($values)) {
            return null;
        }

        sort($values);
        $count = count($values);
        $mid = (int)($count / 2);

        return $count % 2 === 0 
            ? (int)(($values[$mid - 1] + $values[$mid]) / 2)
            : $values[$mid];
    }

    /**
     * Get all analytics metrics
     */
    public static function getMetricsDefinition(): array
    {
        return [
            'velocity' => 'Average hafalan verses per time period',
            'completion_prediction' => 'Estimated date when hafalan will be completed',
            'completion_percentage' => 'Percentage of total Al-Qur\'an verses memorized',
            'confidence_score' => 'Confidence level of the prediction (0-100)',
            'consistency_score' => 'Consistency of learning pattern (0-1)',
            'status' => 'Current status: at_risk, on_track, or excellent',
            'trend' => 'Learning trend: increasing or stable',
        ];
    }
}
