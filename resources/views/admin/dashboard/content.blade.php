@extends('layouts.app')

@section('site_title', formatTitle([__('Dashboard'), config('settings.title')]))

@section('content')
<div class="bg-base-1 flex-fill">
    @include('admin.dashboard.header')
    <div class="bg-base-1">
        <div class="container py-3 my-3">
            <h4 class="mb-0">{{ __('Overview') }}</h4>

            <div class="row mb-5">
                @php
                    $cards = [
                        'users' =>
                        [
                            'title' => 'Users',
                            'value' => $stats['users'],
                            'route' => 'admin.users',
                            'icon' => 'icons.users'
                        ],
                        [
                            'title' => 'Subscriptions',
                            'value' => $stats['subscriptions'],
                            'route' => 'admin.subscriptions',
                            'icon' => 'icons.subscription'
                        ],
                        [
                            'title' => 'Plans',
                            'value' => $stats['plans'],
                            'route' => 'admin.plans',
                            'icon' => 'icons.package'
                        ],
                        [
                            'title' => 'Pages',
                            'value' => $stats['pages'],
                            'route' => 'admin.pages',
                            'icon' => 'icons.pages'
                        ],
                        [
                            'title' => 'Links',
                            'value' => $stats['links'],
                            'route' => 'admin.links',
                            'icon' => 'icons.link'
                        ],
                        [
                            'title' => 'Spaces',
                            'value' => $stats['spaces'],
                            'route' => 'admin.spaces',
                            'icon' => 'icons.space'
                        ],
                        [
                            'title' => 'Domains',
                            'value' => $stats['domains'],
                            'route' => 'admin.domains',
                            'icon' => 'icons.domain'
                        ],
                        [
                            'title' => 'Pixels',
                            'value' => $stats['pixels'],
                            'route' => 'admin.pixels',
                            'icon' => 'icons.pixel'
                        ]
                    ];
                @endphp

                @foreach($cards as $card)
                    <div class="col-12 col-md-6 col-xl-3 mt-3">
                        <div class="card border-0 shadow-sm h-100 overflow-hidden">
                            <div class="card-body d-flex">
                                <div class="d-flex position-relative text-primary width-10 height-10 align-items-center justify-content-center flex-shrink-0">
                                    <div class="position-absolute bg-primary opacity-10 top-0 right-0 bottom-0 left-0 border-radius-35"></div>
                                    @include($card['icon'], ['class' => 'fill-current width-5 height-5'])
                                </div>

                                <div class="flex-grow-1"></div>

                                <div class="d-flex align-items-center h2 font-weight-bold mb-0 text-truncate">
                                    {{ number_format($card['value'], 0, __('.'), __(',')) }}
                                </div>
                            </div>
                            <div class="card-footer bg-base-2 border-0">
                                <a href="{{ route($card['route']) }}" class="text-muted font-weight-medium d-inline-flex align-items-baseline">{{ __($card['title']) }} @include((__('lang_dir') == 'rtl' ? 'icons.chevron_left' : 'icons.chevron_right'), ['class' => 'width-3 height-3 fill-current '.(__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2')])</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <h4 class="mb-0">{{ __('Activity') }}</h4>
            <div class="row">
                <div class="col-12 col-xl-6 mt-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header align-items-center">
                            <div class="row">
                                <div class="col"><div class="font-weight-medium py-1">{{ __('Latest users') }}</div></div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(count($users) == 0)
                                {{ __('No data') }}.
                            @else
                                <div class="list-group list-group-flush my-n3">
                                    @foreach($users as $user)
                                        <div class="list-group-item px-0">
                                            <div class="row align-items-center">
                                                <div class="col text-truncate">
                                                    <div class="text-truncate">
                                                        <div class="d-flex align-items-center">
                                                            <img src="{{ gravatar($user->email, 48) }}" class="rounded-circle width-4 height-4 {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}">

                                                            <div class="text-truncate">
                                                                <a href="{{ route('admin.users.edit', $user->id) }}" class="text-truncate">{{ $user->name }}</a>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <div class="width-4 flex-shrink-0 {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}"></div>
                                                            <div class="text-muted text-truncate small">
                                                                {{ $user->email }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-outline-primary btn-sm">{{ __('Edit') }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        @if(count($users) > 0)
                            <div class="card-footer bg-base-2 border-0">
                                <a href="{{ route('admin.users') }}" class="text-muted font-weight-medium d-flex align-items-center justify-content-center">{{ __('View all') }} @include((__('lang_dir') == 'rtl' ? 'icons.chevron_left' : 'icons.chevron_right'), ['class' => 'width-3 height-3 fill-current '.(__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2')])</a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-xl-6 mt-3">
                    @if(config('settings.stripe'))
                        <div class="card border-0 shadow-sm">
                            <div class="card-header align-items-center">
                                <div class="row">
                                    <div class="col"><div class="font-weight-medium py-1">{{ __('Latest subscriptions') }}</div></div>
                                </div>
                            </div>
                            <div class="card-body">
                                @if(count($subscriptions) == 0)
                                    {{ __('No data') }}.
                                @else
                                    <div class="list-group list-group-flush my-n3">
                                        @foreach($subscriptions as $subscription)
                                            <div class="list-group-item px-0">
                                                <div class="row align-items-center">
                                                    <div class="col text-truncate">
                                                        <div class="text-truncate">
                                                            <div class="d-flex align-items-center">
                                                                <img src="{{ gravatar($subscription->user->email, 48) }}" class="rounded-circle width-4 height-4 {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}">

                                                                <div class="text-truncate">
                                                                    <a href="{{ route('admin.users.edit', $subscription->user->id) }}">{{ $subscription->user->name }}</a>

                                                                    <div class="badge badge-{{ formatStripeStatus()[$subscription->stripe_status]['status'] }}">{{ formatStripeStatus()[$subscription->stripe_status]['title'] }}</div>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex align-items-center">
                                                                <div class="width-4 flex-shrink-0 {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}"></div>
                                                                <div class="text-muted text-truncate small">
                                                                    <a href="{{ route('admin.subscriptions.edit', $subscription->id) }}" class="text-secondary">{{ $subscription->name }}</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <a href="{{ route('admin.subscriptions.edit', $subscription->id) }}" class="btn btn-outline-primary btn-sm">{{ __('Edit') }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            @if(count($subscriptions) > 0)
                                <div class="card-footer bg-base-2 border-0">
                                    <a href="{{ route('admin.subscriptions') }}" class="text-muted font-weight-medium d-flex align-items-center justify-content-center">{{ __('View all') }} @include((__('lang_dir') == 'rtl' ? 'icons.chevron_left' : 'icons.chevron_right'), ['class' => 'width-3 height-3 fill-current '.(__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2')])</a>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="card border-0 shadow-sm">
                            <div class="card-header align-items-center">
                                <div class="row">
                                    <div class="col"><div class="font-weight-medium py-1">{{ __('Latest links') }}</div></div>
                                </div>
                            </div>

                            <div class="card-body">
                                @if(count($links) == 0)
                                    {{ __('No data') }}.
                                @else
                                    <div class="list-group list-group-flush my-n3">
                                        @foreach($links as $link)
                                            <div class="list-group-item px-0">
                                                <div class="row align-items-center">
                                                    <div class="col d-flex text-truncate">
                                                        <div class="{{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}"><img src="https://icons.duckduckgo.com/ip3/{{ parse_url($link->url)['host'] }}.ico" rel="noreferrer" class="width-4 height-4"></div>

                                                        <div class="text-truncate">
                                                            <a href="{{ route('stats.overview', $link->id) }}" class="{{ ($link->disabled || $link->expiration_clicks && $link->clicks >= $link->expiration_clicks || \Carbon\Carbon::now()->greaterThan($link->ends_at) && $link->ends_at ? 'text-danger' : 'text-primary') }}" dir="ltr">{{ str_replace(['http://', 'https://'], '', (($link->domain->name ?? config('app.url')) .'/'.$link->alias)) }}</a>

                                                            <div class="text-dark text-truncate small">
                                                                <span class="text-secondary cursor-help" data-toggle="tooltip-url" title="{{ $link->url }}">@if($link->title){{ $link->title }}@else<span dir="ltr">{{ str_replace(['http://', 'https://'], '', $link->url) }}</span>@endif</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto d-flex">
                                                        @include('shared.buttons.copy_link', ['class' => 'btn-sm text-primary'])
                                                        @include('shared.dropdowns.link', ['class' => 'btn-sm text-primary '.(__('lang_dir') == 'rtl' ? 'mr-3' : 'ml-3'), 'admin' => true, 'options' => ['dropdown' => ['button' => true, 'edit' => true, 'share' => true, 'stats' => true, 'preview' => true, 'open' => true, 'delete' => true]]])
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            @if(count($links) > 0)
                                <div class="card-footer bg-base-2 border-0">
                                    <a href="{{ route('admin.links') }}" class="text-muted font-weight-medium d-flex align-items-center justify-content-center">{{ __('View all') }} @include((__('lang_dir') == 'rtl' ? 'icons.chevron_left' : 'icons.chevron_right'), ['class' => 'width-3 height-3 fill-current '.(__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2')])</a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@include('shared.modals.share_link')
@include('shared.modals.delete_link')

@include('admin.sidebar')
@endsection