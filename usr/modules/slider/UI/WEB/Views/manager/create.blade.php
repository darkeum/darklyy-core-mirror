@extends('slider::manager.body')

@if($id ?? false)
    @section('title', 'Редактировать')
@else
    @section('title', 'Создать')
@endif

@section('content')

{{--    @if ($errors->any())--}}
{{--        <div class="alert alert-danger">--}}
{{--            <ul>--}}
{{--                @foreach ($errors->all() as $error)--}}
{{--                    <li>{{ $error }}</li>--}}
{{--                @endforeach--}}
{{--            </ul>--}}
{{--        </div>--}}
{{--    @endif--}}

    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            @if($unit ?? false)
                                Изменить слайд
                            @else
                                Создать слайд
                            @endif
                        </h4>
                    </div>
                    <div class="card-body">
                        @include('slider::manager.layouts.form')
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

{{-- Пуш стилей --}}
{{--@push('styles')--}}
{{--@endpush--}}
{{-- Пуш стилей --}}

{{-- Пуш скриптов --}}
{{--@push('scripts')--}}
{{--@endpush--}}
{{-- Пуш скриптов --}}