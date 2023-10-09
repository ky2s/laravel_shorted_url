@section('site_title', formatTitle([__('New'), __('Pixel'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => isset($admin) ? route('admin.dashboard') : route('dashboard'), 'title' => isset($admin) ? __('Admin') : __('Home')],
    ['url' => isset($admin) ? route('admin.pixels') : route('pixels'), 'title' => __('Pixels')],
    ['title' => __('New')],
]])

<h2 class="mb-3 d-inline-block">{{ __('New') }}</h2>

<div class="card border-0 shadow-sm">
    <div class="card-header">
        <div class="row">
            <div class="col">
                <div class="font-weight-medium py-1">{{ __('Pixel') }}</div>
            </div>
        </div>
    </div>
    <div class="card-body">
        @include('shared.message')

        <form action="{{ route('pixels.new') }}" method="post" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="i_name">{{ __('Name') }}</label>
                <input type="text" name="name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" id="i_name" value="{{ old('name') }}">
                @if ($errors->has('name'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i_type">{{ __('Type') }}</label>
                <select name="type" id="i_type" class="custom-select{{ $errors->has('type') ? ' is-invalid' : '' }}">
                    @foreach(config('pixels') as $key => $value)
                        <option value="{{ $key }}" @if(old('type') == $key) selected @endif>{{ $value['name'] }}</option>
                    @endforeach
                </select>
                @if ($errors->has('type'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('type') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i_pixel_id">{{ __('Pixel ID') }}</label>
                <input type="text" name="pixel_id" class="form-control{{ $errors->has('pixel_id') ? ' is-invalid' : '' }}" id="i_pixel_id" value="{{ old('pixel_id') }}">
                @if ($errors->has('pixel_id'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('pixel_id') }}</strong>
                    </span>
                @endif
            </div>

            <button type="submit" name="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </form>
    </div>
</div>