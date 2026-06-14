<?php

namespace Tests\Feature;

use App\Models\HafalanNotification;
use App\Models\Hafalan;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HafalanCreationTest extends TestCase
{
    use RefreshDatabase;

    protected User $ustadz;
    protected User $parent;
    protected Santri $santri;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ustadz = User::factory()->create(['role' => 'ustadz']);
        $this->parent = User::factory()->create(['role' => 'orang_tua']);

        $this->santri = Santri::create([
            'nama' => 'Test Santri',
            'nis' => 'TEST001',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => now()->subYears(10)->format('Y-m-d'),
            'jenis_kelamin' => 'L',
            'alamat' => 'Alamat test',
            'no_hp' => '081234567890',
            'orang_tua_id' => $this->parent->id,
        ]);
    }

    public function test_ustadz_can_create_hafalan()
    {
        $this->actingAs($this->ustadz);

        $response = $this->post('/ustadz/hafalan', [
            'santri_id' => $this->santri->id,
            'surat' => 'Al-Fatihah',
            'ayat_dari' => '1',
            'ayat_sampai' => '7',
            'status' => 'sedang',
            'catatan' => 'Test hafalan creation',
            'tanggal_setoran' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('hafalan', [
            'santri_id' => $this->santri->id,
            'surat' => 'Al-Fatihah',
        ]);
    }

    public function test_hafalan_creation_triggers_notification_event()
    {
        \Event::fake();

        $this->actingAs($this->ustadz);

        $this->post('/ustadz/hafalan', [
            'santri_id' => $this->santri->id,
            'surat' => 'Al-Fatihah',
            'ayat_dari' => '1',
            'ayat_sampai' => '7',
            'status' => 'sedang',
        ]);

        \Event::assertDispatched(\App\Events\HafalanCreated::class);
    }

    public function test_hafalan_with_audio_is_stored_correctly()
    {
        Storage::fake('public');
        $this->actingAs($this->ustadz);

        $response = $this->post('/ustadz/hafalan', [
            'santri_id' => $this->santri->id,
            'surat' => 'Al-Fatihah',
            'ayat_dari' => '1',
            'ayat_sampai' => '7',
            'status' => 'sedang',
            'audio' => UploadedFile::fake()->create('audio.webm', 1000, 'audio/webm'),
        ]);

        $response->assertRedirect();

        $hafalan = Hafalan::latest()->first();

        $this->assertNotNull($hafalan);
        $this->assertNotNull($hafalan->audio_path);
        Storage::disk('public')->assertExists($hafalan->audio_path);
    }

    public function test_parent_can_see_notifications_page()
    {
        $this->actingAs($this->parent);

        $response = $this->get('/parent/notifications');

        $response->assertStatus(200);
    }

    public function test_parent_notifications_only_show_their_own_records()
    {
        $otherParent = User::factory()->create(['role' => 'orang_tua']);

        $otherSantri = Santri::create([
            'nama' => 'Other Santri',
            'nis' => 'TEST002',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => now()->subYears(11)->format('Y-m-d'),
            'jenis_kelamin' => 'P',
            'alamat' => 'Alamat other test',
            'no_hp' => '081234567891',
            'orang_tua_id' => $otherParent->id,
        ]);

        $myHafalan = Hafalan::create([
            'santri_id' => $this->santri->id,
            'ustadz_id' => $this->ustadz->id,
            'juz' => 1,
            'surat' => 'Al-Fatihah',
            'status' => 'sedang',
            'tanggal_setoran' => now()->toDateString(),
        ]);

        $otherHafalan = Hafalan::create([
            'santri_id' => $otherSantri->id,
            'ustadz_id' => $this->ustadz->id,
            'juz' => 2,
            'surat' => 'Al-Baqarah',
            'status' => 'sedang',
            'tanggal_setoran' => now()->toDateString(),
        ]);

        HafalanNotification::create([
            'hafalan_id' => $myHafalan->id,
            'santri_id' => $this->santri->id,
            'ustadz_id' => $this->ustadz->id,
            'parent_id' => $this->parent->id,
            'type' => 'hafalan_addition',
            'notification_channel' => 'email',
            'status' => 'sent',
            'message' => 'Notifikasi milik parent test',
        ]);

        HafalanNotification::create([
            'hafalan_id' => $otherHafalan->id,
            'santri_id' => $otherSantri->id,
            'ustadz_id' => $this->ustadz->id,
            'parent_id' => $otherParent->id,
            'type' => 'hafalan_addition',
            'notification_channel' => 'email',
            'status' => 'sent',
            'message' => 'Notifikasi milik parent lain',
        ]);

        $this->actingAs($this->parent);

        $response = $this->get('/parent/notifications');

        $response->assertStatus(200);
        $response->assertSee('Notifikasi milik parent test');
        $response->assertDontSee('Notifikasi milik parent lain');
    }

    public function test_santri_relationship_works_correctly()
    {
        $this->assertNotNull($this->santri->orangTua);
        $this->assertEquals($this->parent->id, $this->santri->orangTua->id);
    }
}
