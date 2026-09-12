<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition register-page" style="background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);">
<div class="register-box">
    <div class="register-logo">
        <a href="/" class="text-white"><b>{{ config('app.name') }}</b></a>
    </div>
    <div class="card">
        <div class="card-body register-card-body">
            <p class="register-box-msg">Register a new agency</p>

            <form action="{{ route('register') }}" method="POST">
                @csrf

                <div class="input-group mb-3">
                    <input type="text" name="agency_name" class="form-control @error('agency_name') is-invalid @enderror" placeholder="Agency Name" value="{{ old('agency_name') }}" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-building"></span></div>
                    </div>
                    @error('agency_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="input-group mb-3">
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Your Name" value="{{ old('name') }}" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-user"></span></div>
                    </div>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email') }}" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                    </div>
                    @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                    @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="input-group mb-3">
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">Register</button>
                    </div>
                </div>
            </form>

            <p class="mb-0 text-center mt-3">
                <a href="{{ route('login') }}">I already have an account</a>
            </p>
        </div>
    </div>
</div>
<script nonce="{{ $cspNonce ?? '' }}" src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script nonce="{{ $cspNonce ?? '' }}" src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script nonce="{{ $cspNonce ?? '' }}" src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
