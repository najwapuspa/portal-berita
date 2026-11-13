@extends('layouts.auth')

@section('title', __('Daftar') . ' – News X Paper')
@section('heading', __('CREATE ACCOUNT'))

@section('top_right_nav')
  <a href="{{ route('login') }}">{{ mb_strtoupper(__('LOGIN')) }}</a>
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
      <label for="name" class="haus-label">{{ __('FULL NAME') }}</label>
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
      <label for="email" class="haus-label">{{ __('EMAIL') }}</label>
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
      <label for="password" class="haus-label">{{ __('PASSWORD') }}</label>
      <div class="haus-password-wrap">
        <input id="password"
               type="password"
               name="password"
               class="haus-input {{ $errors->has('password') ? 'has-error' : '' }}"
               required
               autocomplete="new-password">
        <button type="button"
                class="haus-pw-toggle"
                onclick="togglePasswordVisibility('password', this)"
                aria-label="{{ __('Lihat password') }}"
                aria-pressed="false"
                tabindex="0">
          <svg class="icon-eye-slash" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
            <line x1="1" y1="1" x2="23" y2="23"/>
          </svg>
          <svg class="icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="display:none;" aria-hidden="true">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <circle cx="12" cy="12" r="3"/>
          </svg>
        </button>
      </div>
      @error('password')
        <p class="haus-error-text">{{ $message }}</p>
      @enderror
    </div>

    {{-- Confirm Password --}}
    <div class="haus-field">
      <label for="password_confirmation" class="haus-label">{{ __('CONFIRM PASSWORD') }}</label>
      <div class="haus-password-wrap">
        <input id="password_confirmation"
               type="password"
               name="password_confirmation"
               class="haus-input"
               required
               autocomplete="new-password">
        <button type="button"
                class="haus-pw-toggle"
                onclick="togglePasswordVisibility('password_confirmation', this)"
                aria-label="{{ __('Lihat password') }}"
                aria-pressed="false"
                tabindex="0">
          <svg class="icon-eye-slash" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
            <line x1="1" y1="1" x2="23" y2="23"/>
          </svg>
          <svg class="icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="display:none;" aria-hidden="true">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <circle cx="12" cy="12" r="3"/>
          </svg>
        </button>
      </div>
    </div>

    {{-- Submit Button --}}
    <div class="haus-btn-wrap">
      <button type="submit" class="haus-btn">
        {{ __('CREATE ACCOUNT') }}
      </button>
    </div>
  </form>

@endsection

@section('footer_text')
  {{ __('ALREADY HAVE AN ACCOUNT?') }} <a href="{{ route('login') }}">{{ __('LOGIN.') }}</a>
@endsection
