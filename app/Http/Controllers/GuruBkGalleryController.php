<?php

namespace App\Http\Controllers;

use App\Models\GalleryAlbum;
use App\Models\GalleryPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class GuruBkGalleryController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in([GalleryAlbum::STATUS_DRAFT, GalleryAlbum::STATUS_PUBLISHED])],
        ]);
        $albums = GalleryAlbum::withCount('photos')
            ->with(['photos' => fn ($query) => $query->limit(1)])
            ->when($filters['q'] ?? null, fn ($query, $search) => $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            }))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest('updated_at')->paginate(10)->withQueryString();

        return view('dashboard.guru.dokumentasi.index', compact('albums', 'filters'));
    }

    public function show(GalleryAlbum $album)
    {
        $photos = $album->photos()->paginate(8)->withQueryString();

        return view('dashboard.guru.dokumentasi.show', compact('album', 'photos'));
    }

    public function create()
    {
        return view('dashboard.guru.dokumentasi.form', ['album' => new GalleryAlbum()]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedAlbum($request);
        $photos = $this->validatePhotos($request);
        $status = $request->input('status') === GalleryAlbum::STATUS_PUBLISHED
            ? GalleryAlbum::STATUS_PUBLISHED : GalleryAlbum::STATUS_DRAFT;
        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['created_by'] = $request->user()->id;
        $data['status'] = $status;
        $data['published_at'] = $status === GalleryAlbum::STATUS_PUBLISHED ? now() : null;
        $album = GalleryAlbum::create($data);
        $this->storePhotos($album, $photos, $request);

        return redirect()->route('dashboard.guru.gallery.show', $album)->with('success', 'Album dokumentasi berhasil dibuat.');
    }

    public function edit(GalleryAlbum $album)
    {
        return view('dashboard.guru.dokumentasi.form', compact('album'));
    }

    public function update(Request $request, GalleryAlbum $album)
    {
        $data = $this->validatedAlbum($request);
        $status = $request->input('status') === GalleryAlbum::STATUS_PUBLISHED
            ? GalleryAlbum::STATUS_PUBLISHED : GalleryAlbum::STATUS_DRAFT;
        $data['slug'] = $this->uniqueSlug($data['title'], $album->id);
        $data['status'] = $status;
        $data['published_at'] = $status === GalleryAlbum::STATUS_PUBLISHED ? ($album->published_at ?? now()) : null;
        $album->update($data);

        return redirect()->route('dashboard.guru.gallery.show', $album)->with('success', 'Album dokumentasi berhasil diperbarui.');
    }

    public function uploadPhotos(Request $request, GalleryAlbum $album)
    {
        $photos = $this->validatePhotos($request, required: true);
        $this->storePhotos($album, $photos, $request);

        return back()->with('success', 'Foto berhasil ditambahkan ke album.');
    }

    public function destroyPhoto(GalleryAlbum $album, GalleryPhoto $photo)
    {
        abort_unless($photo->gallery_album_id === $album->id, 404);
        $path = $photo->path;
        $photo->delete();
        Storage::disk('public')->delete($path);

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    public function replacePhoto(Request $request, GalleryAlbum $album, GalleryPhoto $photo)
    {
        abort_unless($photo->gallery_album_id === $album->id, 404);
        $data = $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'caption' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['required', 'string', 'max:255'],
        ]);

        $oldPath = $photo->path;
        $newPath = $request->file('photo')->store('gallery/'.$album->id, 'public');
        $photo->update([
            'path' => $newPath,
            'caption' => $data['caption'] ?? null,
            'alt_text' => $data['alt_text'],
            'uploaded_by' => $request->user()->id,
        ]);
        Storage::disk('public')->delete($oldPath);

        return back()->with('success', 'Foto berhasil diganti.');
    }

    public function destroy(GalleryAlbum $album)
    {
        $paths = $album->photos()->pluck('path');
        $album->delete();
        foreach ($paths as $path) {
            Storage::disk('public')->delete($path);
        }

        return redirect()->route('dashboard.guru.gallery.index')->with('success', 'Album dan seluruh fotonya berhasil dihapus.');
    }

    private function validatedAlbum(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'category' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:5000'],
            'event_date' => ['nullable', 'date'],
        ]);
    }

    private function validatePhotos(Request $request, bool $required = false): array
    {
        $validated = $request->validate([
            'photos' => [$required ? 'required' : 'nullable', 'array', 'max:20'],
            'photos.*' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'captions' => ['nullable', 'array'],
            'captions.*' => ['nullable', 'string', 'max:255'],
            'alt_texts' => ['nullable', 'array'],
            'alt_texts.*' => ['nullable', 'string', 'max:255'],
        ]);

        return $validated['photos'] ?? [];
    }

    private function storePhotos(GalleryAlbum $album, array $photos, Request $request): void
    {
        $sortOrder = (int) $album->photos()->max('sort_order') + 1;
        foreach ($photos as $index => $photo) {
            $path = $photo->store('gallery/'.$album->id, 'public');
            $album->photos()->create([
                'path' => $path,
                'caption' => $request->input("captions.{$index}"),
                'alt_text' => $request->input("alt_texts.{$index}") ?: $album->title.' - foto '.($sortOrder + $index),
                'sort_order' => $sortOrder + $index,
                'uploaded_by' => $request->user()->id,
            ]);
        }
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'dokumentasi';
        $slug = $base;
        $suffix = 2;
        while (GalleryAlbum::query()->where('slug', $slug)->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
