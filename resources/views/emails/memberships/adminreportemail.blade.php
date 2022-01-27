@component('vendor.mail.html.message_iks')
admin
{{$data->osoba->isMale() ? 'Poštovani' : 'Poštovana'}} {{$data->osoba->full_name}},

Obaveštavamo Vas da je Upravni odbor Inženjerske komore Srbije doneo odluku kojom ste primljeni u članstvo Inženjerske komore Srbije.

Kako je članom 11. stav 2. tačka 13. Statuta Inženjerske komore Srbije propisano da su prava i dužnosti članova Komore da uredno plaćaju članarinu, potrebno je da izvršite istu.


@component('mail::table')
| Uputstvo za uplatu članarine|                               |
| :---------------------------| :---------------------------- |
| Iznos za uplatu:            | 9.500,00 dinara               |
| Svrha uplate:               | Članarina                     |
| Primalac:                   | Inženjerska komora Srbije     |
| Tekući račun:               | 160-40916-33                  |
| Poziv na broj:              | 05- *upisati broj Licence*    |
@endcomponent

{{--@component('mail::promotion')--}}
Komora je svojim članovima koji redovno plaćaju godišnju članarinu obezbedila besplatnu polisu osiguranja od profesionalne odgovornosti.
Naime, Zakonom o izmenama i dopunama Zakona o planiranju i izgradnji („Službeni glasnik RS“, broj 9/2020) propisano je da pravo na upotrebu profesionalnog naziva, odnosno pravo na obavljanje stručnih poslova stiče se upisom aktivnog statusa u Registru licenciranih inženjera, arhitekata i prostornih planera na osnovu važeće polise osiguranja od profesionalne odgovornosti.
{{--@endcomponent--}}

@component('mail::panel')
***Napomena***
*Odluka o prijemu u članstvo Inženjerske komore Srbije biće Vam poslata poštom na Vašu adresu, kao i na adresu elektronske pošte.*
@endcomponent


S poštovanjem

Stručna služba za poslove matičnih sekcija
Inženjerska komora Srbije
@endcomponent
