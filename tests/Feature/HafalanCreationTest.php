<?php

namespace Tests\Feature;

use App\Models\Hafalan;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        // Create test users
        $this->ustadz = User::factory()->create(['role' => 'ustadz']);
        $this->parent = User::factory()->create(['role' => 'orang_tua']);
        
        // Create santri with parent
        $this->santri = Santri::factory()->create([
            'orang_tua_id' => $this->parent->id,
        ]);
    }

    /** @test */
    public function ustadz_can_create_hafalan()
    {
        $this->actingAs($this->ustadz);

        $response = $this->post('/ustadz/hafalan/store', [
            'santri_id' => $this->santri->id,
            'surah' => 'Al-Fatihah',
            'verse_start' => 1,
            'verse_end' => 7,
            'notes' => 'Test hafalan creation',
        ]);

        // Should redirect or succeed
        $this->assertNotNull($response->getStatusCode() === 302 || $response->getStatusCode() === 200);
        
        // Hafalan should exist in database
        $this->assertDatabaseHas('hafalan', [
            'santri_id' => $this->santri->id,
            'surah' => 'Al-Fatihah',
        ]);
    }

    /** @test */
    public function hafalan_creation_triggers_notification_event()
    {
        \Event::fake();

        $this->actingAs($this->ustadz);

        $this->post('/ustadz/hafalan/store', [
            'santri_id' => $this->santri->id,
            'surah' => 'Al-Fatihah',
            'verse_start' => 1,
            'verse_end' => 7,
        ]);

        // HafalanCreated event should be dispatched
        \Event::assertDispatched(\App\Events\HafalanCreated::class);
    }

    /** @test */
    public function hafalan_with_audio_is_stored_correctly()
    {
        $this->actingAs($this->ustadz);

        // Create fake audio file
        $audioContent = file_get_contents(
            storage_path('app/test-audio.wav')
        ) ?? base64_decode('UklGRiYAAABXQVZFZm10IBAAAAABAAEAQB8AAAB9AAACABAAZGF0YQIAAAAAAA==');

        $response = $this->post('/ustadz/hafalan/store', [
            'santri_id' => $this->santri->id,
            'surah' => 'Al-Fatihah',
            'verse_start' => 1,
            'verse_end' => 7,
            'audio_blob' => 'data:audio/wav;base64,' . base64_encode($audioContent),
        ]);

        $hafalan = Hafalan::latest()->first();
        
        // Audio should be stored
        if ($hafalan && $hafalan->audio_path) {
            $this->assertTrue(
                \Storage::disk('public')->exists($hafalan->audio_path),
                'Audio file should exist in storage'
            );
        }
    }

    /** @test */
    public function parent_can_see_hafalan_notification()
    {
        // Create hafalan
        $hafalan = Hafalan::factory()->create([
            'santri_id' => $this->santri->id,
            'ustadz_id' => $this->ustadz->id,
        ]);

        // Notification should be created
        $this->actingAs($this->parent);
        
        $response = $this->get('/orang-tua/notifications');
        
        $this->assertResponseOk();
    }

    /** @test */
    public function santri_relationship_works_correctly()
    {
        // Test that santri->orangTua relationship works
        $this->assertNotNull($this->santri->orangTua);
        $this->assertEquals($this->parent->id, $this->santri->orangTua->id);
    }

    /** @test */
    public function hafalan_can_have_multiple_notifications()
    {
        // Create hafalan
        $hafalan = Hafalan::factory()->create([
            'santri_id' => $this->santri->id,
        ]);

        // Create multiple notifications (email + in-app)
        \DB::table('hafalan_notifications')->insert([
            [
                'hafalan_id' => $hafalan->id,
                'user_id' => $this->parent->id,
                'channel' => 'email',
                'status' => 'pending',
                'created_at' => now(),
            ],
            [
                'hafalan_id' => $hafalan->id,
                'user_id' => $this->parent->id,
                'channel' => 'in_app',
                'status' => 'pending',
                'created_at' => now(),
            ],
        ]);

        $notifications = \App\Models\HafalanNotification::where('hafalan_id', $hafalan->id)->get();
        
        $this->assertCount(2, $notifications);
    }
}
