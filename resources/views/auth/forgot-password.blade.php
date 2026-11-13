@extends('layouts.auth')

@section('title', __('Forgot Password') . ' – News X Paper')
@section('heading', __('Forgot Password'))

@section('top_right_nav')
  <a href="{{ route('login') }}">{{ __('LOGIN') }}</a>
@endsection

@section('content')

  <p class="haus-desc">
    {{ __('Enter your email address to receive a password reset link.') }}
  </p>

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

  <form method="POST" action="{{ route('password.email') }}" novalidate>
    @csrf

    {{-- Email Address --}}
    <div class="haus-field">
      <label for="email" class="haus-label">{{ __('EMAIL ADDRESS') }}</label>
      <input id="email"
             type="email"
             name="email"
             value="{{ old('email') }}"
             class="haus-input {{ $errors->has('email') ? 'has-error' : '' }}"
             required
             autofocus
             autocomplete="email">
      @error('email')
        <p class="haus-error-text">{{ $message }}</p>
      @enderror
    </div>

    {{-- Submit Button --}}
    <div class="haus-btn-wrap">
      <button type="submit" class="haus-btn">
        {{ __('SEND RESET LINK') }}
      </button>
    </div>
  </form>

@endsection

@section('footer_text')
  <a href="{{ route('login') }}">{{ __('BACK TO LOGIN.') }}</a>
@endsection
