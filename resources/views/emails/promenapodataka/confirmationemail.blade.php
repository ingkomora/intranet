@component('vendor.mail.html.message_promena_podataka')
## Vaš zahtev za promenu ličnih podataka je obrađen.


@component('mail::table')

| Podatak        | Ažuriran                      | Već je ažuran                   |
| :--------------| :---------------------------- | :------------------------------ |
@foreach($data->fields as $field => $value)
|**{{$field}}:** | {{$value['azurirano'] ?? ''}} | {{$value['neazurirano'] ?? ''}} |
@endforeach

@endcomponent

@endcomponent
