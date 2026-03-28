@extends('layouts.auth')
@section('title', 'Register')

@section('content')
<div class="auth-card">
    <h2>Create account</h2>
    <p class="subtitle">Fill in the details below to register</p>

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form id="registerForm" action="{{ route('register.post') }}" method="POST" novalidate>
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name"
                       class="form-control @error('first_name') error @enderror"
                       value="{{ old('first_name') }}" placeholder="John">
                @error('first_name')<label class="error">{{ $message }}</label>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name"
                       class="form-control @error('last_name') error @enderror"
                       value="{{ old('last_name') }}" placeholder="Doe">
                @error('last_name')<label class="error">{{ $message }}</label>@enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" id="email" name="email"
                   class="form-control @error('email') error @enderror"
                   value="{{ old('email') }}" placeholder="you@example.com">
            @error('email')<label class="error">{{ $message }}</label>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" id="password" name="password"
                   class="form-control @error('password') error @enderror"
                   placeholder="Min. 6 characters">
            @error('password')<label class="error">{{ $message }}</label>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password_confirmation">Re-Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   class="form-control @error('password_confirmation') error @enderror"
                   placeholder="Repeat your password">
            @error('password_confirmation')<label class="error">{{ $message }}</label>@enderror
        </div>

        <button type="submit" class="btn-block">Create Account</button>
    </form>
</div>

<div class="auth-footer">
    Already have an account? <a href="{{ route('login') }}">Sign in</a>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    // Custom method: remote email uniqueness check
    $.validator.addMethod('uniqueEmail', function (value, element) {
        let isUnique = true;
        $.ajax({
            url: '{{ route("check.email") }}',
            type: 'POST',
            async: false,
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                email: value
            },
            success: function (res) {
                isUnique = !res.exists;
            }
        });
        return isUnique;
    }, 'This email address is already registered.');

    $('#registerForm').validate({
        rules: {
            first_name:            { required: true, maxlength: 100 },
            last_name:             { required: true, maxlength: 100 },
            email:                 { required: true, email: true, uniqueEmail: true },
            password:              { required: true, minlength: 6 },
            password_confirmation: { required: true, equalTo: '#password' }
        },
        messages: {
            first_name: { required: 'First name is required.' },
            last_name:  { required: 'Last name is required.' },
            email: {
                required: 'Email is required.',
                email:    'Enter a valid email address.'
            },
            password: {
                required:  'Password is required.',
                minlength: 'Password must be at least 6 characters.'
            },
            password_confirmation: {
                required: 'Please re-enter your password.',
                equalTo:  'Password and Re-Password do not match.'
            }
        },
        errorPlacement: function (error, element) {
            error.insertAfter(element);
        },
        highlight:   function (el) { $(el).addClass('error'); },
        unhighlight: function (el) { $(el).removeClass('error'); }
    });
});
</script>
@endpush
