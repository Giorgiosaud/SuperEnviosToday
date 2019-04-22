@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="idn_type" class="col-md-4 col-form-label text-md-right">{{ __('Identification Type') }}</label>

                            <div class="col-md-6">
                                <select
                                    id="idn_type"
                                    class="form-control{{ $errors->has('idn_type') ? ' is-invalid' : '' }}"
                                    name="idn_type" required autofocus>
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
                            <label for="idn" class="col-md-4 col-form-label text-md-right">{{ __('Identification Number') }}</label>

                            <div class="col-md-6">
                                <input id="idn" type="idn" class="form-control{{ $errors->has('idn') ? ' is-invalid' : '' }}" name="idn" value="{{ old('idn') }}" required>

                                @if ($errors->has('idn'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('idn') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Send Password Reset Link') }}
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
