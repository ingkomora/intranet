@component('vendor.mail.html.message_iks')

# Backpack/MembershipApproving

@component('mail::table')
|                 |                                                                                          |
| :-------------- | :--------------------------------------------------------------------------------------- |
| **Zahtev:**     | *{{$data->id}} *                                                                         |
| **Podnosilac:** | *{{$data->osoba->full_name}}*                                                            |
|                 | *{{$data->osoba->kontaktemail}}*                                                         |
| **Opis:**       | *Nakon uspešnog odobrenja članstva, konfirmacioni mejl nije poslat podnosiocu zahteva.*  |
@if($error)
| **Greška:**     | *{{$error['message']}}*                                                                  |
@endif
@endcomponent
@endcomponent
