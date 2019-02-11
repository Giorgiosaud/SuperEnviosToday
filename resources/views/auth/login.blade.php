@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="w-full max-w-xs mx-auto">
      <form  method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" action="{{ route('login') }}">
        @csrf
        <div class="mb-4">
          <label class="label-base" for="email">Email</label>
          <input class="input-base {{ $errors->has('email') ? ' border-red' : '' }}" type="email" id="email"  name="email" value="{{ old('email') }}" required autofocus>
          @if ($errors->has('email'))
          <span class="error-base" role="alert"><strong>{{ $errors->first('email') }}</strong></span>
          @endif
      </div>
      <div class="mb-6">
          <label class="label-base" for="password">Password</label>
          <input class="input-base{{ $errors->has('password') ? ' border-red' : '' }}" id="password" type="password" placeholder="******************" name="password">
          @if ($errors->has('password'))
          <span class="text-red text-xs italic" role="alert"><strong>{{ $errors->first('password') }}</strong></span>
          @endif
      </div>
      <label class="label-base">
        <input class="mr-2 leading-tight" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}> <span class="text-sm">
            {{ __('Remember Me') }}
        </span></label>
        <div class="flex items-center justify-between">
            <button class="btn-primary" type="submit" >
              {{--  --}}
                {{ __('Login') }}
            </button>
            <a class="inline-block align-baseline font-bold text-sm text-blue hover:text-blue-darker" href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a>
        </div>
    </form>
    <p class="text-center text-grey text-xs">
        ©2019 Super Envios Today. All rights reserved.
    </p>
</div>
</div>

@endsection
