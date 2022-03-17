@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => 'http://www.ingkomora.rs'])
            Инжењерска комора Србије
        @endcomponent
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} <a href="http://www.ingkomora.rs/kontakt">ИКС::Служба за информационе технологије</a>.
        @endcomponent
    @endslot
@endcomponent
