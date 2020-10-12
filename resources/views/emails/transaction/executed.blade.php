@component('mail::message')
# Hola {{$client->name}} {{$client->last_name}} tu transacción fue completada

@component('mail::panel')
    A continuación el detalle de la transacción
@endcomponent
@component('mail::table')
    | Transaccion   | Monto         | Fecha  |
    | ------------- |:-------------:| --------:|
    | Extranjera          | {{$foreignTransaction->amount}} {{$foreignTransaction->account->bank->currency->sign}}       |  {{$foreignTransaction->created_at}}     |
    | Bs      | {{$venezuelanTransaction->amount}} BsS | {{$venezuelanTransaction->updated_at}}      |
@endcomponent
Gracias,<br>
{{ config('app.name') }}
@endcomponent
