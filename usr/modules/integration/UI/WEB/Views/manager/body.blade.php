@extends('layouts.base')

@section('header')
    @include('elements.header', [
        'title' => __('integration::general.title'),
        'buttons' => [
            __('integration::general.index') => 'admin.integration.index',
            __('integration::general.matching') => [
                __('integration::general.matching.category') => 'admin.integration.matching.category',
                __('integration::general.matching.attribute') => 'admin.integration.matching.attribute',
                __('integration::general.matching.values') => 'admin.integration.matching.values',
            ],
        ],
    ])
@endsection
