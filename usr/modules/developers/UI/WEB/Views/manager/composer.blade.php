@extends('developers::manager.body')

{{--@section('title', __('changelog::lang.edit'))--}}

@section('content')

    <div id="content" class="animated fadeIn">
        <div class="card">
            <div class="card-body">
                <h3>@lang('developers::composer.result', ['command' => $command]):</h3>
                <hr>

                {{-- null --}}
                @if($output == 'null')
                    <div class="alert alert-secondary" role="alert">
                        <div class="alert-body d-flex align-items-center">
                            <i data-feather="alert-circle"></i>
                            <span class="ms-1">@lang('developers::composer.output.null')</span>
                        </div>
                    </div>
                @endif
                {{-- null --}}

                {{-- error --}}
                @if($output == 'error')
                    <div class="alert alert-danger" role="alert">
                        <div class="alert-body d-flex align-items-center">
                            <i data-feather='alert-octagon'></i>
                            <span class="ms-1">@lang('developers::composer.output.error')</span>
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
                <h3>@lang('developers::composer.command'):</h3>
                <div class="btn-group">
                    <a href="?composer_action=about" class="btn btn-info">@lang('developers::composer.command.about')</a>
                    <a href="?composer_action=update" class="btn btn-primary">@lang('developers::composer.command.update')</a>
                    <a href="?composer_action=install" class="btn btn-secondary">@lang('developers::composer.command.install')</a>
                    <a href="?composer_action=dump-autoload" class="btn btn-success">@lang('developers::composer.command.dump-autoload')</a>
                </div>
                <hr />
                <h3>@lang('developers::composer.help'):</h3>
                <p><span class="text-info"><strong>@lang('developers::composer.command.about'):</strong></span> @lang('developers::composer.command.about.help')</p>
                <p><span class="text-primary"><strong>@lang('developers::composer.command.update'):</strong></span> @lang('developers::composer.command.update.help')</p>
                <p><span class="text-secondary"><strong>@lang('developers::composer.command.install'):</strong></span> @lang('developers::composer.command.install.help')</p>
                <p>
                    <span class="text-success"><strong>@lang('developers::composer.command.dump-autoload'):</strong></span>
                    @lang('developers::composer.command.dump-autoload.help')
                </p>
            </div>
        </div>
    </div>
@endsection
