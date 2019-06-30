@extends('layouts.app')

@section('content')
<div class="container">
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">{{ __('Reinciiar Contraseña') }}</div>

            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="form-group row">
                        <label for="idn_type" class="col-md-4 col-form-label text-md-right">{{ __('Tipo de Identificación') }}</label>

                        <div class="col-md-6">
                            <select
                                id="idn_type"
                                class="form-control{{ ($errors->has('idn') || $errors->has('idn_type') )? ' is-invalid' : '' }}"
                                name="idn_type" required autofocus>
                                <option value="CI" @if(old('idn_type')=='CI') selected @endif>CI</option>
                                <option value="RUT" @if(old('idn_type')=='RUT') selected @endif>RUT</option>
                                <option value="PASSPORT" @if(old('idn_type')=='PASSPORT') selected @endif>PASSPORT</option>
                                <option value="DNI" @if(old('idn_type')=='DNI') selected @endif>DNI</option>
                                <option value="RIF" @if(old('idn_type')=='RIF') selected @endif>RIF</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idn" class="col-md-4 col-form-label text-md-right">{{ __('Número de Identificación') }}</label>

                        <div class="col-md-6">
                            <input id="idn" type="idn" class="form-control{{ ($errors->has('idn_type') || $errors->has('idn') )? ' is-invalid' : '' }}" name="idn" value="{{ old('idn') }}" required>

                            @if ($errors->has('idn_type')||$errors->has('idn'))
                            <span class="invalid-feedback" role="alert">
                                    <strong>No conseguimos ningun usuario que coincida con estos datos</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Enviar link de reinicio al correo') }}
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
