<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::latest()->paginate(10);
        return view('videos.index', compact('videos'));
    }

    public function create()
    {
        return view('videos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'video' => 'required|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime|max:512000', // max 500MB
        ]);

        $original = $request->file('video');
        $originalPath = $original->store('videos/original', 'public');

        $originalFullPath = storage_path("app/public/{$originalPath}");
        $compressedFileName = 'compressed_' . uniqid() . '.mp4';
        $compressedPath = storage_path("app/public/videos/compressed/{$compressedFileName}");

        // Ensure output directory exists
        if (!file_exists(dirname($compressedPath))) {
            mkdir(dirname($compressedPath), 0755, true);
        }

        // Compress using ffmpeg
        $ffmpegCommand = "ffmpeg -i {$originalFullPath} -vcodec libx264 -crf 28 -preset veryfast -y {$compressedPath}";
        exec($ffmpegCommand);

        // Optional: delete original video after compression
        \Storage::disk('public')->delete($originalPath);

        // Save only the compressed file path
        $publicPath = 'videos/compressed/' . $compressedFileName;

        Video::create([
            'title' => $request->title,
            'video_path' => $publicPath,
        ]);

        return redirect()->route('videos.index')->with('success', 'Video uploaded and compressed successfully.');
    }


    public function show(Video $video)
    {
        return view('videos.show', compact('video'));
    }

    public function edit(Video $video)
    {
        return view('videos.edit', compact('video'));
    }

    public function update(Request $request, Video $video)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'video' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime|max:51200',
        ]);

        $video->title = $request->title;

        if ($request->hasFile('video')) {
            // Delete old file
            Storage::disk('public')->delete($video->video_path);
            $path = $request->file('video')->store('videos', 'public');
            $video->video_path = $path;
        }

        $video->save();

        return redirect()->route('videos.index')->with('success', 'Video updated.');
    }

    public function destroy(Video $video)
    {
        Storage::disk('public')->delete($video->video_path);
        $video->delete();

        return redirect()->route('videos.index')->with('success', 'Video deleted.');
    }
}
