@extends('layouts.app')
@section('title', $asset->name)

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1>{{ $asset->name }}</h1>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body text-center">
                            @if($asset->file_type === 'image')
                                <img src="{{ $asset->thumbnail_url }}" alt="{{ $asset->alt_text ?? $asset->name }}" class="img-fluid" style="max-height: 500px">
                            @elseif($asset->file_type === 'video')
                                <video controls style="max-width: 100%; max-height: 500px">
                                    <source src="{{ $asset->thumbnail_url }}" type="{{ $asset->mime_type }}">
                                </video>
                            @else
                                <i class="fas fa-file-alt fa-5x text-muted"></i>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Details</h3></div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr><td><strong>Name</strong></td><td>{{ $asset->name }}</td></tr>
                                <tr><td><strong>Type</strong></td><td><span class="badge badge-{{ $asset->file_type === 'image' ? 'success' : ($asset->file_type === 'video' ? 'info' : 'secondary') }}">{{ $asset->file_type }}</span></td></tr>
                                <tr><td><strong>Size</strong></td><td>{{ $asset->human_size }}</td></tr>
                                <tr><td><strong>Dimensions</strong></td><td>{{ $asset->width ?? '?' }} x {{ $asset->height ?? '?' }}</td></tr>
                                <tr><td><strong>MIME</strong></td><td>{{ $asset->mime_type }}</td></tr>
                                <tr><td><strong>Folder</strong></td><td>{{ $asset->folder ?? 'Uncategorized' }}</td></tr>
                                <tr><td><strong>Uploaded</strong></td><td>{{ $asset->created_at->format('M d, Y H:i') }}</td></tr>
                                <tr><td><strong>Usage</strong></td><td>{{ $asset->usage_count }} times</td></tr>
                            </table>
                            <hr>
                            <a href="{{ route('media.download', $asset) }}" class="btn btn-primary btn-block"><i class="fas fa-download"></i> Download</a>
                            <a href="{{ route('media.duplicate', $asset) }}" class="btn btn-secondary btn-block"><i class="fas fa-copy"></i> Duplicate</a>
                            <form action="{{ route('media.destroy', $asset) }}" method="POST" class="mt-2" onsubmit="return confirm('Delete this file permanently?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-block"><i class="fas fa-trash"></i> Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
