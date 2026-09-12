<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password | {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page" style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);">
<div class="login-box">
    <div class="login-logo"><a href="/" class="text-white"><b>{{ config('app.name') }}</b></a></div>
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Forgot your password? Enter your email to reset it.</p>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                    <div class="input-group-append"><div class="input-group-text"><span class="fas fa-envelope"></span></div></div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
            </form>
            <p class="mb-0 text-center mt-3"><a href="{{ route('login') }}">Back to login</a></p>
        </div>
    </div>
</div>
<script nonce="{{ \ ?? ' }}" src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script nonce="{{ \ ?? ' }}" src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script nonce="{{ \ ?? ' }}" src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
