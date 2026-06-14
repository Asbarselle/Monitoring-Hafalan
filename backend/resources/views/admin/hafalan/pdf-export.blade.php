<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Backup Data Hafalan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 11px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            color: #1a5490;
            font-size: 24px;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .summary {
            margin-bottom: 25px;
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
        }
        
        .summary h3 {
            margin: 0 0 10px 0;
            color: #1a5490;
            font-size: 14px;
        }
        
        .summary-row {
            display: inline-block;
            margin-right: 30px;
            margin-bottom: 8px;
        }
        
        .summary-row strong {
            color: #333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table thead {
            background-color: #1a5490;
            color: white;
        }
        
        table th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        
        table td {
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        table tbody tr:hover {
            background-color: #f0f0f0;
        }
        
        .status-selesai {
            background-color: #d4edda;
            color: #155724;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: bold;
            display: inline-block;
        }
        
        .status-proses {
            background-color: #fff3cd;
            color: #856404;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: bold;
            display: inline-block;
        }
        
        .status-batal {
            background-color: #f8d7da;
            color: #721c24;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: bold;
            display: inline-block;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #999;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .print-date {
            text-align: right;
            margin-bottom: 20px;
            color: #666;
            font-size: 10px;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📚 BACKUP DATA HAFALAN</h1>
        <p>SIMHAFAL - Sistem Informasi Manajemen Hafalan</p>
        <p class="print-date">Tanggal Cetak: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Ringkasan Statistik -->
    <div class="summary">
        <h3>📊 Ringkasan Statistik Hafalan</h3>
        
        <div class="summary-row">
            <strong>Total Hafalan:</strong> {{ $totalHafalan }}
        </div>
        
        <div class="summary-row">
            <strong>Hafalan Selesai:</strong> {{ $hafalanSelesai }}
        </div>
        
        <div class="summary-row">
            <strong>Hafalan Proses:</strong> {{ $statistikByStatus['proses'] ?? 0 }}
        </div>
        
        <div class="summary-row">
            <strong>Hafalan Batal:</strong> {{ $statistikByStatus['batal'] ?? 0 }}
        </div>
    </div>

    <!-- Detail Data Hafalan -->
    @if($hafalanData->count() > 0)
        <h3 style="color: #1a5490; margin-bottom: 15px;">📝 Detail Data Hafalan</h3>
        
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Santri</th>
                    <th>Juz</th>
                    <th>Surat</th>
                    <th>Ayat</th>
                    <th>Ustadz</th>
                    <th>Status</th>
                    <th>Nilai</th>
                    <th>Tanggal Setoran</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hafalanData as $key => $hafalan)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>
                            <strong>{{ $hafalan->santri->nama ?? '-' }}</strong>
                            @if($hafalan->santri)
                                <br><small style="color: #999;">{{ $hafalan->santri->no_induk ?? '-' }}</small>
                            @endif
                        </td>
                        <td style="text-align: center;">{{ $hafalan->juz }}</td>
                        <td>{{ $hafalan->surat }}</td>
                        <td style="text-align: center;">
                            @if($hafalan->ayat_dari == $hafalan->ayat_sampai)
                                {{ $hafalan->ayat_dari }}
                            @else
                                {{ $hafalan->ayat_dari }}-{{ $hafalan->ayat_sampai }}
                            @endif
                        </td>
                        <td>{{ $hafalan->ustadz->name ?? '-' }}</td>
                        <td>
                            @if($hafalan->status === 'selesai')
                                <span class="status-selesai">Selesai</span>
                            @elseif($hafalan->status === 'proses')
                                <span class="status-proses">Proses</span>
                            @elseif($hafalan->status === 'batal')
                                <span class="status-batal">Batal</span>
                            @else
                                <span style="background-color: #e2e3e5; padding: 3px 8px; border-radius: 3px;">{{ ucfirst($hafalan->status) }}</span>
                            @endif
                        </td>
                        <td style="text-align: center;">{{ $hafalan->nilai ?? '-' }}</td>
                        <td style="text-align: center;">{{ $hafalan->tanggal_setoran ? $hafalan->tanggal_setoran->format('d/m/Y') : '-' }}</td>
                        <td>{{ $hafalan->catatan ? substr($hafalan->catatan, 0, 50) . '...' : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 40px; background-color: #f5f5f5; border-radius: 5px;">
            <p style="color: #999; font-size: 14px;">Tidak ada data hafalan yang tersedia.</p>
        </div>
    @endif

    <div class="footer">
        <p>Dokumen ini dibuat secara otomatis oleh sistem SIMHAFAL. Pastikan untuk menyimpan backup ini di tempat yang aman.</p>
        <p>© {{ now()->year }} SIMHAFAL - All Rights Reserved</p>
    </div>
</body>
</html>
