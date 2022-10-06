<style>
    form .select2-container--bootstrap .select2-selection--multiple .select2-selection__choice {
        margin-top: 6px;
    }

    .select2-container--bootstrap .select2-selection--multiple .select2-selection__choice {
        color: #555;
        background: #fff;
        border: 1px solid #ccc;
        border-radius: 4px;
        cursor: default;
        float: left;
        margin: 5px 0 0 6px;
        padding: 0 6px;
    }

    .select2-container--bootstrap .select2-selection--multiple .select2-selection__choice {
        color: #555;
        background: #fff;
        border: 1px solid #ccc;
        border-radius: 4px;
        cursor: default;
        float: left;
        margin: 5px 0 0 6px;
        padding: 0 6px;
    }

    *, :after, :before {
        box-sizing: border-box;
    }

    li {
        display: list-item;
        text-align: -webkit-match-parent;
    }

    .select2-container--bootstrap .select2-selection--multiple .select2-selection__rendered {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        display: block;
        line-height: 1.42857143;
        list-style: none;
        margin: 0;
        overflow: hidden;
        padding: 0;
        width: 100%;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .select2-container .select2-selection--multiple .select2-selection__rendered {
        display: inline-block;
        overflow: hidden;
        padding-left: 8px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .select2-container--bootstrap .select2-selection--multiple .select2-selection__rendered {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        display: block;
        line-height: 1.42857143;
        list-style: none;
        margin: 0;
        overflow: hidden;
        padding: 0;
        width: 100%;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .select2-container .select2-selection--multiple .select2-selection__rendered {
        display: inline-block;
        overflow: hidden;
        padding-left: 8px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    ul {
        list-style-type: disc;
    }

    .select2-container--bootstrap .select2-selection {
        /*-webkit-box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%);*/
        /*box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%);*/
        /*background-color: #fff;*/
        border: 1px solid #ccc;
        border-radius: 4px;
        color: #555;
        font-size: 14px;
        outline: 0;
    }

    .select2-container .select2-selection--multiple {
        box-sizing: border-box;
        cursor: pointer;
        display: block;
        min-height: 32px;
        user-select: none;
        -webkit-user-select: none;
    }

    .select2-container--bootstrap .select2-selection {
        /*-webkit-box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%);*/
        /*box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%);*/
        /*background-color: #fff;*/
        border: 1px solid #ccc;
        border-radius: 4px;
        color: #555;
        font-size: 14px;
        outline: 0;
        background-color: #f8f9fa;
        opacity: 1;
    }

    .select2-container .select2-selection--multiple {
        box-sizing: border-box;
        cursor: pointer;
        display: block;
        min-height: 32px;
        user-select: none;
        -webkit-user-select: none;
    }

    .card {
        word-wrap: break-word;
        border: 1px solid #d9e2ef;
        border-radius: 0.25rem;
        display: flex;
        flex-direction: column;
        min-width: 0;
        position: relative;
    }

    body {
        -moz-osx-font-smoothing: grayscale;
        -webkit-font-smoothing: antialiased;
    }

    body {
        background-color: #f9fbfd;
        color: #1b2a4e;
        font-family: Source Sans Pro, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        margin: 0;
        text-align: left;
    }
</style>
<div class="row">
    <div class="col-md-8">

        <div class="card">
            <div class="card-header h2 bg-css3 text-white text-center">O KANDIDATU
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Ime (roditelj) prezime:</label>
                        <input type="text" value="{{!empty($widget['osoba']->ime) ? $widget['osoba']->ime_roditelj_prezime : 'Nema podataka' }}" disabled class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Zvanje:</label>
                        <input type="text" value="{{ !empty($widget['osoba']->zvanje) ? $widget['osoba']->zvanjeId->naziv : 'Nema podataka' }}" disabled class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Članstvo:</label>
                        <input type="text" value="{{ $widget['osoba']->clan == 1 ? 'Član IKS' : 'Nije član IKS'}}" disabled class="form-control"/>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Prebivalište:</label>
                        <input type="text" value="{{!empty($widget['osoba']->prebivalisteadresa) ? $widget['osoba']->data_prebivaliste_to_string : 'Nema podataka'}}" disabled class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Pošta:</label>
                        <input type="text" value="{{ !empty($widget['osoba']->ulica) ? $widget['osoba']->data_posta_to_string : 'Nema podataka'}}" disabled class="form-control">
                    </div>
                </div>

                @if($widget['osoba']->licence->isNotEmpty())
                    <div class="row">

                        <div class="form-group col-sm-12">
                            <label>Licence:</label>

                            <span class="select2 select2-container select2-container--bootstrap">
                                <span class="select2-selection select2-selection--multiple">
                                    <ul class="select2-selection__rendered">
                                        @foreach($widget['osoba']->data_licence_to_array as $licenca)
                                            <li class="select2-selection__choice">{{$licenca}}</li>
                                        @endforeach
                                    </ul>
                                </span>
                            </span>

                        </div>

                        {{--@if($widget['osoba']->aktivnaOsiguranja->isNotEmpty())
                            <div class="form-group col-sm-12">
                                <label>Osiguranje:</label>

                                <span class="select2 select2-container select2-container--bootstrap">
                                    <span class="select2-selection select2-selection--multiple">
                                        <ul class="select2-selection__rendered">
                                            @foreach($widget['osoba']->aktivnaOsiguranja as $osiguranje)
                                                <li class="select2-selection__choice">{{ $osiguranje->firmaUgovarac->naziv ?? $osiguranje->osobaUgovarac->full_name}} (do {{\Carbon\Carbon::parse($osiguranje->polisa_datum_zavrsetka)->format('d.m.Y')}})</li>
                                            @endforeach
                                        </ul>
                                    </span>
                                </span>

                            </div>
                        @endif--}}
                        {{--<div class="form-group col-sm-3">
                            <label>Registar:</label>

                            <span class="select2 select2-container select2-container--bootstrap">
                                <span class="select2-selection select2-selection--multiple">
                                    <ul class="select2-selection__rendered">
                                        <li>
                                            <a
                                                href="http://www.ingkomora.rs/clanovi/registar_pretraga.php?lib={{ $widget['osoba']->lib }}"
                                                target="_blank"
                                                title="Proveri osobu u Registru"
                                                class="select2-selection__choice"
                                            >
                                                <i class="nav-icon la la-book-open"></i> Pogledaj podatke ovde
                                            </a>
                                        </li>
                                    </ul>
                                </span>
                            </span>

                        </div>--}}
                    </div>
                @endif
                <div class="row">
                    <div class="form-group col-md-2">
                        <label>Sistem školovanja:</label>
                        <input type="text" value="{{ $widget['osoba']->bolonja ? 'Po Bolonji' : 'Pre Bolonje'}}" disabled class="form-control">
                    </div>
                    <div class="form-group col-md-5">
                        <label>Master studije:</label>
                        <input type="text" value="{{ !empty($widget['osoba']->mrfakultet) ? $widget['osoba']->data_master_studije_to_string : "Nema podataka" }}" disabled class="form-control">
                    </div>
                    <div class="form-group col-md-5">
                        <label>Osnovne studije:</label>
                        <input type="text" value="{{ !empty($widget['osoba']->diplfakultet) ? $widget['osoba']->data_osnovne_studije_to_string : "Nema podataka" }}" disabled class="form-control">
                    </div>
                </div>

                {{--@if($widget['osoba']->siPrijave->isNotEmpty())
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label>Prijave za polaganje SI:</label>

                            <span class="select2 select2-container select2-container--bootstrap">
                                <span class="select2-selection select2-selection--multiple">
                                    <ul class="select2-selection__rendered">
                                        @foreach($widget['osoba']->siPrijave as $prijava)
                                            <li class="select2-selection__choice">{{ $prijava->id }} ({{ $prijava->status->naziv }}), {{$prijava->vrstaPosla->naziv}}, {{$prijava->regPodOblast->naziv}}</li>
                                        @endforeach
                                    </ul>
                                </span>
                            </span>

                        </div>
                    </div>
                @endif
                @if($widget['osoba']->zahteviLicence->isNotEmpty())
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label>Zahtevi za licence:</label>

                            <span class="select2 select2-container select2-container--bootstrap">
                                <span class="select2-selection select2-selection--multiple">
                                    <ul class="select2-selection__rendered">
                                        @foreach($widget['osoba']->zahteviLicence as $zahtev)
                                            <li class="select2-selection__choice">{{ $zahtev->id }} ({{ $zahtev->statusId->naziv }}), {{$zahtev->vrstaPosla->naziv}}, {{$zahtev->regPodOblast->naziv}}</li>
                                        @endforeach
                                    </ul>
                                </span>
                            </span>

                        </div>
                    </div>
                @endif--}}

            </div>

        </div>
    </div>
</div>
