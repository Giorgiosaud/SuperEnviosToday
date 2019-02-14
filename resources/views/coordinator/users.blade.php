@extends('layouts.app')

@section('content')
    <div class="container mx-auto">
        <div class="col-12">
            <h1>usuarios</h1>
            <table class="table-auto w-full">
                <tr>
                    <th class="p-3">
                        Nombre
                    </th>
                    <th class="p-3">
                        Apellido
                    </th>
                    <th class="p-3">
                        Tipo de Identificacion
                    </th>
                    <th class="p-3">
                        Numero de Identificacion
                    </th>
                    <th class="p-3">
                        e-mail
                    </th>
                    <th class="p-3">
                        Dirección
                    </th>
                    <th class="p-3">
                        Telefono
                    </th>
                </tr>
                @foreach ($users as $user)
                    <tr class="border ">
                        <td class="p-3">
                            {{ $user->name }}
                        </td>
                        <td class="p-3">
                            {{ $user->last_name }}
                        </td>
                        <td class="p-3">
                            {{ $user->idn_type}}
                        </td>
                        <td class="p-3">
                            {{ $user->idn}}
                        </td>
                        <td class="p-3">

                            {{ $user->email}}
                        </td>
                        <td class="p-3">
                            {{ $user->address}}
                        </td>
                        <td class="p-3">
                            {{ $user->phone}}
                        </td>
                    </tr>
                @endforeach
            </table>
            {{ $users->links() }}
        </div>
    </div>
@endsection
