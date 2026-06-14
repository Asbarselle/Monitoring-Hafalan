@extends('emails.layout')

@section('content')
<div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="text-align: center; border-bottom: 3px solid #4CAF50; padding-bottom: 20px; margin-bottom: 20px;">
            <h1 style="color: #2c3e50; margin: 0;">Assalamu'alaikum Wa Rahmatullahi Wa Barakatuh</h1>
            <p style="color: #7f8c8d; margin: 10px 0 0 0;">Update Hafalan Al-Qur'an Anak Anda</p>
        </div>

        <!-- Main Content -->
        <div style="color: #2c3e50;">
            <p style="font-size: 16px; line-height: 1.6;">
                Assalamu'alaikum {{ $notification->parent->name }},
            </p>

            <p style="font-size: 16px; line-height: 1.6;">
                Anak Anda, <strong>{{ $santri->nama }}</strong>, telah menyelesaikan setoran hafalan Al-Qur'an dengan informasi berikut:
            </p>

            <!-- Hafalan Details Card -->
            <div style="background-color: #ecf0f1; padding: 20px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #4CAF50;">
                <table style="width: 100%; color: #2c3e50;">
                    <tr>
                        <td style="padding: 8px 0;"><strong>📖 Surah/Juz:</strong></td>
                        <td style="padding: 8px 0; text-align: right;">{{ $hafalan->surat ?? 'Juz ' . $hafalan->juz }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #bdc3c7;">
                        <td style="padding: 8px 0;"><strong>📝 Ayat:</strong></td>
                        <td style="padding: 8px 0; text-align: right;">
                            @if($hafalan->ayat_dari)
                                {{ $hafalan->ayat_dari }} - {{ $hafalan->ayat_sampai ?? 'selesai' }}
                            @else
                                Satu Juz Penuh
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0;"><strong>✅ Status:</strong></td>
                        <td style="padding: 8px 0; text-align: right;">
                            <span style="background-color: #4CAF50; color: white; padding: 3px 8px; border-radius: 3px; text-transform: capitalize;">
                                {{ $hafalan->status }}
                            </span>
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #bdc3c7;">
                        <td style="padding: 8px 0;"><strong>⭐ Nilai:</strong></td>
                        <td style="padding: 8px 0; text-align: right;">{{ $hafalan->nilai ?? '-' }}/100</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0;"><strong>📅 Tanggal Setoran:</strong></td>
                        <td style="padding: 8px 0; text-align: right;">{{ optional($hafalan->tanggal_setoran)->format('d-m-Y') ?? date('d-m-Y') }}</td>
                    </tr>
                    @if($hafalan->catatan)
                    <tr>
                        <td style="padding: 8px 0;"><strong>📝 Catatan:</strong></td>
                        <td style="padding: 8px 0; text-align: right;">{{ $hafalan->catatan }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            <!-- Audio Recording -->
            @if($hafalan->audio_path)
            <div style="background-color: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0; text-align: center;">
                <p style="margin: 0 0 10px 0;"><strong>🎵 Rekaman Hafalan Tersedia</strong></p>
                <p style="font-size: 14px; color: #1976d2; margin: 0;">
                    Klik tombol di bawah untuk mendengarkan bacaan hafalan {{ $santri->nama }}:
                </p>
                <div style="margin-top: 15px;">
                    <a href="{{ route('parent.hafalan.listen', $hafalan->id) }}" style="background-color: #2196F3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">
                        🎧 Dengarkan Rekaman
                    </a>
                </div>
            </div>
            @endif

            <!-- Teacher Info -->
            @if($ustadz)
            <div style="background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <p style="margin: 0 0 8px 0;"><strong>📚 Ustadz/Guru Pembimbing:</strong></p>
                <p style="margin: 0; color: #4CAF50;">{{ $ustadz->name }}</p>
            </div>
            @endif

            <!-- Call to Action -->
            <div style="background-color: #fff3e0; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #ff9800;">
                <p style="margin: 0; color: #e65100;">
                    <strong>💡 Tips:</strong> Silakan login ke dashboard orang tua Anda untuk melihat detail lengkap progress hafalan anak Anda.
                </p>
            </div>

            <a href="{{ route('login') }}" style="background-color: #4CAF50; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px; font-weight: bold;">
                Masuk ke Dashboard
            </a>

            <!-- Footer Message -->
            <p style="font-size: 14px; line-height: 1.6; margin-top: 30px; color: #7f8c8d; border-top: 1px solid #ecf0f1; padding-top: 20px;">
                Terima kasih atas dukungan Anda dalam mendampingi hafalan Al-Qur'an anak Anda. 
                Semoga Allah menerima hafalan dan amal ibadah semuanya. 
                <br><br>
                <em>Wassalamu'alaikum Wa Rahmatullahi Wa Barakatuh</em>
            </p>

            <!-- Sistem Information -->
            <p style="font-size: 12px; color: #95a5a6; text-align: center; margin-top: 20px;">
                Ini adalah email otomatis dari sistem SIMHAFAL (Sistem Informasi Manajemen Hafalan Al-Qur'an)
            </p>
        </div>
    </div>
</div>
@endsection
