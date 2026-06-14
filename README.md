# SIMHAFAL - Sistem Informasi Monitoring Hafalan Al-Qur'an

Sistem informasi monitoring hafalan Al-Qur'an untuk Pondok Pesantren Hilyatul Irsyad yang dibangun dengan Laravel dan SQLite.

## Fitur Utama

### Halaman Publik
- **Beranda**: Menampilkan informasi pondok, foto, dan navigasi
- **Tentang Kami**: Informasi tentang pondok pesantren
- **Info**: Informasi pendaftaran dan FAQ

### Sistem Autentikasi & RBAC
- Login dengan Role-Based Access Control (RBAC)
- Tiga role pengguna:
  - **Admin**: Akses penuh ke seluruh sistem
  - **Ustadz**: Mengelola hafalan santri
  - **Orang Tua**: Melihat perkembangan hafalan anak

### Dashboard Admin
- Kelola User (tambah, edit, hapus)
- Kelola Santri (tambah, edit, hapus, upload foto)
- Lihat Data Hafalan
- Statistik lengkap sistem

### Dashboard Ustadz
- Daftar semua santri
- Tambah hafalan baru
- Edit hafalan santri
- Update status hafalan (belum, sedang, selesai, mengulang)
- Catatan dan nilai hafalan

### Dashboard Orang Tua
- Lihat perkembangan hafalan anak
- Statistik progres hafalan (juz, surat, ayat)
- Progress bar visualisasi
- Detail lengkap setiap hafalan

## Audio Recording & Analysis

- Rekam audio hafalan dan simpan ke local storage
- Putar dan unduh rekaman dari dashboard orang tua
- Analisis metadata audio lokal untuk kebutuhan evaluasi kualitas
- Ekspor PDF data hafalan untuk laporan

## Instalasi

1. Clone repository atau extract file
2. Install dependencies:
   ```bash
   cd backend
   composer install
   npm install
   ```
3. Jalankan build aset:
   ```bash
   npm run build
   ```
4. Jalankan development server backend:
   ```bash
   php artisan serve
   ```

> Atau di Windows, jalankan `start-backend.bat` dari direktori root proyek untuk membuka aplikasi secara otomatis di `http://127.0.0.1:8000`.

3. Setup environment (jika belum ada .env):
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Pastikan database sudah dikonfigurasi di `.env`

5. Jalankan migrations:
   ```bash
   php artisan migrate --force
   ```

6. Buat symlink untuk storage:
   ```bash
   php artisan storage:link
   ```

7. Jalankan server development:
   ```bash
   php artisan serve
   ```

8. Akses aplikasi di `http://localhost:8000`

### Audio Analysis (Opsional)

Untuk menganalisis audio yang sudah tersimpan:

```bash
php artisan hafalan:analyze-audios --limit=20
```


## Kredensial Login

### Admin
- Email: `admin@hilyatulirsyad.ac.id`
- Password: `password`

### Ustadz
- Email: `ustadz@hilyatulirsyad.ac.id`
- Password: `password`

### Orang Tua
- Email: `orangtua@hilyatulirsyad.ac.id`
- Password: `password`

## Struktur Database

### Tabel Users
- id, name, email, password, role (admin/ustadz/orang_tua), timestamps

### Tabel Santri
- id, nama, nis, tempat_lahir, tanggal_lahir, jenis_kelamin, alamat, no_hp, foto, orang_tua_id, timestamps

### Tabel Hafalan
- id, santri_id, ustadz_id, juz, surat, ayat_dari, ayat_sampai, status, catatan, tanggal_setoran, nilai, audio_path, audio_filename, audio_duration, audio_quality_score, audio_bitrate, audio_codec, is_audio_analyzed, progress_percentage, days_to_completion, completion_status, analytics_insights, last_analyzed_at, timestamps

## Teknologi yang Digunakan

- Laravel 12
- SQLite
- Bootstrap 5
- Bootstrap Icons
- PHP 8.2+

## Fitur Tambahan

- Responsive design untuk mobile dan desktop
- Progress bar visualisasi progres hafalan
- Upload foto santri
- Statistik dan grafik progres
- Pagination untuk data besar
- Validasi form yang lengkap
- Alert notifications

## Cara Menggunakan

1. **Sebagai Admin**:
   - Login dengan kredensial admin
   - Akses menu Kelola User untuk menambah user baru
   - Akses menu Kelola Santri untuk menambah santri
   - Lihat semua data hafalan di menu Data Hafalan

2. **Sebagai Ustadz**:
   - Login dengan kredensial ustadz
   - Lihat daftar santri di dashboard
   - Klik "Lihat" pada santri untuk melihat detail hafalan
   - Tambah hafalan baru dengan klik "Tambah Hafalan"
   - Edit atau hapus hafalan yang sudah ada

3. **Sebagai Orang Tua**:
   - Login dengan kredensial orang tua
   - Lihat dashboard yang menampilkan statistik hafalan anak
   - Klik "Lihat Detail" untuk melihat detail lengkap hafalan
   - Monitor progres hafalan dalam bentuk progress bar

## Catatan Penting

- Pastikan folder `storage/app/public` memiliki permission yang sesuai untuk upload file
- Database SQLite akan dibuat otomatis saat menjalankan migrations
- Semua password default adalah `password` - ubah setelah instalasi untuk keamanan

## Lisensi

Sistem ini dibuat untuk Pondok Pesantren Hilyatul Irsyad.
