
@extends('orgs::manager.body')

@section('title', __('orgs::general.manager'))

@section('content')
    <div class="row">
        
        <livewire:orgs::infouser :unit="$unit" field="uid" />      
        <livewire:orgs::infouser :unit="$unit" field="director" />
        <livewire:orgs::infouser :unit="$unit" field="accountant" />
        <livewire:orgs::infoorg  :unit="$unit"  />
        
    </div>
@endsection




