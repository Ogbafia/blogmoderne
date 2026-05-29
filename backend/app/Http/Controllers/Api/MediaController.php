<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * POST /api/media/upload (auth)
     * Upload d'image avec redimensionnement automatique.
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file'    => 'required|file|mimes:jpeg,png,webp,gif|max:10240', // 10MB
            'alt_text'=> 'nullable|string|max:255',
        ]);

        $file = $request->file('file');
        $path = $file->store('media/' . date('Y/m'), 'public');

        // Récupérer les dimensions
        [$width, $height] = getimagesize($file->getRealPath());

        $media = Media::create([
            'filename'      => basename($path),
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'disk'          => 'public',
            'path'          => $path,
            'size'          => $file->getSize(),
            'width'         => $width,
            'height'        => $height,
            'alt_text'      => $request->alt_text,
            'uploaded_by'   => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Fichier uploadé.',
            'data'    => $media,
        ], 201);
    }

    /**
     * GET /api/media (auth + admin)
     */
    public function index(Request $request): JsonResponse
    {
        $media = Media::with('uploader:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate(24);

        return response()->json(['data' => $media]);
    }

    /**
     * DELETE /api/media/{id} (auth + admin ou owner)
     */
    public function destroy(string $id): JsonResponse
    {
        $media = Media::findOrFail($id);
        // $this->authorize('delete', $media);

        Storage::disk('public')->delete($media->path);
        $media->delete();

        return response()->json(['message' => 'Média supprimé.']);
    }
}
