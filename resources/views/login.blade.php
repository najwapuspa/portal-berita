@extends('layouts.auth')

@section('title', 'Masuk – News X Paper')
@section('heading', 'Login')

@section('top_right_nav')
  <a href="{{ route('register') }}">DAFTAR</a>
@endsection

@section('content')

  @if (session('status'))
    <div class="haus-alert haus-alert-success" role="alert">
      {{ session('status') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="haus-alert haus-alert-error" role="alert">
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('login') }}" novalidate>
    @csrf

    {{-- Field Email --}}
    <div class="haus-field">
      <label for="email" class="haus-label">EMAIL</label>
      <input id="email"
             type="email"
             name="email"
             value="{{ old('email') }}"
             class="haus-input {{ $errors->has('email') ? 'has-error' : '' }}"
             required
             autofocus
             autocomplete="email">
    </div>

    {{-- Field Password --}}
    <div class="haus-field">
      <label for="password" class="haus-label">PASSWORD</label>
      <input id="password"
             type="password"
             name="password"
             class="haus-input {{ $errors->has('password') ? 'has-error' : '' }}"
             required
             autocomplete="current-password">

      {{-- Forgot Password Link --}}
      <div class="haus-field-sub">
        <a href="{{ route('password.request') }}" class="haus-forgot-link">
          FORGOT PASSWORD?
        </a>
      </div>
    </div>

    {{-- Remember Me --}}
    <label class="haus-remember-wrap">
      <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
      <span>Ingat sesi saya di perangkat ini</span>
    </label>

    {{-- Submit Button --}}
    <div class="haus-btn-wrap">
      <button type="submit" class="haus-btn">
        LOGIN
      </button>
    </div>
  </form>

@endsection

@section('footer_text')
  DON'T HAVE AN ACCOUNT? <a href="{{ route('register') }}">CREATE ONE.</a>
@endsection
