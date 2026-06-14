<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Hafalan {{ $santri->nama }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', serif; font-size: 12px; color: #333; line-height: 1.5; }
        .page { page-break-after: always; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #1a472a; padding-bottom: 15px; }
        .header-title { font-size: 18px; font-weight: bold; color: #1a472a; margin-bottom: 8px; }
        .header-subtitle { font-size: 14px; color: #666; margin-bottom: 4px; }
        .header-date { font-size: 11px; color: #999; }
        .header-logo { margin: 0 auto 12px; width: 60px; height: 60px; }
        .header-logo img { width: 100%; height: 100%; object-fit: contain; }
        .santri-info { padding: 0; margin-bottom: 20px; }
        .profile-card { display: table; width: 100%; table-layout: fixed; border-radius: 18px; overflow: hidden; border: 1px solid #d8e4d5; background-color: #ffffff; }
        .profile-left { display: table-cell; width: 240px; vertical-align: middle; background-color: #1d4f2d; color: #ffffff; padding: 22px 16px; text-align: center; }
        .profile-photo { width: 170px; height: 170px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(255, 255, 255, 0.9); margin: 0 auto 14px; background-color: #ffffff; }
        .profile-name { font-size: 15px; font-weight: 700; margin-top: 8px; }
        .profile-nis { font-size: 11px; opacity: 0.78; margin-top: 4px; }
        .profile-role { display: inline-block; margin-top: 18px; padding: 8px 16px; border: 1px solid rgba(255,255,255,0.24); border-radius: 999px; font-size: 10px; letter-spacing: 0.12em; text-transform: uppercase; }
        .profile-right { display: table-cell; vertical-align: middle; padding: 24px 24px; background-color: #f6faf6; }
        .profile-heading { font-size: 16px; font-weight: 700; color: #1a472a; margin-bottom: 10px; }
        .profile-heading-line { width: 110px; height: 3px; background-color: #9fc6a7; margin-bottom: 18px; border-radius: 2px; }
        .profile-field { display: table; width: 100%; margin-bottom: 12px; }
        .profile-field-label { display: table-cell; width: 190px; vertical-align: top; font-size: 11px; font-weight: 700; color: #2f563d; padding-right: 8px; }
        .profile-field-colon { display: table-cell; width: 10px; vertical-align: top; font-size: 11px; color: #2f563d; padding-right: 8px; }
        .profile-field-value { display: table-cell; vertical-align: top; font-size: 12px; color: #26392f; line-height: 1.5; }
        .summary-box { display: flex; gap: 20px; margin-bottom: 20px; }
        .summary-item { flex: 1; background-color: #f0f8ff; padding: 10px; border-radius: 4px; text-align: center; }
        .summary-item-title { font-size: 11px; color: #666; text-transform: uppercase; }
        .summary-item-value { font-size: 18px; font-weight: bold; color: #1a472a; }
        .section-title { font-size: 14px; font-weight: bold; color: #1a472a; margin-top: 20px; margin-bottom: 10px; padding-bottom: 5px; border-bottom: 2px solid #1a472a; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background-color: #1a472a; color: white; padding: 8px; text-align: left; font-size: 11px; font-weight: bold; }
        td { padding: 8px; border-bottom: 1px solid #ddd; font-size: 11px; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .status-selesai { background-color: #d4edda; color: #155724; padding: 3px 6px; border-radius: 3px; font-weight: bold; }
        .status-sedang { background-color: #fff3cd; color: #856404; padding: 3px 6px; border-radius: 3px; font-weight: bold; }
        .status-mengulang { background-color: #f8d7da; color: #721c24; padding: 3px 6px; border-radius: 3px; font-weight: bold; }
        .progress-bar-container { width: 100%; height: 20px; background-color: #e9ecef; border-radius: 4px; overflow: hidden; margin: 10px 0; }
        .progress-bar { height: 100%; background-color: #28a745; display: flex; align-items: center; justify-content: center; color: white; font-size: 10px; font-weight: bold; }
        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; text-align: center; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            @php
            $canRenderImages = extension_loaded('gd');
            $logoPath = public_path('images/logo-resmi.png');
            $logoDataUri = null;
            if ($canRenderImages && file_exists($logoPath)) {
                $extension = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
                $mimeType = $extension === 'jpg' ? 'jpeg' : $extension;
                $logoDataUri = 'data:image/' . $mimeType . ';base64,' . base64_encode(file_get_contents($logoPath));
            }
        @endphp
        <div class="header-logo">
            @if($logoDataUri)
                <img src="{{ $logoDataUri }}" alt="Logo Pesantren">
            @else
                <div style="width: 100%; height: 100%; display:flex; align-items:center; justify-content:center; font-size: 12px; color:#1a472a;">
                    Logo
                </div>
            @endif
        </div>
        <div class="header-title">LAPORAN HAFALAN AL-QUR'AN</div>
        <div class="header-subtitle">Sistem Monitoring Pondok Pesantren Hilyatul Irsyad</div>
        <div class="header-date">Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
        </div>

        @php
            $fotoPath = $santri->foto ? public_path('storage/' . $santri->foto) : null;
            $fotoDataUri = null;
            if ($canRenderImages && $fotoPath && file_exists($fotoPath)) {
                $extension = strtolower(pathinfo($fotoPath, PATHINFO_EXTENSION));
                $mimeType = $extension === 'jpg' ? 'jpeg' : $extension;
                $fotoDataUri = 'data:image/' . $mimeType . ';base64,' . base64_encode(file_get_contents($fotoPath));
            }
        @endphp
        <div class="santri-info">
            <div class="profile-card">
                <div class="profile-left">
                    @if($fotoDataUri)
                        <img src="{{ $fotoDataUri }}" alt="{{ $santri->nama }}" class="profile-photo">
                    @else
                        <div class="profile-photo" style="display:flex; align-items:center; justify-content:center; background:#ffffff;">
                            <span style="font-size: 42px; color: #6c757d;">👤</span>
                        </div>
                    @endif
                    <div class="profile-name">{{ $santri->nama }}</div>
                    <div class="profile-nis">{{ $santri->nis ?? '-' }}</div>
                    <div class="profile-role">{{ $santri->jenis_kelamin === 'L' ? 'SANTRI' : 'SANTRIWATI' }}</div>
                </div>
                <div class="profile-right">
                    <div class="profile-heading">BIODATA SANTRI</div>
                    <div class="profile-heading-line"></div>
                    <div class="profile-field">
                        <div class="profile-field-label">Nama Lengkap</div>
                        <div class="profile-field-colon">:</div>
                        <div class="profile-field-value">{{ $santri->nama }}</div>
                    </div>
                    <div class="profile-field">
                        <div class="profile-field-label">NIS</div>
                        <div class="profile-field-colon">:</div>
                        <div class="profile-field-value">{{ $santri->nis ?? '-' }}</div>
                    </div>
                    <div class="profile-field">
                        <div class="profile-field-label">Jenis Kelamin</div>
                        <div class="profile-field-colon">:</div>
                        <div class="profile-field-value">{{ $santri->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                    </div>
                    <div class="profile-field">
                        <div class="profile-field-label">Tempat / Tgl Lahir</div>
                        <div class="profile-field-colon">:</div>
                        <div class="profile-field-value">{{ $santri->tempat_lahir ?? '-' }} / {{ $santri->tanggal_lahir ? $santri->tanggal_lahir->format('d/m/Y') : '-' }}</div>
                    </div>
                    <div class="profile-field">
                        <div class="profile-field-label">Alamat</div>
                        <div class="profile-field-colon">:</div>
                        <div class="profile-field-value">{{ $santri->alamat ?? '-' }}</div>
                    </div>
                    <div class="profile-field">
                        <div class="profile-field-label">No. HP / WA</div>
                        <div class="profile-field-colon">:</div>
                        <div class="profile-field-value">{{ $santri->no_hp ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="summary-box">
            <div class="summary-item">
                <div class="summary-item-title">Total Setoran</div>
                <div class="summary-item-value">{{ $totalSetoran }}</div>
            </div>
        </div>

        <div class="section-title">Riwayat Hafalan</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th style="width: 35px;">Juz</th>
                    <th style="width: 80px;">Surat</th>
                    <th style="width: 70px;">Ayat</th>
                    <th style="width: 60px;">Status</th>
                    <th style="width: 90px;">Ustadz</th>
                    <th style="width: 70px;">Tgl Setoran</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hafalans as $h)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $h->juz ?? '-' }}</td>
                        <td>{{ $h->surat ?? '-' }}</td>
                        <td>{{ $h->ayat_dari && $h->ayat_sampai ? $h->ayat_dari . '-' . $h->ayat_sampai : '-' }}</td>
                        <td>
                            @if($h->status === 'selesai')
                                <span class="status-selesai">Selesai</span>
                            @elseif($h->status === 'sedang')
                                <span class="status-sedang">Sedang</span>
                            @elseif($h->status === 'mengulang')
                                <span class="status-mengulang">Mengulang</span>
                            @else
                                <span>{{ ucfirst($h->status) }}</span>
                            @endif
                        </td>
                        <td>{{ $h->ustadz ? $h->ustadz->name : '-' }}</td>
                        <td>{{ $h->tanggal_setoran ? $h->tanggal_setoran->format('d/m/Y') : '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align: center; color: #999;">Belum ada riwayat hafalan</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <p>Laporan ini dicetak oleh Admin Sistem Monitoring Hafalan - Pondok Pesantren Hilyatul Irsyad</p>
            <p style="margin-top: 10px; font-size: 9px; color: #bbb;">Generated automatically | {{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</body>
</html>