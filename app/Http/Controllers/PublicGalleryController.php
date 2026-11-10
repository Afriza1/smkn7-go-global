<?php

namespace App\Http\Controllers;

use App\Models\GalleryAlbum;

class PublicGalleryController extends Controller
{
    public function show(GalleryAlbum $album)
    {
        abort_unless($album->status === GalleryAlbum::STATUS_PUBLISHED, 404);
        $album->load('photos');

        return view('pages.gallery.show', compact('album'));
    }
}
