@extends('layouts.app')

@section('content')
    <h1>All Videos</h1>

    <a href="{{ route('videos.create') }}">Upload New Video</a>

    @if(session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @foreach ($videos as $video)
    <div class="video-card">
        <h3>{{ $video->title }}</h3>

        <video width="320" height="240" controls>
            <source src="{{ asset('storage/' . $video->video_path) }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        {{-- Show file size directly from disk --}}
        <p>
            Size:
            {{ number_format(filesize(storage_path('app/public/' . $video->video_path)) / 1024 / 1024, 2) }} MB
        </p>

        {{-- Optionally, compare with original size --}}
        @if ($video->original_size && $video->compressed_size)
            <p>Original: {{ $video->original_size }} MB</p>
            <p>Compressed: {{ $video->compressed_size }} MB</p>
        @endif
        <form action="{{ route('videos.destroy', $video) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit">Delete</button>
            </form>
    </div>

@endforeach
    {{ $videos->links() }}
@endsection
