<div class="row">
    <div class="col-12">
        <div class="card m-0">
            <div class="card-header d-flex justify-content-between align-items-center px-0 py-50">

                <div class="d-flex align-items-center">
                    <div class="fw-bold fs-2 me-1">Договор :</div>
                    <a class="btn btn-sm btn-icon btn-success waves-effect cp" onclick="reestr_module.show();">
                        <i data-feather="plus"></i>
                    </a>
                </div>

            </div>
            <div class="">
                <ul class="list-group">
                    <li class="list-group-item d-flex align-items-center">
                        <span>
                            {!! $pact !!}
                        </span>
                        {{--<a class="cp ms-auto" title="Убрать"><i data-feather="x" class="font-medium-2"></i></a>--}}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>