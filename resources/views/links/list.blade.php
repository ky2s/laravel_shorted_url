@section('site_title', formatTitle([__('Links'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('dashboard'), 'title' => __('Home')],
    ['title' => __('Links')],
]])

<div class="d-flex">
    <div class="flex-grow-1">
        <h2 class="mb-0 d-inline-block">{{ __('Links') }}</h2>
    </div>
</div>

@include('links.new')

<div class="card border-0 shadow-sm mt-3">
    <div class="card-header align-items-center">
        <div class="row">
            <div class="col"><div class="font-weight-medium py-1">{{ __('Links') }}</div></div>
            <div class="col-auto">
                <div class="form-row">
                    <div class="col">
                        <form method="GET" action="{{ route('links') }}" class="d-md-flex">
                            <div class="input-group input-group-sm">
                                <input class="form-control" name="search" placeholder="{{ __('Search') }}" value="{{ app('request')->input('search') }}">
                                <div class="input-group-append">
                                    <button type="button" class="btn {{ request()->input('sort') || request()->input('space') || request()->input('domain') ? 'btn-primary' : 'btn-outline-primary' }} d-flex align-items-center dropdown-toggle dropdown-toggle-split reset-after" data-enable="tooltip" title="{{ __('Filters') }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@include('icons.filter', ['class' => 'fill-current width-4 height-4'])&#8203;</button>
                                    <div class="dropdown-menu {{ (__('lang_dir') == 'rtl' ? 'dropdown-menu' : 'dropdown-menu-right') }} border-0 shadow width-64" id="search-filters">
                                        <div class="dropdown-header py-1">
                                            <div class="row">
                                                <div class="col"><div class="font-weight-medium m-0 text-dark text-truncate">{{ __('Filters') }}</div></div>
                                                <div class="col-auto">
                                                    @if(request()->input('sort') || request()->input('space') || request()->input('domain'))
                                                        <a href="{{ route('links') }}" class="text-secondary">{{ __('Reset') }}</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="dropdown-divider"></div>

                                        <div class="form-group px-4">
                                            <label for="i_domain" class="small">{{ __('Domain') }}</label>
                                            <select name="domain" id="i_domain" class="custom-select custom-select-sm">
                                                <option value="">{{ __('All') }}</option>
                                                @foreach($domains as $domain)
                                                    <option value="{{ $domain->id }}" @if(request()->input('domain') == $domain->id && request()->input('domain') !== null) selected @endif>{{ str_replace(['http://', 'https://'], '', $domain->name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group px-4">
                                            <label for="i_space" class="small">{{ __('Space') }}</label>
                                            <select name="space" id="i_space" class="custom-select custom-select-sm">
                                                <option value="">{{ __('All') }}</option>
                                                @foreach($spaces as $space)
                                                    <option value="{{ $space->id }}" @if(request()->input('space') == $space->id && request()->input('space') !== null) selected @endif>{{ $space->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group px-4">
                                            <label for="i_status" class="small">{{ __('Status') }}</label>
                                            <select name="status" id="i_status" class="custom-select custom-select-sm">
                                                @foreach([0 => __('All'), 1 => __('Active'), 2 => __('Expired'), 3 => __('Disabled')] as $key => $value)
                                                    <option value="{{ $key }}" @if(request()->input('type') == $key && request()->input('type') !== null) selected @endif>{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group px-4">
                                            <label for="i_by" class="small">{{ __('Search by') }}</label>
                                            <select name="by" id="i_by" class="custom-select custom-select-sm">
                                                @foreach(['title' => __('Title'), 'alias' => __('Alias'), 'url' => __('URL')] as $key => $value)
                                                    <option value="{{ $key }}" @if(request()->input('by') == $key || !request()->input('by') && $key == 'name') selected @endif>{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group px-4">
                                            <label for="i_sort" class="small">{{ __('Sort') }}</label>
                                            <select name="sort" id="i_sort" class="custom-select custom-select-sm">
                                                @foreach(['desc' => __('New'), 'asc' => __('Old'), 'max' => __('Best performing'), 'min' => __('Least performing')] as $key => $value)
                                                    <option value="{{ $key }}" @if(request()->input('sort') == $key) selected @endif>{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group px-4 mb-2">
                                            <button type="submit" class="btn btn-primary btn-sm btn-block">{{ __('Search') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-auto">
                        <button type="button" class="btn btn-sm btn-outline-primary d-flex align-items-center" data-toggle="modal" data-target="#exportModal" data-enable="tooltip" title="{{ __('Export') }}">@include('icons.export', ['class' => 'fill-current width-4 height-4'])&#8203;</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        @include('shared.message')

        @if(count($links) == 0)
            {{ __('No results found.') }}
        @else
            <div class="list-group list-group-flush my-n3">
                <div class="list-group-item px-0 text-muted">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="row align-items-center">
                                <div class="col-12 col-md-8 col-lg-6 d-flex text-truncate">
                                    {{ __('URL') }}
                                </div>

                                <div class="d-none d-md-block col-md-4 col-lg-2 text-truncate">
                                    {{ __('Space') }}
                                </div>

                                <div class="d-none d-lg-block col-lg-2 text-truncate">
                                    {{ __('Clicks') }}
                                </div>

                                <div class="d-none d-lg-block col-lg-2 text-truncate">
                                    {{ __('Created at') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-auto d-flex">
                            <button type="button" class="btn text-primary btn-sm d-flex align-items-center invisible {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}">@include('icons.copy_link', ['class' => 'fill-current width-4 height-4'])&#8203;</button>

                            <button type="button" href="#" class="btn text-primary d-inline-flex align-items-center invisible">@include('icons.horizontal_menu', ['class' => 'fill-current width-4 height-4'])</button>
                        </div>
                    </div>
                </div>

                @foreach($links as $link)
                    <div class="list-group-item px-0">
                        <div class="row align-items-center">
                            <div class="col text-truncate">
                                <div class="row align-items-center">
                                    <div class="col-12 col-md-8 col-lg-6 d-flex text-truncate">
                                        <div class="text-truncate">
                                            <div class="d-flex align-items-center">
                                                <img src="https://icons.duckduckgo.com/ip3/{{ parse_url($link->url)['host'] }}.ico" rel="noreferrer" class="width-4 height-4 {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}">

                                                <div class="text-truncate">
                                                    <a href="{{ route('stats.overview', $link->id) }}" class="{{ ($link->disabled || $link->expiration_clicks && $link->clicks >= $link->expiration_clicks || \Carbon\Carbon::now()->greaterThan($link->ends_at) && $link->ends_at ? 'text-danger' : 'text-primary') }}" dir="ltr">{{ str_replace(['http://', 'https://'], '', (($link->domain->name ?? config('app.url')) .'/'.$link->alias)) }}</a>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <div class="width-4 flex-shrink-0 {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}"></div>
                                                <div class="text-muted text-truncate small">
                                                    <span class="text-secondary cursor-help" data-toggle="tooltip-url" title="{{ $link->url }}">@if($link->title){{ $link->title }}@else<span dir="ltr">{{ str_replace(['http://', 'https://'], '', $link->url) }}</span>@endif</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-none d-md-flex align-items-center col-md-4 col-lg-2">
                                        @if(isset($link->space->name))
                                            <a href="{{ route('links', ['space' => $link->space->id]) }}" class="badge badge-{{ formatSpace()[$link->space->color] }} text-truncate">{{ $link->space->name }}</a>
                                        @else
                                            <div class="badge badge-secondary text-truncate">{{ __('None') }}</div>
                                        @endif
                                    </div>

                                    <div class="d-none d-lg-block col-lg-2 text-truncate">
                                        <a href="{{ route('stats.overview', $link->id) }}" dir="ltr" class="text-dark text-truncate">{{ number_format($link->clicks, 0, __('.'), __(',')) }}</a>
                                    </div>

                                    <div class="d-none d-lg-flex col-lg-2">
                                        <div class="text-truncate" data-enable="tooltip" title="{{ $link->created_at->tz(Auth::user()->timezone ?? config('app.timezone'))->format(__('Y-m-d') . ' H:i:s') }}">{{ $link->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto d-flex">
                                @include('shared.buttons.copy_link', ['class' => 'btn-sm text-primary'])
                                @include('shared.dropdowns.link', ['class' => 'btn-sm text-primary '.(__('lang_dir') == 'rtl' ? 'mr-3' : 'ml-3'), 'options' => ['dropdown' => ['button' => true, 'edit' => true, 'share' => true, 'stats' => true, 'preview' => true, 'open' => true, 'delete' => true]]])
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="mt-3 align-items-center">
                    <div class="row">
                        <div class="col">
                            <div class="mt-2 mb-3">{{ __('Showing :from-:to of :total', ['from' => $links->firstItem(), 'to' => $links->lastItem(), 'total' => $links->total()]) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            {{ $links->onEachSide(1)->links() }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">{{ __('Export') }}</h6>
                <button type="button" class="close d-flex align-items-center justify-content-center width-12 height-14" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="d-flex align-items-center">@include('icons.close', ['class' => 'fill-current width-3 height-3'])</span>
                </button>
            </div>
            <div class="modal-body">
                @can('dataExport', ['App\Link', $userFeatures['option_data_export']])
                    {{ __('Are you sure you want to export this table?') }}
                @else
                    @if(config('settings.stripe'))
                        @include('shared.features.locked')
                    @else
                        @include('shared.features.unavailable')
                    @endif
                @endcan
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                @can('dataExport', ['App\Link', $userFeatures['option_data_export']])
                    <a href="{{ route('links.export', Request::query()) }}" target="_self" class="btn btn-primary" id="exportButton">{{ __('Export') }}</a>
                @endcan
            </div>
        </div>
    </div>
</div>
<script>
    'use strict';

    window.addEventListener('DOMContentLoaded', function () {
        jQuery('#exportButton').on('click', function () {
            jQuery('#exportModal').modal('hide');
        });
    });
</script>

@include('shared.modals.share_link')
@include('shared.modals.delete_link')