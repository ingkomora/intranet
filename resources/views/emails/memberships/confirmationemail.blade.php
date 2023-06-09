@component('vendor.mail.html.message_iks')

{{$data->osoba->isMale() ? 'Поштовани' : 'Поштована'}} {{$data->osoba->full_name}},

Обавештавамо Вас да је Управни одбор Инжењерске коморе Србије донео одлуку којом сте примљени у чланство Инжењерске коморе Србије.

Како је чланом 11. став 2. тачка 13. Статута Инжењерске коморе Србије прописано да су права и дужности чланова Коморе да уредно плаћају чланарину, потребно је да извршите исту.


@component('mail::table')
| Упутство за уплату чланарине|                               |
| :---------------------------| :---------------------------- |
| Износ за уплату:            | 9.500,00 динара               |
| Сврха уплате:               | Чланарина                     |
| Прималац:                   | Инжењерска комора Србије      |
| Текући рачун:               | 160-40916-33                  |
| Позив на број:              | 05- *уписати број лиценце*    |
@endcomponent

Комора је својим члановима обезбедила полису осигурања од професионалне одговорности.
Наиме, Законом о изменама и допунама Закона о планирању и изградњи („Службени гласник РС“, број 9/2020) прописано је да право на употребу професионалног назива, односно право на обављање стручних послова стиче се уписом активног статуса у Регистру лиценцираних инжењера, архитеката и просторних планера на основу важеће полисе осигурања од професионалне одговорности.

@component('mail::panel')
***Напомена***
*Одлука о пријему у чланство Инжењерске коморе Србије биће Вам послата поштом на Вашу адресу.*
@endcomponent

&nbsp;\
Како би у Регистру обезбедили активан статус, молимо Вас да у што краћем року извршите уплату чланарине.
Доказ о уплати чланарине можете доставити на имејл адресу: <a href="mailto:{{EMAIL_RACUNOVODSTVO}}">{{EMAIL_RACUNOVODSTVO}}</a>
&nbsp;

&nbsp;\
С поштовањем

СТРУЧНА СЛУЖБА ЗА ПОСЛОВЕ МАТИЧНИХ СЕКЦИЈА, СТРУЧНИХ ИСПИТА И УСАВРШАВАЊА
@endcomponent
