@extends('layouts.app')
@section('title', 'Edit Template')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1>Edit: {{ $template->name }}</h1>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <form action="{{ route('email.templates.update', $template) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Template Name *</label>
                                    <input type="text" name="name" class="form-control" value="{{ $template->name }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Subject *</label>
                                    <input type="text" name="subject" class="form-control" value="{{ $template->subject }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Category *</label>
                                    <input type="text" name="category" class="form-control" value="{{ $template->category }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Folder</label>
                                    <select name="folder_id" class="form-control">
                                        <option value="">None</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>HTML Content *</label>
                                    <textarea name="html_content" class="form-control" rows="12" required>{{ $template->html_content }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>Plain Text Content</label>
                                    <textarea name="plain_text_content" class="form-control" rows="6">{{ $template->plain_text_content }}</textarea>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Update Template</button>
                                <a href="{{ route('email.templates.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Preview</h3></div>
                        <div class="card-body">
                            <button class="btn btn-sm btn-outline-primary" onclick="previewTemplate()">Preview</button>
                            <hr>
                            <div id="previewContent" style="border:1px solid #dee2e6;padding:15px;border-radius:4px;max-height:400px;overflow-y:auto;">
                                <p class="text-muted">Click Preview to see rendered template</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push("scripts")
<script>
    async function previewTemplate() {
        const response = await fetch('{{ route("email.templates.preview", $template) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ variables: {} }),
        });
        const data = await response.json();
        document.getElementById('previewContent').innerHTML = data.html;
    }
</script>
@endpush
