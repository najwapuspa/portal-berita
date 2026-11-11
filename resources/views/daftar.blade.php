@extends('layouts.auth')

@section('title', 'Daftar Akun – News X Paper')
@section('heading', 'Create Account')

@section('top_right_nav')
  <a href="{{ route('login') }}">LOGIN</a>
@endsection

@section('content')

  @if ($errors->any())
    <div class="haus-alert haus-alert-error" role="alert">
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('register') }}" novalidate>
    @csrf

    {{-- Full Name --}}
    <div class="haus-field">
      <label for="name" class="haus-label">FULL NAME</label>
      <input id="name"
             type="text"
             name="name"
             value="{{ old('name') }}"
             class="haus-input {{ $errors->has('name') ? 'has-error' : '' }}"
             required
             autofocus
             autocomplete="name">
      @error('name')
        <p class="haus-error-text">{{ $message }}</p>
      @enderror
    </div>

    {{-- Email --}}
    <div class="haus-field">
      <label for="email" class="haus-label">EMAIL</label>
      <input id="email"
             type="email"
             name="email"
             value="{{ old('email') }}"
             class="haus-input {{ $errors->has('email') ? 'has-error' : '' }}"
             required
             autocomplete="email">
      @error('email')
        <p class="haus-error-text">{{ $message }}</p>
      @enderror
    </div>

    {{-- Password --}}
    <div class="haus-field">
      <label for="password" class="haus-label">PASSWORD</label>
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
        CREATE ACCOUNT
      </button>
    </div>
  </form>

@endsection

@section('footer_text')
  ALREADY HAVE AN ACCOUNT? <a href="{{ route('login') }}">LOGIN.</a>
@endsection
