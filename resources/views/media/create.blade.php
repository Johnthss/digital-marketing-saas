@extends('layouts.app')
@section('title', 'Upload Media')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1>Upload Media Files</h1>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Upload Files</h3></div>
                        <div class="card-body">
                            <form action="{{ route('media.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label>Select Files</label>
                                    <input type="file" name="files[]" multiple accept="image/*,video/*,.pdf,.doc,.docx" required>
                                </div>
                                <div class="form-group">
                                    <label>Folder</label>
                                    <input type="text" name="folder" class="form-control" placeholder="e.g., social-posts, banners" list="folders">
                                    <datalist id="folders">
                                        @foreach($folders as $folder)
                                            @if($folder)<option value="{{ $folder }}">@endif
                                        @endforeach
                                    </datalist>
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Upload</button>
                                <a href="{{ route('media.index') }}" class="btn btn-secondary">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
