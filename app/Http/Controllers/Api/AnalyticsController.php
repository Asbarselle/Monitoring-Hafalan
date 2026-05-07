<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hafalan;
use App\Models\Santri;
use App\Models\User;
use App\Services\HafalanAnalyticsService;
use App\Services\AudioAnalysisService;
use Illuminate\Http\Request;

/**
 * Analytics API Controller
 * Menyediakan endpoint untuk analytics dan komputasi data hafalan
 */
class AnalyticsController extends Controller
{
    protected $analyticsService;
    protected $audioService;

    public function __construct(
        HafalanAnalyticsService $analyticsService,
        AudioAnalysisService $audioService
    ) {
        $this->analyticsService = $analyticsService;
        $this->audioService = $audioService;
        $this->middleware(['auth', 'verified']);
    }

    /**
     * GET /api/analytics/santri/{id}/progress
     * Get progress analytics untuk satu santri
     */
    public function getSantriProgress($santriId)
    {
        try {
            $santri = Santri::findOrFail($santriId);
            
            // Check authorization
            if (auth()->user()->role !== 'admin' && auth()->user()->id !== $santri->orang_tua_id) {
                return response()->json([
                    'error' => 'Unauthorized',
                ], 403);
            }

            $velocity = $this->analyticsService->calculateVelocity($santriId);
            
            return response()->json([
                'success' => true,
                'data' => $velocity,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * GET /api/analytics/santri/{id}/prediction
     * Get completion prediction untuk satu santri
     */
    public function getSantriPrediction($santriId)
    {
        try {
            $santri = Santri::findOrFail($santriId);
            
            // Check authorization
            if (auth()->user()->role !== 'admin' && auth()->user()->id !== $santri->orang_tua_id) {
                return response()->json([
                    'error' => 'Unauthorized',
                ], 403);
            }

            $prediction = $this->analyticsService->predictCompletion($santriId);
            
            return response()->json([
                'success' => true,
                'data' => $prediction,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * GET /api/analytics/santri/{id}/report
     * Get comprehensive report untuk satu santri
     */
    public function getSantriReport($santriId)
    {
        try {
            $santri = Santri::findOrFail($santriId);
            
            // Check authorization
            if (auth()->user()->role !== 'admin' && auth()->user()->id !== $santri->orang_tua_id) {
                return response()->json([
                    'error' => 'Unauthorized',
                ], 403);
            }

            $report = $this->analyticsService->generateStudentReport($santriId);
            
            return response()->json([
                'success' => true,
                'data' => $report,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * GET /api/analytics/class/{ustadzId}/insights
     * Get class analytics untuk satu ustadz
     */
    public function getClassInsights($ustadzId)
    {
        try {
            $ustadz = User::findOrFail($ustadzId);
            
            // Check authorization (hanya admin atau ustadz sendiri)
            if (auth()->user()->role !== 'admin' && auth()->user()->id !== $ustadzId) {
                return response()->json([
                    'error' => 'Unauthorized',
                ], 403);
            }

            $insights = $this->analyticsService->getClassInsights($ustadzId);
            
            return response()->json([
                'success' => true,
                'data' => $insights,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * GET /api/analytics/struggling-students
     * Get list of struggling students (admin only)
     */
    public function getStrugglingStudents(Request $request)
    {
        $this->authorize('admin');

        try {
            $ustadzId = $request->query('ustadz_id');
            $result = $this->analyticsService->identifyStrugglingStudents($ustadzId);
            
            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * GET /api/analytics/audio/{hafalanId}/quality
     * Get audio quality metrics untuk satu hafalan
     */
    public function getAudioQuality($hafalanId)
    {
        try {
            $hafalan = Hafalan::findOrFail($hafalanId);
            
            // Check authorization
            if (auth()->user()->role !== 'admin' && 
                auth()->user()->id !== $hafalan->santri->orang_tua_id &&
                auth()->user()->id !== $hafalan->ustadz_id) {
                return response()->json([
                    'error' => 'Unauthorized',
                ], 403);
            }

            if (!$hafalan->is_audio_analyzed) {
                return response()->json([
                    'error' => 'Audio belum dianalisis',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'hafalan_id' => $hafalan->id,
                    'quality_score' => $hafalan->audio_quality_score,
                    'quality_level' => $hafalan->audio_quality_level,
                    'bitrate' => $hafalan->audio_bitrate,
                    'codec' => $hafalan->audio_codec,
                    'duration' => $hafalan->audio_duration,
                    'analyzed_at' => $hafalan->last_analyzed_at,
                    'insights' => $hafalan->analytics_insights,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * POST /api/analytics/process-audios
     * Trigger batch audio analysis (admin only)
     */
    public function processAudios(Request $request)
    {
        $this->authorize('admin');

        try {
            $limit = $request->input('limit', 10);
            $hafalans = Hafalan::where('is_audio_analyzed', false)
                ->where('s3_audio_path', '!=', null)
                ->limit($limit)
                ->get();

            $processed = 0;
            $errors = [];

            foreach ($hafalans as $hafalan) {
                $result = $this->audioService->analyzeAudio($hafalan->s3_audio_path);
                
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
                    $errors[] = [
                        'hafalan_id' => $hafalan->id,
                        'error' => $result['error'],
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'processed' => $processed,
                'total' => $hafalans->count(),
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * GET /api/analytics/metrics-definition
     * Get definition of all analytics metrics
     */
    public function getMetricsDefinition()
    {
        return response()->json([
            'success' => true,
            'data' => HafalanAnalyticsService::getMetricsDefinition(),
        ]);
    }

    /**
     * Authorize admin role
     */
    private function authorize($role)
    {
        if (auth()->user()->role !== $role) {
            abort(403, 'Unauthorized');
        }
    }
}
