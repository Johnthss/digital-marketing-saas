@extends('layouts.app')
@section('title', 'Setup Your Agency')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Progress Bar -->
            <div class="card card-outline card-primary mb-4">
                <div class="card-body">
                    <h5 class="text-center mb-3">Step 1 of 5: Create Your Agency</h5>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-primary progress-bar-striped" role="progressbar" style="width: 20%;">
                            20%
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-2 text-sm text-muted">
                        <span class="font-weight-bold text-primary">Agency Info</span>
                        <span>Social</span>
                        <span>Team</span>
                        <span>Campaign</span>
                        <span>AI</span>
                    </div>
                </div>
            </div>

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-building mr-2"></i>Tell Us About Your Agency</h3>
                </div>

                <form action="{{ route('onboarding.step1') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <i class="fas fa-check mr-1"></i>{{ session('success') }}
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="agency_name">Agency Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                </div>
                                <input type="text" class="form-control @error('agency_name') is-invalid @enderror"
                                       id="agency_name" name="agency_name"
                                       value="{{ old('agency_name', $agency->name ?? '') }}"
                                       placeholder="Enter your agency name" required>
                                @error('agency_name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="website">Website</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                </div>
                                <input type="url" class="form-control @error('website') is-invalid @enderror"
                                       id="website" name="website"
                                       value="{{ old('website', $agency->website ?? '') }}"
                                       placeholder="https://youragency.com">
                                @error('website')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Your agency's website URL (optional)</small>
                        </div>

                        <div class="form-group">
                            <label for="timezone">Timezone <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                </div>
                                <select class="form-control @error('timezone') is-invalid @enderror"
                                        id="timezone" name="timezone" required>
                                    <option value="">Select your timezone</option>
                                    @foreach(timezone_identifiers_list() as $tz)
                                        <option value="{{ $tz }}" {{ old('timezone', $agency->timezone ?? config('app.timezone')) === $tz ? 'selected' : '' }}>
                                            {{ $tz }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('timezone')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Used for scheduling posts and reports</small>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-lg">
                            Next: Connect Social Accounts <i class="fas fa-arrow-right ml-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
