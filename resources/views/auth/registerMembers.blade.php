@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="w-full max-w-md mx-auto">
        <form method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" action="{{ route('register') }}">
          <label class="label-base" for="email">Email</label>
          <input class="input-base {{ $errors->has('email') ? ' border-red' : '' }}" type="email" id="email"  name="email" value="{{ old('email') }}" required autofocus>
          @if ($errors->has('email'))
          <span class="error-base" role="alert"><strong>{{ $errors->first('email') }}</strong></span>
          @endif
          @csrf
          <label for="name" class="label-base">{{ __('Name') }}</label>

          <input id="name" type="text" class="input-base{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" required autofocus>

          @if ($errors->has('name'))
          <span class="error-base" role="alert">
            <strong>{{ $errors->first('name') }}</strong>
        </span>
        @endif

        <label for="email" class="label-base">{{ __('E-Mail Address') }}</label>

        <input id="email" type="email" class="input-base{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

        @if ($errors->has('email'))
        <span class="error-base" role="alert">
            <strong>{{ $errors->first('email') }}</strong>
        </span>
        @endif

        <label for="password" class="label-base">{{ __('Password') }}</label>

        <input id="password" type="password" class="input-base{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

        @if ($errors->has('password'))
        <span class="error-base" role="alert">
            <strong>{{ $errors->first('password') }}</strong>
        </span>
        @endif

        <label for="password-confirm" class="label-base">{{ __('Confirm Password') }}</label>

        <input id="password-confirm" type="password" class="input-base" name="password_confirmation" required>

        <button type="submit" class="btn btn-primary mt-2">
            {{ __('Register') }}
        </button>
    </form>
</div>
</div>
@endsection
