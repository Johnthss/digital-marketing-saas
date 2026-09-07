@extends('layouts.app')
@section('title', 'White-Label Settings')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1>White-Label Settings</h1>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <form action="{{ route('white-label.update') }}" method="POST">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Branding</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Enable White-Label</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="enabled" class="custom-control-input" id="enableWhiteLabel" {{ ($settings->enabled ?? false) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="enableWhiteLabel">Enable custom branding</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Brand Name</label>
                                    <input type="text" name="brand_name" class="form-control" value="{{ $settings->brand_name ?? '' }}" placeholder="{{ config('app.name') }}">
                                </div>
                                <div class="form-group">
                                    <label>Brand Color</label>
                                    <input type="color" name="brand_color" class="form-control" value="{{ $settings->brand_color ?? '#007bff' }}" style="height: 40px;">
                                </div>
                                <div class="form-group">
                                    <label>Logo URL</label>
                                    <input type="url" name="logo_url" class="form-control" value="{{ $settings->logo_url ?? '' }}" placeholder="https://...">
                                </div>
                                <div class="form-group">
                                    <label>Favicon URL</label>
                                    <input type="url" name="favicon_url" class="form-control" value="{{ $settings->favicon_url ?? '' }}" placeholder="https://...">
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Email Branding</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>From Name</label>
                                    <input type="text" name="from_name" class="form-control" value="{{ $settings->from_name ?? '' }}" placeholder="{{ config('mail.from.name') }}">
                                </div>
                                <div class="form-group">
                                    <label>From Email</label>
                                    <input type="email" name="from_email" class="form-control" value="{{ $settings->from_email ?? '' }}" placeholder="{{ config('mail.from.address') }}">
                                </div>
                                <div class="form-group">
                                    <label>Email Signature</label>
                                    <textarea name="email_signature" class="form-control" rows="4">{{ $settings->email_signature ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Advanced</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Custom CSS</label>
                                    <textarea name="custom_css" class="form-control" rows="8" placeholder="body { background: #f5f5f5; }">{{ $settings->custom_css ?? '' }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>Remove "Powered By"</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="hide_powered_by" value="1" class="custom-control-input" id="hidePoweredBy" {{ ($settings->hide_powered_by ?? false) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="hidePoweredBy">Hide footer credit</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Settings</button>
                    </form>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Preview</h3>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                @if(!empty($settings->logo_url))
                                    <img src="{{ $settings->logo_url }}" alt="Logo" style="max-height: 60px;">
                                @else
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                @endif
                            </div>
                            <h4 class="text-center" id="brandPreview">{{ $settings->brand_name ?? config('app.name') }}</h4>
                            <p class="text-center text-muted">Brand color: <span id="colorPreview" style="display:inline-block;width:20px;height:20px;border-radius:4px;vertical-align:middle;background:{{ $settings->brand_color ?? '#007bff' }}"></span></p>
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
    document.querySelector('input[name="brand_name"]').addEventListener('input', function() {
        document.getElementById('brandPreview').textContent = this.value || '{{ config("app.name") }}';
    });
    document.querySelector('input[name="brand_color"]').addEventListener('input', function() {
        document.getElementById('colorPreview').style.background = this.value;
    });
</script>
@endpush
