<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class GuruBkAnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in([Announcement::STATUS_DRAFT, Announcement::STATUS_PUBLISHED])],
        ]);

        $announcements = Announcement::query()
            ->when($filters['q'] ?? null, fn ($query, $search) => $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            }))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest('updated_at')->paginate(10)->withQueryString();

        return view('dashboard.guru.pengumuman.index', compact('announcements', 'filters'));
    }

    public function create()
    {
        return view('dashboard.guru.pengumuman.form', ['announcement' => new Announcement()]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['created_by'] = $request->user()->id;
        $data['status'] = $request->input('status') === Announcement::STATUS_PUBLISHED
            ? Announcement::STATUS_PUBLISHED : Announcement::STATUS_DRAFT;
        $data['published_at'] = $data['status'] === Announcement::STATUS_PUBLISHED ? now() : null;
        $data['cover_image'] = $request->file('cover_image')?->store('announcements', 'public');

        Announcement::create($data);

        return redirect()->route('dashboard.guru.announcements.index')->with('success', 'Pengumuman berhasil disimpan.');
    }

    public function edit(Announcement $announcement)
    {
        return view('dashboard.guru.pengumuman.form', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $data = $this->validatedData($request);
        $data['slug'] = $this->uniqueSlug($data['title'], $announcement->id);
        $status = $request->input('status') === Announcement::STATUS_PUBLISHED
            ? Announcement::STATUS_PUBLISHED : Announcement::STATUS_DRAFT;
        $data['status'] = $status;
        $data['published_at'] = $status === Announcement::STATUS_PUBLISHED
            ? ($announcement->published_at ?? now()) : null;

        $oldCover = $announcement->cover_image;
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('announcements', 'public');
        }
        $announcement->update($data);
        if (! empty($data['cover_image']) && $oldCover) {
            Storage::disk('public')->delete($oldCover);
        }

        return redirect()->route('dashboard.guru.announcements.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement)
    {
        $cover = $announcement->cover_image;
        $announcement->delete();
        if ($cover) {
            Storage::disk('public')->delete($cover);
        }

        return back()->with('success', 'Pengumuman berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'max:120'],
            'excerpt' => ['required', 'string', 'max:500'],
            'body' => ['required', 'string', 'max:20000'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'pengumuman';
        $slug = $base;
        $suffix = 2;
        while (Announcement::query()->where('slug', $slug)->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
