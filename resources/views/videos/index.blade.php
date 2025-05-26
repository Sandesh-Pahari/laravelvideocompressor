@extends('layouts.app')

@section('content')
    <h1>All Videos</h1>

    <a href="{{ route('videos.create') }}">Upload New Video</a>

    @if(session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @foreach($videos as $video)
        <div>
            <h3>{{ $video->title }}</h3>
            <video width="320" height="240" controls>
                <source src="{{ asset('storage/' . $video->video_path) }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <br>
            <a href="{{ route('videos.edit', $video) }}">Edit</a>
            <form action="{{ route('videos.destroy', $video) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit">Delete</button>
            </form>
        </div>
    @endforeach

    {{ $videos->links() }}
@endsection
