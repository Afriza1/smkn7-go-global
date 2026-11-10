<?php

namespace Tests\Feature;

use App\Models\GalleryAlbum;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GuruContentManagementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->app['env'] = 'testing';
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');

        Schema::create('users', function (Blueprint $table): void {
            $table->id(); $table->string('name'); $table->string('email')->unique(); $table->string('password')->nullable();
            $table->string('role'); $table->string('status')->default('aktif'); $table->timestamps();
        });
        foreach ([
            '2026_11_11_120000_create_announcements_table.php',
            '2026_11_11_120100_create_gallery_albums_table.php',
            '2026_11_11_120200_create_gallery_photos_table.php',
        ] as $migrationFile) {
            $migration = require database_path('migrations/'.$migrationFile);
            $migration->up();
        }
    }

    protected function tearDown(): void
    {
        DB::disconnect('sqlite');
        parent::tearDown();
    }

    private function user(string $role = 'guru', string $status = 'aktif'): User
    {
        return User::query()->create([
            'name' => 'Guru Uji', 'email' => uniqid('guru').'@example.test', 'password' => 'password', 'role' => $role, 'status' => $status,
        ]);
    }

    public function test_guru_can_create_publish_and_edit_announcement_while_draft_is_not_public(): void
    {
        $guru = $this->user();
        $this->actingAs($guru);
        $this->post(route('dashboard.guru.announcements.store'), [
            'title' => 'Kegiatan Baru', 'category' => 'Kegiatan', 'excerpt' => 'Ringkasan kegiatan', 'body' => 'Isi pengumuman', 'status' => 'draft',
        ])->assertRedirect(route('dashboard.guru.announcements.index'));
        $announcement = \App\Models\Announcement::firstOrFail();
        $this->assertSame('draft', $announcement->status);
        auth()->logout();
        $this->get(route('landing'))->assertDontSee('Kegiatan Baru');

        $this->actingAs($guru);
        $this->put(route('dashboard.guru.announcements.update', $announcement), [
            'title' => 'Kegiatan Baru', 'category' => 'Kegiatan', 'excerpt' => 'Ringkasan kegiatan', 'body' => 'Isi pengumuman', 'status' => 'published',
        ])->assertRedirect(route('dashboard.guru.announcements.index'));
        $this->assertSame('published', $announcement->fresh()->status);
        $this->assertNotNull($announcement->fresh()->published_at);
        auth()->logout();
        $this->get(route('landing'))->assertSee('Kegiatan Baru');

        $this->actingAs($guru);
        $this->delete(route('dashboard.guru.announcements.destroy', $announcement))->assertRedirect();
        $this->assertDatabaseMissing('announcements', ['id' => $announcement->id]);
    }

    public function test_non_guru_cannot_manage_guru_content(): void
    {
        $this->actingAs($this->user('siswa'));
        $this->get(route('dashboard.guru.announcements.index'))->assertRedirect(route('dashboard.siswa'));
        $this->get(route('dashboard.guru.gallery.index'))->assertRedirect(route('dashboard.siswa'));

        $this->actingAs($this->user('guru', 'menunggu_verifikasi'));
        $this->get(route('dashboard.guru.announcements.index'))->assertForbidden();
    }

    public function test_gallery_upload_draft_visibility_and_file_cleanup(): void
    {
        Storage::fake('public');
        $guru = $this->user();
        $this->actingAs($guru);
        $this->post(route('dashboard.guru.gallery.store'), [
            'title' => 'Dokumentasi Uji', 'category' => 'Seleksi', 'description' => 'Album uji', 'status' => 'draft',
            'photos' => [UploadedFile::fake()->image('satu.jpg'), UploadedFile::fake()->image('dua.png')],
        ])->assertRedirect();

        $album = GalleryAlbum::with('photos')->firstOrFail();
        $this->assertCount(2, $album->photos);
        foreach ($album->photos as $photo) Storage::disk('public')->assertExists($photo->path);
        auth()->logout();
        $this->get(route('gallery.show', $album->slug))->assertNotFound();

        $this->actingAs($guru);
        $this->put(route('dashboard.guru.gallery.update', $album), [
            'title' => 'Dokumentasi Uji', 'category' => 'Seleksi', 'description' => 'Album uji', 'status' => 'published',
        ])->assertRedirect();
        auth()->logout();
        $this->get(route('landing'))->assertSee('Dokumentasi Uji');
        $this->get(route('gallery.show', $album->slug))->assertOk()->assertSee('Dokumentasi Uji');
        $this->actingAs($guru);

        $firstPhoto = $album->photos->first();
        $oldPhotoPath = $firstPhoto->path;
        $this->put(route('dashboard.guru.gallery.photos.update', [$album, $firstPhoto]), [
            'photo' => UploadedFile::fake()->image('baru.jpg'), 'caption' => 'Foto pengganti', 'alt_text' => 'Foto kegiatan yang diperbarui',
        ])->assertRedirect();
        Storage::disk('public')->assertMissing($oldPhotoPath);
        $firstPhoto->refresh();
        Storage::disk('public')->assertExists($firstPhoto->path);
        $this->delete(route('dashboard.guru.gallery.photos.destroy', [$album, $firstPhoto]))->assertRedirect();
        Storage::disk('public')->assertMissing($firstPhoto->path);

        $remainingPaths = $album->photos()->pluck('path')->all();
        $this->delete(route('dashboard.guru.gallery.destroy', $album))->assertRedirect();
        foreach ($remainingPaths as $path) Storage::disk('public')->assertMissing($path);
        $this->assertDatabaseMissing('gallery_albums', ['id' => $album->id]);
    }

    public function test_gallery_detail_is_guru_only_and_paginates_photos_in_sort_order(): void
    {
        Storage::fake('public');
        $guru = $this->user();
        $album = GalleryAlbum::query()->create([
            'title' => 'Album Detail Uji', 'slug' => 'album-detail-uji', 'category' => 'Kegiatan Sekolah',
            'description' => 'Album untuk menguji detail dan pagination.', 'status' => 'draft', 'created_by' => $guru->id,
        ]);
        foreach (range(1, 9) as $sortOrder) {
            $album->photos()->create([
                'path' => "gallery/{$album->id}/foto-{$sortOrder}.jpg",
                'caption' => "Foto {$sortOrder}", 'alt_text' => "Foto kegiatan {$sortOrder}", 'sort_order' => $sortOrder,
                'uploaded_by' => $guru->id,
            ]);
        }

        $this->actingAs($guru);
        $this->get(route('dashboard.guru.gallery.index'))
            ->assertOk()
            ->assertViewHas('albums', fn ($albums) => $albums->count() === 1 && $albums->first()->photos->count() === 1)
            ->assertSee('Detail/Kelola')
            ->assertDontSee('Ganti Foto');

        $this->get(route('dashboard.guru.gallery.create'))->assertOk()->assertSee('Tambah Album Dokumentasi');
        $this->get(route('dashboard.guru.gallery.edit', $album))
            ->assertOk()
            ->assertSee('Edit Informasi Album')
            ->assertDontSee('Ganti Foto');

        $this->get(route('dashboard.guru.gallery.show', $album))
            ->assertOk()
            ->assertSee('Album Detail Uji')
            ->assertSee("confirm('Hapus foto ini?')", false)
            ->assertViewHas('photos', fn ($photos) => $photos->count() === 8 && $photos->currentPage() === 1 && $photos->first()->sort_order === 1);

        $this->get(route('dashboard.guru.gallery.show', $album).'?page=2')
            ->assertOk()
            ->assertViewHas('photos', fn ($photos) => $photos->count() === 1 && $photos->currentPage() === 2 && $photos->first()->sort_order === 9);

        $this->from(route('dashboard.guru.gallery.show', $album))
            ->post(route('dashboard.guru.gallery.photos.store', $album), [
                'photos' => [UploadedFile::fake()->image('tambahan-satu.jpg'), UploadedFile::fake()->image('tambahan-dua.jpg')],
            ])
            ->assertRedirect(route('dashboard.guru.gallery.show', $album));
        $this->assertSame(11, $album->photos()->count());
        $this->assertCount(2, Storage::disk('public')->allFiles("gallery/{$album->id}"));

        auth()->logout();
        $this->actingAs($this->user('siswa'))
            ->get(route('dashboard.guru.gallery.show', $album))
            ->assertRedirect(route('dashboard.siswa'));
    }

    public function test_gallery_rejects_files_that_are_not_images(): void
    {
        Storage::fake('public');
        $this->actingAs($this->user());

        $this->post(route('dashboard.guru.gallery.store'), [
            'title' => 'Album Invalid', 'category' => 'Uji', 'status' => 'draft',
            'photos' => [UploadedFile::fake()->create('dokumen.txt', 2, 'text/plain')],
        ])->assertSessionHasErrors('photos.0');

        $this->assertDatabaseMissing('gallery_albums', ['title' => 'Album Invalid']);
        $this->assertSame([], Storage::disk('public')->allFiles());

        $this->post(route('dashboard.guru.gallery.store'), [
            'title' => 'Album Terlalu Besar', 'category' => 'Uji', 'status' => 'draft',
            'photos' => [UploadedFile::fake()->image('besar.jpg')->size(5121)],
        ])->assertSessionHasErrors('photos.0');
        $this->assertDatabaseMissing('gallery_albums', ['title' => 'Album Terlalu Besar']);
    }
}
