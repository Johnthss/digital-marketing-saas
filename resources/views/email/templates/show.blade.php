@extends('layouts.app')
@section('title', $template->name)

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <h1>{{ $template->name }}</h1>
                <div>
                    <a href="{{ route('email.templates.edit', $template) }}" class="btn btn-warning">Edit</a>
                    <a href="{{ route('email.templates.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr><th>Subject</th><td>{{ $template->subject }}</td></tr>
                                <tr><th>Category</th><td>{{ ucfirst($template->category) }}</td></tr>
                            </table>
                            <h5>HTML Content</h5>
                            <div class="card bg-light"><div class="card-body">{{ $template->html_content }}</div></div>
                            @if($template->plain_text_content)
                                <h5 class="mt-3">Plain Text</h5>
                                <div class="card bg-light"><div class="card-body">{{ $template->plain_text_content }}</div></div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
