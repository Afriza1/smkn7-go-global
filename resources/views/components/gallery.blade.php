<section id="galeri" class="py-12 md:py-14 bg-white border-b border-slate-200/80" aria-labelledby="gallery-heading">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-5"><div><span class="inline-block text-xs font-bold uppercase tracking-wider text-[#166534] mb-2">Dokumentasi Visual</span><h2 id="gallery-heading" class="text-2xl sm:text-3xl font-extrabold text-[#0F2F5F] tracking-tight">Galeri Kegiatan & Momen Bersejarah</h2><p class="mt-2 text-sm text-slate-600 max-w-2xl">Dokumentasi kegiatan SMKN 7 Jember Go Global.</p></div>
            <div class="flex flex-wrap items-center gap-2" id="gallery-filter-tabs"><button type="button" class="gallery-filter-btn px-3.5 py-1.5 rounded-full text-xs font-bold transition-all bg-[#0F2F5F] text-white active" data-filter="all">Semua Kegiatan</button>@foreach($galleryAlbums->pluck('category')->filter()->unique() as $category)<button type="button" class="gallery-filter-btn px-3.5 py-1.5 rounded-full text-xs font-bold transition-all bg-slate-100 hover:bg-slate-200 text-slate-700" data-filter="{{ Str::slug($category) }}">{{ $category }}</button>@endforeach</div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="gallery-grid">
            @forelse($galleryAlbums as $album)
                @php($cover = $album->photos->first())
                <a href="{{ route('gallery.show', $album->slug) }}" class="gallery-item group relative rounded-2xl overflow-hidden border border-slate-200 shadow-xs hover:shadow-lg transition-all duration-300 bg-slate-100" data-category="{{ Str::slug($album->category ?: 'kegiatan') }}">
                    <div class="h-40 sm:h-44 relative overflow-hidden bg-slate-100">@if($cover)<img src="{{ Storage::disk('public')->url($cover->path) }}" alt="{{ $cover->alt_text }}" class="h-full w-full object-cover" loading="lazy">@else<div class="flex h-full items-center justify-center text-slate-400">Dokumentasi Kegiatan</div>@endif<div class="gallery-overlay absolute inset-0 bg-[#0F2F5F]/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10"><span class="px-4 py-2 rounded-xl bg-white text-[#0F2F5F] text-xs font-bold shadow-lg">Lihat Album · {{ $album->photos->count() }} Foto</span></div></div>
                    <div class="p-4 bg-white"><div class="flex items-center justify-between text-xs text-slate-500 mb-1"><span>{{ $album->category ?: 'Kegiatan' }}</span><span>{{ $album->event_date?->translatedFormat('d M Y') }}</span></div><h3 class="text-sm font-bold text-slate-900 mb-1">{{ $album->title }}</h3><p class="text-xs leading-relaxed text-slate-600 line-clamp-2">{{ $album->description }}</p></div>
                </a>
            @empty<div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500 sm:col-span-2 lg:col-span-3">Belum ada dokumentasi kegiatan yang dipublikasikan.</div>@endforelse
        </div>
    </div>
</section>
