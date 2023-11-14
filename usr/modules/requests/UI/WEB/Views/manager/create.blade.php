@extends('requests::manager.body')

@section('title', ($unit ?? false) ? 'Редактировать' : 'Создать')

@section('content')

    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            {{ ($unit ?? false) ? 'Изменить' : 'Создать' }} заявку
                        </h4>
                    </div>
                    <div class="card-body">
                        @include('requests::manager.layouts.form')
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection