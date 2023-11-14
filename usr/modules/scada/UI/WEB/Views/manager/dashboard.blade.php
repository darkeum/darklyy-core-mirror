@extends('scada::manager.body')

@section('title', __('scada::general.dashboard'))

@section('content')

 
    <livewire:scada::uptime />    
    <div class="row match-height">
        <livewire:scada::crash />
        <livewire:scada::actions />
    </div>
@endsection
