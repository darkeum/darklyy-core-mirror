@extends('layouts.base')

@section('title')
    @lang('order::general.order')
@endsection

@section('content')
    @include('order::manager.header')
    <style>
        .h4,
        h4 {
            font-size: 1.2rem;
        }
    </style>
    <div id="content" class="">
        <div class="row">
            <div class="col-md-12 mb-1">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 border-bottom">
                                <h2 class="pb-2">Заявка #{{ $order->id }}, от {{ $order->created_at }}</h2>
                            </div>
                            <div class="col-md-6 text-end border-bottom">
                                <div class="btn-group" role="group">
                                    <button id="btnGroupDrop1" type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Ожидает исполнения</button>
                                    <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                        <li><a class="dropdown-item" href="#">В работе</a></li>
                                        <li><a class="dropdown-item" href="#">Выполнена</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3 mt-3 d-flex align-items-start">
                                    <div class="me-1  fa-2x">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    <div>
                                        <h4 class="fw-bold mb-0">{{ $order->surname }} {{ $order->name }} {{ $order->ochestvo }}</h4>
                                        <p>ФИО заявителя</p>
                                    </div>
                                </div>
                                <div class="mb-3 mt-3 d-flex align-items-start">
                                    <div class="me-1  fa-2x">
                                        <i class="fa-solid fa-envelope"></i>
                                    </div>
                                    <div>
                                        <h4 class="fw-bold mb-0">{{ $order->email }}</h4>
                                        <p>E-mail заявителя</p>
                                    </div>
                                </div>
                                <div class="mb-3 mt-3 d-flex align-items-start">
                                    <div class="me-1  fa-2x">
                                        <i class="fa-solid fa-phone"></i>
                                    </div>
                                    <div>
                                        <h4 class="fw-bold mb-0">{{ $order->phone }}</h4>
                                        <p>Телефон заявителя</p>
                                    </div>
                                </div>
                                <div class="mb-3 mt-3 d-flex align-items-start">
                                    <div class="me-1  fa-2x">
                                        <i class="fa-solid fa-gear"></i>
                                    </div>
                                    <div>
                                        <h4 class="fw-bold mb-0">{{ $order_title }}</h4>
                                        <p>Тип заявителя</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3 mt-3 d-flex align-items-start">
                                    <div class="me-1  fa-2x">
                                        <i class="fa-solid fa-gear"></i>
                                    </div>
                                    <div>
                                        <h4 class="fw-bold mb-0">
                                            @if (!empty($order->org_name))
                                                {{ $order->org_name }}
                                            @else
                                                Не указано
                                            @endif
                                        </h4>
                                        <p>Название организация</p>
                                    </div>
                                </div>
                                <div class="mb-3 mt-3 d-flex align-items-start">
                                    <div class="me-1  fa-2x">
                                        <i class="fa-solid fa-gear"></i>
                                    </div>
                                    <div>
                                        <h4 class="fw-bold mb-0">
                                            @if (!empty($order->org_inn))
                                                {{ $order->org_inn }}
                                            @else
                                                Не указано
                                            @endif
                                        </h4>
                                        <p>ИНН Организация</p>
                                    </div>
                                </div>
                                <div class="mb-3 mt-3 d-flex align-items-start">
                                    <div class="me-1  fa-2x">
                                        <i class="fa-solid fa-gear"></i>
                                    </div>
                                    <div>
                                        <h4 class="fw-bold mb-0">
                                            @if (!empty($order->object_name))
                                                {{ $order->object_name }}
                                            @else
                                                Не указано
                                            @endif
                                        </h4>
                                        <p>Объект</p>
                                    </div>
                                </div>
                                <div class="mb-3 mt-3 d-flex align-items-start">
                                    <div class="me-2  fa-2x">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </div>
                                    <div>
                                        <h4 class="fw-bold mb-0">
                                            @if (!empty($order->object_address))
                                                {{ $order->object_address }}
                                            @else
                                                Не указано
                                            @endif
                                        </h4>
                                        <p>Адрес объекта</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 mt-3 d-flex align-items-start">
                                    <div class="me-1  fa-2x">
                                        <i class="fa-solid fa-message"></i>
                                    </div>
                                    <div>
                                        <h4 class="fw-bold mb-0">Сообщение</h4>
                                        <p style="white-space: break-spaces;">{{ $order->message }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('order::manager.suborder.' . $order->type)
        </div>
    </div>
@endsection
