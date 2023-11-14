@extends('certificates::manager.body')

@section('title', "Менеджер сертификатов")

@section('content')
    
    <grid:certificates::indexgrid />

@endsection
