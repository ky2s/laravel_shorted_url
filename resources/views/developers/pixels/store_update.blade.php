@php
    $parameters = [
        [
            'name' => 'name',
            'type' => $type,
            'format' => 'string',
            'description' => __('The pixel name.')
        ],
        [
            'name' => 'type',
            'type' => $type,
            'format' => 'string',
            'description' => __('The pixel type.') . ' ' . __('Possible values are: :values.', ['values' => '<code>'.implode('</code>, <code>', array_keys(config('pixels'))).'</code>'])
        ],
        [
            'name' => 'pixel_id',
            'type' => $type,
            'format' => 'string',
            'description' => __('The pixel ID.')
        ]
    ];
@endphp

@include('developers.parameters')