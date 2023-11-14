@extends('app')

@section('content')
    <div id="wrapper">
        <div class="container">

            <div class="row">

                @include('page::pages.' . Request::segment(2))

            </div>

        </div>
@endsection