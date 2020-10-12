@php
    // preserve backwards compatibility with Widgets in Backpack 4.0
    $widget['wrapper']['class'] = $widget['wrapper']['class'] ?? $widget['wrapperClass'] ?? 'col-sm-6 col-md-4';
@endphp

@includeWhen(!empty($widget['wrapper']), 'backpack::widgets.inc.wrapper_start')
<div class="card {{$widget['class'] ?? ''}}">
    @if (isset($widget['content']))
        @if (isset($widget['content']['header']))
            <div class="card-header h2">{!! $widget['content']['header'] !!}</div>
        @endif
        <div class="card-body">{!! $widget['content']['body'] !!}</div>
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