{{-- Универсальный wizard --}}
<section class="horizontal-wizard">
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="bs-stepper unit-wizard">

            <div class="bs-stepper-header">

                {{-- Шаг 1 --}}
                <div class="step" data-target="#unit-step-1" role="tab"
                     id="account-details-vertical-trigger">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-box">1</span>
                        <span class="bs-stepper-label">
                                <span class="bs-stepper-title">Основная информация</span>
                                <span class="bs-stepper-subtitle">Полная информация об объекте</span>
                            </span>
                    </button>
                </div>
                <div class="line">
                    <i data-feather="chevron-right" class="font-medium-2"></i>
                </div>
                {{-- Шаг 1 --}}

                {{-- Шаг 2 --}}
                <div class="step" data-target="#unit-step-2" role="tab"
                     id="personal-info-vertical-trigger">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-box">2</span>
                        <span class="bs-stepper-label">
                                <span class="bs-stepper-title">Карта</span>
                                <span class="bs-stepper-subtitle">Координаты объекта</span>
                            </span>
                    </button>
                </div>
                <div class="line">
                    <i data-feather="chevron-right" class="font-medium-2"></i>
                </div>
                {{-- Шаг 2 --}}

                {{-- Шаг 3 --}}
                <div class="step" data-target="#unit-step-3" role="tab"
                     id="social-links-vertical-trigger">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-box">3</span>
                        <span class="bs-stepper-label">
                                <span class="bs-stepper-title">@lang('support::form.attaches')</span>
                                <span class="bs-stepper-subtitle">@lang('support::form.attaches.description')</span>
                            </span>
                    </button>
                </div>
                <div class="line">
                    <i data-feather="chevron-right" class="font-medium-2"></i>
                </div>
                {{-- Шаг 3 --}}

                {{-- Шаг 4 --}}
                <div class="step" data-target="#unit-step-4" role="tab"
                     id="address-step-vertical-trigger">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-box">4</span>
                        <span class="bs-stepper-label">
                                <span class="bs-stepper-title">Разделы</span>
                                <span class="bs-stepper-subtitle">Какие то разделы</span>
                            </span>
                    </button>
                </div>
                {{-- Шаг 4 --}}

            </div>

            <div class="bs-stepper-content">

                {{-- Шаг 1 --}}
                <div id="unit-step-1" class="content" role="tabpanel"
                     aria-labelledby="account-details-vertical-trigger">
                    <div class="content-header">
                        <h5 class="mb-0">Шаг 1</h5>
                        <small class="text-muted">Шаг 1</small>
                    </div>

                    <div class="row">
                        <div class="mb-1 col-md-6">
                            <label class="form-label" for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-control" placeholder="johndoe">
                        </div>
                        <div class="mb-1 col-md-6">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="john.doe@email.com" aria-label="john.doe">
                        </div>
                    </div>

                    <div class="mt-2 d-flex justify-content-between unit-step-actions">
                        <button type="button" class="btn btn-outline-secondary btn-prev" disabled>
                            <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>
                            <span class="align-middle d-sm-inline-block d-none">@lang('objects::main.wizard.back')</span>
                        </button>
                        <button type="button" class="btn btn-primary btn-next">
                            <span class="align-middle d-sm-inline-block d-none">@lang('objects::main.wizard.next')</span>
                            <i data-feather="arrow-right" class="align-middle ms-sm-25 ms-0"></i>
                        </button>
                    </div>

                </div>
                {{-- Шаг 1 --}}

                {{-- Шаг 2 --}}
                <div id="unit-step-2" class="content" role="tabpanel"
                     aria-labelledby="personal-info-vertical-trigger">
                    <div class="content-header">
                        <h5 class="mb-0">Шаг 2</h5>
                        <small class="text-muted">Шаг 2</small>
                    </div>

                    <div class="row">
                        <div class="mb-1 col-md-6">
                            <label class="form-label" for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-control" placeholder="johndoe">
                        </div>
                        <div class="mb-1 col-md-6">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="john.doe@email.com" aria-label="john.doe">
                        </div>
                    </div>

                    <div class="mt-2 d-flex justify-content-between unit-step-actions">
                        <button type="button" class="btn btn-outline-secondary btn-prev">
                            <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>
                            <span class="align-middle d-sm-inline-block d-none">@lang('objects::main.wizard.back')</span>
                        </button>
                        <button type="button" class="btn btn-primary btn-next">
                            <span class="align-middle d-sm-inline-block d-none">@lang('objects::main.wizard.next')</span>
                            <i data-feather="arrow-right" class="align-middle ms-sm-25 ms-0"></i>
                        </button>
                    </div>

                </div>
                {{-- Шаг 2 --}}

                {{-- Шаг 3 --}}
                <div id="unit-step-3" class="content" role="tabpanel"
                     aria-labelledby="social-links-vertical-trigger">
                    <div class="content-header">
                        <h5 class="mb-0">Шаг 3</h5>
                        <small class="text-muted">Шаг 3</small>
                    </div>

                    <div class="row">
                        <div class="mb-1 col-md-6">
                            <label class="form-label" for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-control" placeholder="johndoe">
                        </div>
                        <div class="mb-1 col-md-6">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="john.doe@email.com" aria-label="john.doe">
                        </div>
                    </div>

                    <div class="mt-2 d-flex justify-content-between unit-step-actions">
                        <button type="button" class="btn btn-outline-secondary btn-prev">
                            <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>
                            <span class="align-middle d-sm-inline-block d-none">@lang('objects::main.wizard.back')</span>
                        </button>
                        <button type="button" class="btn btn-primary btn-next">
                            <span class="align-middle d-sm-inline-block d-none">@lang('objects::main.wizard.next')</span>
                            <i data-feather="arrow-right" class="align-middle ms-sm-25 ms-0"></i>
                        </button>
                    </div>

                </div>
                {{-- Шаг 3 --}}

                {{-- Шаг 4 --}}
                <div id="unit-step-4" class="content" role="tabpanel"
                     aria-labelledby="address-step-vertical-trigger">
                    <div class="content-header">
                        <h5 class="mb-0">Шаг 4</h5>
                        <small class="text-muted">Шаг 4</small>
                    </div>

                    <div class="row">
                        <div class="mb-1 col-md-6">
                            <label class="form-label" for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-control" placeholder="johndoe">
                        </div>
                        <div class="mb-1 col-md-6">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="john.doe@email.com" aria-label="john.doe">
                        </div>
                    </div>

                    <div class="mt-2 d-flex justify-content-between unit-step-actions">
                        <button type="button" class="btn btn-outline-secondary btn-prev">
                            <i data-feather="arrow-left" class="align-middle me-sm-25 me-0"></i>
                            <span class="align-middle d-sm-inline-block d-none">@lang('objects::main.wizard.back')</span>
                        </button>
                        <button class="btn btn-success btn-submit">@lang('objects::main.wizard.send')</button>
                    </div>

                </div>
                {{-- Шаг 4 --}}

            </div>

        </div>
    </form>
</section>
{{-- Универсальный wizard --}}


@push('scripts')
    <!-- Page js files -->
    <script data-title="wizard">

        let bsStepper = document.querySelectorAll('.bs-stepper');
        if (typeof bsStepper !== undefined && bsStepper !== null) {
            for (let el = 0; el < bsStepper.length; ++el) {
                bsStepper[el].addEventListener('show.bs-stepper', function (event) {
                    let index = event.detail.indexStep;
                    let numberOfSteps = $(event.target).find('.step').length - 1;
                    let line = $(event.target).find('.step');
                    for (let i = 0; i < index; i++) {
                        line[i].classList.add('crossed');

                        for (let j = index; j < numberOfSteps; j++) {
                            line[j].classList.remove('crossed');
                        }
                    }
                    if (event.detail.to == 0) {
                        for (let k = index; k < numberOfSteps; k++) {
                            line[k].classList.remove('crossed');
                        }
                        line[0].classList.remove('crossed');
                    }
                });
            }
        }
        let unitWizard = document.querySelector('.unit-wizard');
        if (typeof unitWizard !== undefined && unitWizard !== null) {
            let unitStepper = new Stepper(unitWizard, {
                linear: false
            });
            $(unitWizard)
                .find('.btn-next')
                .on('click', function () {
                    unitStepper.next();
                });
            $(unitWizard)
                .find('.btn-prev')
                .on('click', function () {
                    unitStepper.previous();
                });
        }

    </script>
@endpush