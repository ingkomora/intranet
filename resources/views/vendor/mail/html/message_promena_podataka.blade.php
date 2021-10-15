@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => 'http://www.ingkomora.rs'])
            Inženjerska komora Srbije
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
            © {{ date('Y') }} <a href="http://www.ingkomora.rs/kontakt">IKS::Služba za informacione tehnologije</a>.
        @endcomponent
    @endslot
@endcomponent
