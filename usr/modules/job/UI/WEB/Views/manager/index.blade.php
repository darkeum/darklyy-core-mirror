@extends('job::manager.body')

@section('title', __('job::general.list'))

@section('content')
    <grid:job::indexgrid />
@endsection


@push('scripts')
    <script>
        @if(session()->has('message'))
            Swal.fire({
                icon: 'success',
                title: 'Отлично!',
                html: "{{ session()->get('message') }}",
                timer: 2000,
                timerProgressBar: true,
            });
        @endif
    </script>
@endpush