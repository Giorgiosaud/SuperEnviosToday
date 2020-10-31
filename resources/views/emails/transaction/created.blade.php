@component('mail::message')
# Hola {{$client->name}} {{$client->last_name}} tu transacción fue creada
@component('mail::panel')
Se ha comenzado a procesar tu orden por un monto de
{{$foreignTransaction->amount}} {{$foreignTransaction->account->bank->currency->identifier}} equivalentes a
{{$venezuelanTransaction->amount}} BsS, y estará listo en un maximo de 6 horas.
se realizara la transferencia a la cuenta {{$venezuelanTransaction->account->number}}
del banco {{$venezuelanTransaction->account->bank->name}}
a nombre de {{$venezuelanTransaction->client->name}} {{$venezuelanTransaction->client->last_name}}
id: {{$venezuelanTransaction->client->idn_type}}-{{$venezuelanTransaction->client->idn}}
tipo: {{$venezuelanTransaction->account->type}}
@endcomponent
@component('mail::table')
@endcomponent
Gracias,<br>
{{ config('app.name') }}
@endcomponent
