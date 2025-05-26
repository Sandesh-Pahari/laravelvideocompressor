@extends('layouts.app')

@section('content')
    <h1>Upload Video</h1>

    <form action="{{ route('videos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label>Title:</label>
        <input type="text" name="title" required>

        <label>Video File:</label>
        <input type="file" name="video" accept="video/*" required>

        <button type="submit">Upload</button>
    </form>
@endsection
