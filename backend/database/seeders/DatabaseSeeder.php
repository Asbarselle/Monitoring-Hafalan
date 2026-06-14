<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Santri;
use App\Models\Hafalan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@hilyatulirsyad.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Ustadz
        $ustadz = User::create([
            'name' => 'Ustadz Ahmad',
            'email' => 'ustadz@hilyatulirsyad.ac.id',
            'password' => Hash::make('password'),
            'role' => 'ustadz',
        ]);

        // Create Orang Tua
        $orangTua = User::create([
            'name' => 'Bapak Santri',
            'email' => 'orangtua@hilyatulirsyad.ac.id',
            'password' => Hash::make('password'),
            'role' => 'orang_tua',
        ]);

        // Create Santri
        $santri1 = Santri::create([
            'nama' => 'Ahmad Fauzi',
            'nis' => 'S001',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '2010-05-15',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Pesantren No. 123',
            'no_hp' => '081234567890',
            'orang_tua_id' => $orangTua->id,
        ]);

        $santri2 = Santri::create([
            'nama' => 'Siti Aisyah',
            'nis' => 'S002',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2011-08-20',
            'jenis_kelamin' => 'P',
            'alamat' => 'Jl. Pesantren No. 456',
            'no_hp' => '081234567891',
            'orang_tua_id' => $orangTua->id,
        ]);

        // Create Hafalan
        Hafalan::create([
            'santri_id' => $santri1->id,
            'ustadz_id' => $ustadz->id,
            'juz' => 1,
            'surat' => 'Al-Fatihah',
            'ayat_dari' => '1',
            'ayat_sampai' => '7',
            'status' => 'selesai',
            'catatan' => 'Hafalan lancar dan baik',
            'tanggal_setoran' => now()->subDays(5),
            'nilai' => 90,
        ]);

        Hafalan::create([
            'santri_id' => $santri1->id,
            'ustadz_id' => $ustadz->id,
            'juz' => 1,
            'surat' => 'Al-Baqarah',
            'ayat_dari' => '1',
            'ayat_sampai' => '50',
            'status' => 'sedang',
            'catatan' => 'Perlu lebih banyak latihan',
            'tanggal_setoran' => now()->subDays(2),
            'nilai' => 75,
        ]);

        Hafalan::create([
            'santri_id' => $santri2->id,
            'ustadz_id' => $ustadz->id,
            'juz' => 30,
            'surat' => 'An-Nas',
            'ayat_dari' => '1',
            'ayat_sampai' => '6',
            'status' => 'selesai',
            'catatan' => 'Sangat baik',
            'tanggal_setoran' => now()->subDays(3),
            'nilai' => 95,
        ]);
    }
}
