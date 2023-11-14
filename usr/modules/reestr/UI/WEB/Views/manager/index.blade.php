@extends("reestr::manager.body")

@section('title', __('reestr::admin.reestr'))

@section('content')
    <grid:reestr::index />
@endsection
