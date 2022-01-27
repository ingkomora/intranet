@component('vendor.mail.html.message_iks')

@component('mail::panel')
{{$data->osoba->isMale() ? 'Poštovani' : 'Poštovana'}} {{$data->osoba->full_name}},\
Vaš zahtev za promenu ličnih podataka je obrađen.
@endcomponent

@component('mail::table')

| Podatak        | Ažuriran                      | Već je ažuran                   |
| :--------------| :---------------------------- | :------------------------------ |
@foreach($data->fields as $field => $value)
|**{{$field}}:** | {{$value['azurirano'] ?? ''}} | {{$value['neazurirano'] ?? ''}} |
@endforeach

@endcomponent

@endcomponent
