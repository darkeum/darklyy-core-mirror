@extends('developers::manager.body')

{{--@section('title', __('changelog::lang.edit'))--}}

@section('content')

    <div class="card">
        <div class="card-body">
            <h3>@lang('developers::git.result', ['command' => $command]):</h3>
            <hr>

            {{-- null --}}
            @if($output == 'null')
                <div class="alert alert-secondary" role="alert">
                    <div class="alert-body d-flex align-items-center">
                        <i data-feather="alert-circle"></i>
                        <span class="ms-1">@lang('developers::git.output.null')</span>
                    </div>
                </div>
            @endif
            {{-- null --}}

            {{-- error --}}
            @if($output == 'error')
                <div class="alert alert-danger" role="alert">
                    <div class="alert-body d-flex align-items-center">
                        <i data-feather='alert-octagon'></i>
                        <span class="ms-1">@lang('developers::git.output.error')</span>
                    </div>
                </div>
            @endif
            {{-- error --}}

            {{-- result --}}
            @if($output !== 'null' and $output !== 'error')
                <div class="alert alert-dark" role="alert">
                    <div class="alert-body">
                        <pre class="m-0 p-1 rounded">{{ $output }}</pre>
                    </div>
                </div>
            @endif
            {{-- result --}}

            <hr>
            <h3>@lang('developers::git.command'):</h3>
            <div class="btn-group">
                <a href="?git_action=status" class="btn btn-info">@lang('developers::git.command.status')</a>
                <a href="?git_action=pull" class="btn btn-primary">@lang('developers::git.command.pull')</a>
                <a href="?git_action=add" class="btn btn-secondary">@lang('developers::git.command.add')</a>
                <a href="?git_action=push" class="btn btn-success">@lang('developers::git.command.push')</a>
                <a href="?git_action=commit" class="btn btn-warning">@lang('developers::git.command.commit')</a>
                <a href="?git_action=resethard" class="btn btn-danger">@lang('developers::git.command.resethard')</a>
            </div>
            <hr>
            <h3>@lang('developers::git.help'):</h3>
            <p><span class="text-info"><strong>@lang('developers::git.command.status'):</strong></span> @lang('developers::git.command.status.help')</p>
            <p><span class="text-primary"><strong>@lang('developers::git.command.pull'):</strong></span> @lang('developers::git.command.pull.help')</p>
            <p><span class="text-secondary"><strong>@lang('developers::git.command.add'):</strong></span> @lang('developers::git.command.add.help')</p>
            <p><span class="text-success"><strong>@lang('developers::git.command.push'):</strong></span> @lang('developers::git.command.push.help')</p>
            <p><span class="text-warning"><strong>@lang('developers::git.command.commit'):</strong></span> @lang('developers::git.command.commit.help')</p>
            <p><span class="text-danger"><strong>@lang('developers::git.command.resethard'):</strong></span> @lang('developers::git.command.resethard.help')</p>
        </div>
    </div>
@endsection
