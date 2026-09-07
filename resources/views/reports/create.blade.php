@extends('layouts.app')
@section('title', 'Create Report')

@section('content')
<div class="content-wrapper">
    <div class="content-header"><div class="container-fluid"><h1>Create Report</h1></div></div>
    <div class="content"><div class="container-fluid">
        <div class="row"><div class="col-md-8">
            <form action="{{ route('reports.store') }}" method="POST">@csrf
                <div class="card">
                    <div class="card-body">
                        <div class="form-group"><label>Name *</label><input type="text" name="name" class="form-control" required></div>
                        <div class="form-group"><label>Type *</label>
                            <select name="type" class="form-control" required>
                                <option value="social">Social Media</option>
                                <option value="email">Email</option>
                                <option value="campaign">Campaign</option>
                                <option value="analytics">Analytics</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                        <div class="form-group"><label>Format *</label>
                            <select name="format" class="form-control" required>
                                <option value="pdf">PDF</option>
                                <option value="csv">CSV</option>
                                <option value="xlsx">Excel</option>
                            </select>
                        </div>
                        <div class="form-group"><label>Schedule *</label>
                            <select name="schedule" class="form-control" required>
                                <option value="once">Once</option>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-footer"><button class="btn btn-primary">Create</button><a href="{{ route('reports.index') }}" class="btn btn-secondary">Cancel</a></div>
                </div>
            </form>
        </div></div>
    </div></div>
</div>
@endsection
