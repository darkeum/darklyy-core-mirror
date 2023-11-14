{{-- Общая информация --}}
<div class="card-body invoice-padding pb-0">

    {{-- Header starts --}}
    <div class="d-flex justify-content-between flex-md-row flex-column invoice-spacing mt-0">
        <div>
            <div class="logo-wrapper">
                @include('layouts.svg.' . check_company(true))
                <h3 class="text-primary invoice-logo">
                    @lang('app.name_' . check_company(true))
                </h3>
            </div>
            <p class="card-text mb-25">
                @lang('app.address')
            </p>
            <p class="card-text mb-25">
                @lang('app.email')
            </p>
            <p class="card-text mb-0">
                @lang('app.phone_num_text')
            </p>
        </div>
        <div class="mt-md-0 mt-2">
            <h4 class="invoice-title">
                Объект
                <span class="invoice-number">#{{ $object->id }}</span>
            </h4>
            <div class="invoice-date-wrapper">
                <p class="invoice-date-title">Старт работ:</p>
                <p class="invoice-date">
                    {{ \Carbon\Carbon::parse($object['date'])->format('d.m.Y') }}
                </p>
            </div>
            <div class="invoice-date-wrapper">
                <p class="invoice-date-title">Конец работ:</p>
                <p class="invoice-date">
                    {{ \Carbon\Carbon::parse($object['date'])->format('d.m.Y') }}
                </p>
            </div>
        </div>
    </div>
    {{-- Header ends --}}

    <h2 class="text-center">
        {{ $object['name'] }}
    </h2>

</div>

<hr class="invoice-spacing"/>

{{-- Основная информация по объекту --}}
<div class="card-body invoice-padding pt-0">
    <div class="row invoice-spacing">
        <div class="col-xl-12 p-0">
            <h6 class="text-center mb-1">Основная информация по объекту</h6>
            {{--<span class="hr-text my-0 mb-1 fs-5 text-default">--}}
            {{--    <span>Основная информация по объекту</span>--}}
            {{--</span>--}}
            {{--<h6 class="mb-25">{{ $object['name'] }}</h6>--}}
            <p class="card-text mb-25">
                    <span class="h6">
                        Наименование объекта:
                    </span>
                {{ $object['name'] }}
            </p>
            <p class="card-text mb-25">
                    <span class="h6">
                        Адрес объекта:
                    </span>
                {{ $object['address'] }}
            </p>

            {{---}}
            <p class="card-text mb-25">
                <span class="h6">
                    Тип объекта:
                </span>
                {{ $object['cat'] }}
            </p>
            <p class="card-text mb-25">
                <span class="h6">
                    Статус объекта:
                </span>
                {{ $object['status'] }}
            </p>
            <p class="card-text mb-25">
                <span class="h6">
                    Закрепление объекта:
                </span>
                object_no_info
            </p>
            <p class="card-text mb-25">
                <span class="h6">
                    Клиенты:
                </span>
                {{ $object['id_client'] }}
            </p>
            <p class="card-text mb-25">
                <span class="h6">
                    Застройщик:
                </span>
                {{ $object['id_zas'] }}
            </p>
            <p class="card-text mb-25">
                <span class="h6">
                    Ген. подрядчик:
                </span>
                {{ $object['id_pod'] }}
            </p>
            <p class="card-text mb-25">
                <span class="h6">
                    Ген. проектировщик:
                </span>
                {{ $object['id_gpr'] }}
            </p>
            <p class="card-text mb-0">
                <span class="h6">
                    Суб. проектировщик:
                </span>
                {{ $object['id_spr'] }}
            </p>
            {{----}}

        </div>

        {{---}}
        <div class="col-xl-3 p-0 mt-xl-0 mt-2">
            <h6 class="text-center mb-2">Информация по разделам</h6>
            <p class="card-text mb-25">
                <span class="me-50">
                    <input class="form-check-input" type="checkbox" id="" value="checked" checked=""
                           disabled>
                </span>
                Раздел ТС
            </p>
            <p class="card-text mb-25">
                <span class="me-50">
                    <input class="form-check-input" type="checkbox" id="" value="checked" checked=""
                           disabled>
                </span>
                Раздел ОВ
            </p>
            <p class="card-text mb-25">
                <span class="me-50">
                    <input class="form-check-input" type="checkbox" id="" value="checked" checked=""
                           disabled>
                </span>
                Раздел ВК
            </p>
            <p class="card-text mb-0">
                <span class="me-50">
                    <input class="form-check-input" type="checkbox" id="" value="checked" checked=""
                           disabled>
                </span>
                Раздел ТМ
            </p>
        </div>
        {{----}}

    </div>
</div>
{{-- Основная информация по объекту --}}

<hr class="invoice-spacing d-none"/>

{{-- Invoice Note starts -}}
<div class="card-body invoice-padding pt-0 d-none">
    <div class="row">
        <div class="col-12">
            <span class="fw-bold">Note:</span>
            <span>It was a pleasure working with you and your team. We hope you will keep us in mind for future freelance
                                        projects. Thank You!</span>
        </div>
    </div>
</div>
{{-- Invoice Note ends --}}

{{-- Общая информация --}}

{{--@push('styles')--}}
{{--@endpush--}}

{{--@push('scripts')--}}
{{--@endpush--}}