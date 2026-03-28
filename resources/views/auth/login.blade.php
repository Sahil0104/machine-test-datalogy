@extends('layouts.auth')
@section('title', 'Login')

@section('content')
<div class="auth-card">
    <h2>Welcome back</h2>
    <p class="subtitle">Sign in to your account</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->has('login_error'))
        <div class="alert alert-danger">{{ $errors->first('login_error') }}</div>
    @endif

    <form id="loginForm" action="{{ route('login.post') }}" method="POST" novalidate>
        @csrf
        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" id="email" name="email"
                   class="form-control @error('email') error @enderror"
                   value="{{ old('email') }}"
                   placeholder="you@example.com" autocomplete="email">
            @error('email')<label class="error">{{ $message }}</label>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" id="password" name="password"
                   class="form-control @error('password') error @enderror"
                   placeholder="••••••••" autocomplete="current-password">
            @error('password')<label class="error">{{ $message }}</label>@enderror
        </div>

        <button type="submit" class="btn-block">Sign In</button>
    </form>
</div>

<div class="auth-footer">
    Don't have an account? <a href="{{ route('register') }}">Create one</a>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    $('#loginForm').validate({
        rules: {
            email:    { required: true, email: true },
            password: { required: true }
        },
        messages: {
            email: {
                required: 'Email is required.',
                email:    'Enter a valid email address.'
            },
            password: { required: 'Password is required.' }
        },
        errorPlacement: function (error, element) {
            error.insertAfter(element);
        },
        highlight: function (el) { $(el).addClass('error'); },
        unhighlight: function (el) { $(el).removeClass('error'); }
    });
});
</script>
@endpush
