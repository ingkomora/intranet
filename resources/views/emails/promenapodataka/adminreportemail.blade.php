@component('vendor.mail.html.message_iks')
# Backpack/Promena podataka

@component('mail::table')
|                 |                                                                                          |
| --------------- | ---------------------------------------------------------------------------------------- |
| **Zahtev:**     | {{$data->zahtev->id}}                                                                    |
| **Podnosilac:** | {{$data->osoba->ime}} {{$data->zahtev->prezime}}                                         |
|                 | {{$data->osoba->kontaktemail}}                                                           |
| **Opis:**       | Nakon uspešnog ažuriranja podataka, konfirmacioni imejl nije poslat podnosiocu zahteva.  |
@if($data->error)
| **Error:**      | {{$data->error['message']}}                                                              |
@endif
@endcomponent

@endcomponent
