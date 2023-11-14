<style>
    h6,
    .h6 {
        font-size: 13px;
        color: #423748;
    }

    h5,
    .h5,
    h6,
    .h6 {
        margin-top: 18px;
        margin-bottom: 18px;
    }

    h1,
    h2,
    h3,
    h5,
    h6,
    .h1,
    .h2,
    .h3,
    .h5,
    .h6 {
        font-family: inherit;
        font-weight: 400;
        line-height: 1.1;
        color: inherit;
        text-rendering: optimizelegibility;
    }
</style>

<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <div class="row mb-1">
                <div class="col-md-6 border-bottom">
                    <h2 class="pb-2">Данные по заявке</h2>
                </div>
                <div class="col-md-6 text-end border-bottom">
                    {{-- <a href="/administration/module/request/view/{{ $suborder->request->id }}" class="btn btn-outline-primary">Перейти к расчету</a> --}}
                </div>
            </div>
            <div class="row">
                <small>Тип:</small><br />
                @switch($suborder->r1)
                    @case(1)
                        <b>Повысительный хвс/гвс</b>
                    @break

                    @case(2)
                        <b>Станция пожаротушения</b>
                    @break

                    @case(3)
                        <b>Пропиточная система отопления</b>
                    @break

					 @case(4)
                        <b>Другое назначение</b>
                    @break

                    @default
                           <b>Не указано</b>
                @endswitch
                <br /><br />
                <small>Напор:</small><br />
                <b>{{ $suborder->napor }}</b>
                <br /><br />
                <small>Расход:</small><br />
                <b>{{ $suborder->rashod }}</b>
                <br /><br />

                <small>Кол-во насосв:</small><br />
                <b>{{ $suborder->sum }}</b>
                <br /><br />
                <small>Кол-во резервных:</small><br />
                <b>{{ $suborder->sumr }}</b>
                <br /><br />

                <small>Точка на нескольких насосов:</small><br />
                <b>{{ $suborder->tocha }}</b>
                <br /><br />
                <small>ПЧ:</small><br />
                 @switch($suborder->r1)
                    @case(1)
                        <b>Насосы со строенным ПЧ</b>
                    @break

                    @case(2)
                        <b>Шкаф управления с ПЧ</b>
                    @break

                    @case(3)
                        <b>Шкаф управления "Прямой пуск"</b>
                    @break

					

                    @default
                           <b>Не указано</b>
                @endswitch
                <br /><br />
            </div>
        </div>
    </div>
</div>
