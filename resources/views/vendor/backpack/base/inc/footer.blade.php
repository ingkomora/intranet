@if (config('backpack.base.show_powered_by') || config('backpack.base.developer_link'))
    <div class="row text-muted ml-auto mr-auto">
            @if (config('backpack.base.show_app_version'))
        <div class="col-12 text-center">
                <span class="badge badge-warning">{{ APP_VERSION }}</span>
        </div>
            @endif
        <div class="col-12 text-center">
            @if (config('backpack.base.developer_link') && config('backpack.base.developer_name'))
                <a target="_blank" rel="noopener" href="{{ config('backpack.base.developer_link') }}">{{ config('backpack.base.developer_name') }}</a>.
            @endif
            @if (config('backpack.base.show_powered_by'))
                {{ trans('backpack::base.powered_by') }} <a target="_blank" rel="noopener" href="http://backpackforlaravel.com?ref=panel_footer_link">Backpack for Laravel</a>.
            @endif
        </div>
    </div>
@endif
