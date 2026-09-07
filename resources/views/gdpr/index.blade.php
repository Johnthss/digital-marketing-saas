@extends('layouts.app')
@section('title', 'Privacy & Data')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1>Privacy & Data Management</h1>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Data Export</h3></div>
                        <div class="card-body">
                            <p>Request a copy of your data in JSON or CSV format.</p>
                            <form action="{{ route('gdpr.export') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>Select data to export:</label>
                                    <div class="form-check">
                                        <input type="checkbox" name="export_types[]" value="posts" id="export_posts" class="form-check-input">
                                        <label for="export_posts" class="form-check-label">Posts</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="export_types[]" value="campaigns" id="export_campaigns" class="form-check-input">
                                        <label for="export_campaigns" class="form-check-label">Campaigns</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="export_types[]" value="clients" id="export_clients" class="form-check-input">
                                        <label for="export_clients" class="form-check-label">Clients</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="export_types[]" value="invoices" id="export_invoices" class="form-check-input">
                                        <label for="export_invoices" class="form-check-label">Invoices</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="export_types[]" value="activity" id="export_activity" class="form-check-input">
                                        <label for="export_activity" class="form-check-label">Activity Logs</label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Request Export</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Consent Management</h3></div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($consents as $consent)
                                    <tr>
                                        <td>{{ ucfirst($consent->consent_type) }}</td>
                                        <td><span class="badge badge-{{ $consent->granted ? 'success' : 'danger' }}">{{ $consent->granted ? 'Granted' : 'Denied' }}</span></td>
                                        <td>{{ $consent->created_at->format('M d, Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-danger">
                        <div class="card-header"><h3 class="card-title">Delete My Data</h3></div>
                        <div class="card-body">
                            <p class="text-danger"><strong>Warning:</strong> This action is irreversible. Your account will be permanently deleted after a 30-day cooling period.</p>
                            <form action="{{ route('gdpr.delete') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete your account? This cannot be undone.')">
                                @csrf
                                <div class="form-group">
                                    <label>Reason (optional)</label>
                                    <textarea name="reason" class="form-control" rows="3" placeholder="Why are you deleting your account?"></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger">Request Account Deletion</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
