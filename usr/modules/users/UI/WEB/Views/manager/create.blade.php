@extends('users::manager.body')

@section('title', ($unit ?? false) ? 'Редактировать' : 'Создать')

{{--@section('header')--}}
{{--@endsection--}}

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
                            {{ ($unit ?? false) ? 'Изменить' : 'Создать' }} пользователя
                        </h4>
                    </div>
                    <div class="card-body">
                        @include('users::manager.layouts.form')
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

{{--  --}}
{{--@section('vendor-style')--}}
{{--@endsection--}}
{{--  --}}

{{--  --}}
{{--@section('vendor-script')--}}
{{--@endsection--}}
{{--  --}}

{{--  --}}
{{--@section('page-style')--}}
{{--@endsection--}}
{{--  --}}

{{--  --}}
{{--@section('page-script')--}}
{{--@endsection--}}
{{--  --}}

{{-- Пуш стилей --}}
{{--@push('styles')--}}
{{--@endpush--}}
{{-- Пуш стилей --}}

{{-- Пуш скриптов --}}
{{--@push('scripts')--}}
{{--@endpush--}}
{{-- Пуш скриптов --}}