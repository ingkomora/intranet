@php
    // preserve backwards compatibility with Widgets in Backpack 4.0
    $widget['wrapper']['class'] = $widget['wrapper']['class'] ?? $widget['wrapperClass'] ?? 'col-sm-6 col-md-4';
@endphp

@includeWhen(!empty($widget['wrapper']), 'backpack::widgets.inc.wrapper_start')
<div class="card {{$widget['class'] ?? ''}}">
    @if (isset($widget['content']))
        @if (isset($widget['content']['header']))
            <div class="card-header h2 bg-warning text-center">{!! $widget['content']['header'] !!}</div>
        @endif
        <div class="card-body m-0 p-0">
            <table class="table table-sm">
                <thead>
                <tr>
                    <th>Naziv i opis</th>
                    <th>Lokacija objekta</th>
                    <th>Investitor</th>
                    <th>Pravno lice/preduzetnik</th>
                    <th>Period izrade</th>
                    <th>Uloga podnosioca prijave</th>
                    <th>Odgovorno lice</th>
                </tr>
                </thead>
                <tbody>
                @foreach($widget['content']['body'] as $referenca)
                    <tr>
                        <td>{{$referenca->naziv}}</td>
                        <td>{{$referenca->lokacijamesto}}, {{$referenca->lokacijaopstina}}, {{$referenca->lokacijadrzava}} / {{$referenca->lokacijaadresa}}</td>
                        <td>{{$referenca->investitor}}</td>
                        <td>{{$referenca->firma}}</td>
                        <td>{{$referenca->godinaizrade}}</td>
                        <td>{{$referenca->ulogaId->naziv}}</td>
                        <td>{{$referenca->data_reference_to_array}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @if (isset($widget['footer']))
            <div class="card-footer">
                @if (isset($widget['footer']['button_link']))
                    <a class="btn btn-primary" href="{{ $widget['footer']['button_link'] }}" role="button">{{ $widget['footer']['button_text'] }}</a>
                @else
                    <p class="card-text">{!! $widget['footer'] !!}</p>

                @endif
            </div>
        @endif
    @endif
</div>
@includeWhen(!empty($widget['wrapper']), 'backpack::widgets.inc.wrapper_end')
