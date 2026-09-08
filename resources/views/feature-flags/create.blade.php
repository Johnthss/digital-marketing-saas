@extends('layouts.app')
@section('title', 'Create Feature Flag')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1>Create Feature Flag</h1>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <form action="{{ route('feature-flags.store') }}" method="POST">
                        @csrf
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Feature Key *</label>
                                    <input type="text" name="feature_key" class="form-control" required placeholder="feature_key">
                                </div>
                                <div class="form-group">
                                    <label>Feature Name *</label>
                                    <input type="text" name="feature_name" class="form-control" required placeholder="Feature Name">
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Enabled</label>
                                    <select name="enabled" class="form-control">
                                        <option value="1">On</option>
                                        <option value="0">Off</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save Flag</button>
                                <a href="{{ route('feature-flags.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
