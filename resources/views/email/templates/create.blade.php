@extends('layouts.app')
@section('title', 'Create Template')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1>Create Email Template</h1>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <form action="{{ route('email.templates.store') }}" method="POST">
                        @csrf
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Template Name *</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Subject *</label>
                                    <input type="text" name="subject" class="form-control" required placeholder="Welcome to {{ agency_name }}">
                                </div>
                                <div class="form-group">
                                    <label>Category *</label>
                                    <input type="text" name="category" class="form-control" required placeholder="welcome, notification, marketing">
                                </div>
                                <div class="form-group">
                                    <label>Folder</label>
                                    <select name="folder_id" class="form-control">
                                        <option value="">None</option>
                                        @foreach($folders as $folder)
                                        <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>HTML Content *</label>
                                    <textarea name="html_content" class="form-control" rows="12" required placeholder="<h1>Hello {{ name }}"></h1>"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Plain Text Content</label>
                                    <textarea name="plain_text_content" class="form-control" rows="6"></textarea>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save Template</button>
                                <a href="{{ route('email.templates.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Variables</h3></div>
                        <div class="card-body">
                            <p>Use these variables in your template:</p>
                            <ul>
                                <li><code>{{ name }}</code> - User name</li>
                                <li><code>{{ email }}</code> - User email</li>
                                <li><code>{{ agency_name }}</code> - Agency name</li>
                                <li><code>{{ unsubscribe_url }}</code> - Unsubscribe link</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
