@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group row">
                            <label for="idn_type" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <select
                                    id="idn_type"
                                    class="form-control{{ $errors->has('idn_type') ? ' is-invalid' : '' }}"
                                    name="idn_type" value="{{ $idn_type ?? old('idn_type') }}" required autofocus>
                                    <option value="CI">CI</option>
                                    <option value="RUT">RUT</option>
                                    <option value="PASSPORT">PASSPORT</option>
                                    <option value="DNI">DNI</option>
                                </select>
                                @if ($errors->has('idn_type'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('idn_type') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="idn" class="col-md-4 col-form-label text-md-right">{{ __('Numero de Identificación') }}</label>

                            <div class="col-md-6">
                                <input id="idn" type="text" class="form-control{{ $errors->has('idn') ? ' is-invalid' : '' }}" name="idn" value="{{ $idn ?? old('idn') }}" required autofocus>

                                @if ($errors->has('idn'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('idn') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Reset Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
