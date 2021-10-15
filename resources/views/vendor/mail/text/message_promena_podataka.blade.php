@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header')
            Promena ličnih podataka
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
            © {{ date('Y') }} IKS::Služba za informacione tehnologije. @lang('All rights reserved.')
        @endcomponent
    @endslot
@endcomponent
