<a href="#" class="btn d-flex align-items-center link-copy {{ $class }}" data-url="{{ (($link->domain->name ?? config('app.url')) . '/' . $link->alias) }}" data-enable="tooltip-copy" title="{{ __('Copy') }}" data-copy="{{ __('Copy') }}" data-copied="{{ __('Copied') }}">
    @include('icons.copy_link', ['class' => 'fill-current width-4 height-4'])&#8203;
</a>