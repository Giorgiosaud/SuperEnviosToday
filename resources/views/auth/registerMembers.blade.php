@extends('layouts.app')

@section('content')
    <register-client :auth-user="{{Auth::user()}}" :roles="{{$roles}}">
    </register-client>
@endsection
