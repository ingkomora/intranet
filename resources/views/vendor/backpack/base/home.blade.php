@extends(backpack_view('blank'))

@php

    $widgets['before_content'][] = [
        'type'        => 'jumbotron',
        'heading'     => trans('backpack::base.welcome'),
        'content'     => trans('backpack::base.use_sidebar'),
        'button_link' => backpack_url('logout'),
        'button_text' => trans('backpack::base.logout'),
    ];
     $widgets['before_content'][] = [
        'type'       => 'div',
        'class'      => 'row',
        'content'    => [
             [
                'type'       => 'card',
                //'wrapper' => ['class' => 'col-sm-6 col-md-4'], // optional
                'class'   => 'h-100', // optional
                'content' => [
                    'header' => 'Pretraga osoba', // optional
                    'body'   => 'Pretraga i ažuriranje podataka o osobama koje su prijavili stručni ispit, podneli zahtev za licencu, članovi komore ili su samo upisani u Registre koje vodi IKS',
                ],
                'footer' => [
                     'button_link' => backpack_url('osoba'),
                     'button_text' => trans('backpack::custom.search'),
                ]
             ],
             [
                'type'       => 'card',
                //'wrapper' => ['class' => 'col-sm-6 col-md-4'], // optional
                'class'   => 'h-100', // optional
                'content' => [
                    'header' => 'Pretraga prijava za stručne ispite', // optional
                    'body'   => 'Pretraga novih prijava za stručne ispite, od broja 20693',
                ],
                'footer' => [
                     'button_link' => backpack_url('prijava'),
                     'button_text' => trans('backpack::custom.search'),
                ]
             ],
             [
                'type'       => 'card',
                //'wrapper' => ['class' => 'col-sm-6 col-md-4'], // optional
                'class'   => 'h-100', // optional
                'content' => [
                    'header' => 'Stare prijave za stručne ispite', // optional
                    'body'   => 'Pretraga starih prijava za stručne ispite do broja 20693',
                ],
                'footer' => [
                     'button_link' => backpack_url('prijavasistara'),
                     'button_text' => trans('backpack::custom.search'),
                ]
             ],
         ]
     ];
@endphp

@section('content')
@endsection
