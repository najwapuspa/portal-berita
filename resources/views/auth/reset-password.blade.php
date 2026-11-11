@extends('layouts.auth')

@section('title', 'Atur Ulang Kata Sandi – News X Paper')
@section('heading', 'Reset Password')

@section('top_right_nav')
  <a href="{{ route('login') }}">LOGIN</a>
@endsection

@section('content')

  @if ($errors->any())
    <div class="haus-alert haus-alert-error" role="alert">
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('password.update') }}" novalidate>
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    {{-- Email Address --}}
    <div class="haus-field">
      <label for="email" class="haus-label">EMAIL ADDRESS</label>
      <input id="email"
             type="email"
             name="email"
             value="{{ old('email', $email) }}"
             class="haus-input {{ $errors->has('email') ? 'has-error' : '' }}"
             required
             autofocus
             autocomplete="email">
      @error('email')
        <p class="haus-error-text">{{ $message }}</p>
      @enderror
    </div>

    {{-- New Password --}}
    <div class="haus-field">
      <label for="password" class="haus-label">NEW PASSWORD</label>
      <input id="password"
             type="password"
             name="password"
             class="haus-input {{ $errors->has('password') ? 'has-error' : '' }}"
             required
             autocomplete="new-password">
      @error('password')
        <p class="haus-error-text">{{ $message }}</p>
      @enderror
    </div>

    {{-- Confirm Password --}}
    <div class="haus-field">
      <label for="password_confirmation" class="haus-label">CONFIRM PASSWORD</label>
      <input id="password_confirmation"
             type="password"
             name="password_confirmation"
             class="haus-input"
             required
             autocomplete="new-password">
    </div>

    {{-- Submit Button --}}
    <div class="haus-btn-wrap">
      <button type="submit" class="haus-btn">
        RESET PASSWORD
      </button>
    </div>
  </form>

@endsection

@section('footer_text')
  <a href="{{ route('login') }}">BACK TO LOGIN.</a>
@endsection

