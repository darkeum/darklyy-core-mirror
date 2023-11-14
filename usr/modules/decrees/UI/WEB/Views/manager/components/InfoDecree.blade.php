<div class="">
    @include('elements.preloader', ['wire' => true])
    <div class="col-12">
        <div class="row">
            <div class="col-9">
                <label for="post" class="form-label">Должность</label>
                <input type="text" @disabled($dis == true) name="post" id="post" class="form-control text-center" placeholder="Введите должность" wire:model.debounce.1000ms="post">
            </div>
            <div class="col-3">
                <label for="days" class="form-label">Дни</label>
                <input type="number" @disabled($dis == true) name='days' id="days" class="form-control text-center" placeholder="Введите дней в пути" wire:model.debounce.1000ms="date_transit">
            </div>
        </div>       

        <div class="col-12 mt-1 mb-1">  
            <label for="target" class="form-label">Цель поездки</label>              
            <input type="text" @disabled($dis == true) name='target' id='target' class="form-control" placeholder="Введите цель" wire:model.debounce.1000ms="target">
        </div>
    </div>

    @foreach($inputs as $key => $value)
        <div class="mb-1 border border-2 p-1 rounded-3 position-relative">
            <div class="row d-flex justify-content-between">
                <div class="col-6">
                    <label for="city{{$value}}" class="form-label">Город</label> 
                    <input @disabled($dis == true)  required type="text" id="city{{$value}}" class="form-control" placeholder="Город №{{$key+1}}" wire:model.debounce.1000ms="city.{{$value}}">
                    <input  value="{{ $data['city'][$value] ?? null }}" hidden name='citys[]'>
                </div>
                <div class="col-6">
                    <label for="org{{$value}}" class="form-label">Организация</label> 
                    <input @disabled($dis == true) required type="text" id="org{{$value}}" class="form-control" placeholder="Организация №{{$key+1}}" wire:model.debounce.1000ms="org.{{$value}}">
                    <input wire.model="data.org.{{$value}}" value="{{ $data['org'][$value] ?? null }}" hidden name='orgs[]'>
                </div>
            </div>                        
            <div class="col-12 mt-1">
                <label for="result{{$value}}" class="form-label">Результат поездки</label> 
                <textarea @disabled($dis == true) id="result{{$value}}" class="form-control" placeholder="Результат поездки №{{$key+1}}" wire:model.debounce.1000ms="short_report.{{$value}}"></textarea>
                <input value="{{ $data['short_report'][$value] ?? null }}" hidden name='reports[]'>
            </div>
            @if($key != 0)
                <div class="position-absolute top-0 start-100 translate-middle">
                    <a @disabled($dis == true) class="" wire:click.prevent="remove({{$key}})"><i class="fa-solid fa-trash-can fa-lg"></i></a>
                </div>
            @endif
        </div>
    @endforeach
    <div class="row">
        <div class="d-flex justify-content-between">
            <div class="col-12">
                <button @disabled($dis == true) class="btn text-white btn-info btn-sm w-100" wire:click.prevent="add({{ $i }})">Добавить город</button>
            </div>
        </div>
    </div>


</div>

@push('scripts')
<script>

    Livewire.on('success', event => {
        Swal.fire({
            position: 'bottom-end',
            icon: 'success',
            title: 'Сохранено',
            showConfirmButton: false,
            timer: 1500
        })
    })
    </script> 
@endpush