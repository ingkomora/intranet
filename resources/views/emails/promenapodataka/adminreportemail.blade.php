@component('vendor.mail.html.message_promena_podataka')
# Backpack/Promena podataka

@component('mail::table')
|                 |                                                                                          |
| --------------- | ---------------------------------------------------------------------------------------- |
| **Zahtev:**     | {{$data->zahtev->id}}                                                                    |
| **Podnosilac:** | {{$data->osoba->ime}} {{$data->zahtev->prezime}}                                         |
|                 | {{$data->osoba->kontaktemail}}                                                           |
| **Opis:**       | Nakon uspešnog ažuriranja podataka, konfirmacioni imejl nije poslat podnosiocu zahteva.  |
@endcomponent

@endcomponent
