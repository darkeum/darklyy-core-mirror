<div class="col-lg-4 col-md-6 col-12">
    <div class="card card-transaction">
        <div class="card-header">
            <h4 class="card-title">@lang('home::transaction.transactions')</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li>
                        <a wire:click="reload"><i class="fa-light fa-rotate-right"></i></a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- preloader --}}
        @include('elements.preloader', ['wire' => true])
        {{-- preloader --}}

        <div class="card-body">

            {{-- Реализации --}}
            <div class="transaction-item">
                <div class="d-flex">
                    <div class="avatar bg-light-primary rounded float-start">
                        <div class="avatar-content">
                            <i class="fa-light fa-bag-shopping fa-xl"></i>
                        </div>
                    </div>
                    <div class="transaction-percentage">
                        <h6 class="transaction-title">@lang('home::transaction.realizations')</h6>
                        <small>@lang('home::transaction.count', ['count' => $realizationsCount ?: 0])</small>
                    </div>
                </div>
                <div class="fw-bolder text-success">{{ $realizationsTotal }}</div>
            </div>
            {{-- Реализации --}}

            {{-- Поступления --}}
            <div class="transaction-item">
                <div class="d-flex">
                    <div class="avatar bg-light-success rounded float-start">
                        <div class="avatar-content">
                            <i class="fa-light fa-receipt fa-xl"></i>
                        </div>
                    </div>
                    <div class="transaction-percentage">
                        <h6 class="transaction-title">@lang('home::transaction.receipts')</h6>
                        <small>@lang('home::transaction.count', ['count' => $receiptsCount ?: 0])</small>
                    </div>
                </div>
                <div class="fw-bolder text-success">{{ $receiptsTotal }}</div>
            </div>
            {{-- Поступления --}}

            {{-- Перемещения --}}
            <div class="transaction-item">
                <div class="d-flex">
                    <div class="avatar bg-light-danger rounded float-start">
                        <div class="avatar-content">
                            <i class="fa-light fa-people-carry-box fa-xl"></i>
                        </div>
                    </div>
                    <div class="transaction-percentage">
                        <h6 class="transaction-title">@lang('home::transaction.transfer')</h6>
                        <small>@lang('home::transaction.count', ['count' => $transferCount ?: 0])</small>
                    </div>
                </div>
                <div class="fw-bolder text-info">{{ $transferTotal }}</div>
            </div>
            {{-- Перемещения --}}

            {{-- Оплаты --}}
            <div class="transaction-item">
                <div class="d-flex">
                    <div class="avatar bg-light-warning rounded float-start">
                        <div class="avatar-content">
                            <i class="fa-light fa-ruble-sign fa-xl"></i>
                        </div>
                    </div>
                    <div class="transaction-percentage">
                        <h6 class="transaction-title">@lang('home::transaction.bank.in')</h6>
                        <small>@lang('home::transaction.count', ['count' => $bankInCount ?: 0])</small>
                    </div>
                </div>
                <div class="fw-bolder @if($bankInCount){{ 'text-success' }}@else{{ 'text-secondary' }}@endif">
                    @if($bankInCount){{ '+' }}@endif {{ $bankInTotal }}
                </div>
            </div>
            {{-- Оплаты --}}

            {{-- Списания --}}
            <div class="transaction-item">
                <div class="d-flex">
                    <div class="avatar bg-light-info rounded float-start">
                        <div class="avatar-content">
                            <i class="fa-light fa-money-check-pen fa-xl"></i>
                        </div>
                    </div>
                    <div class="transaction-percentage">
                        <h6 class="transaction-title">@lang('home::transaction.bank.out')</h6>
                        <small>@lang('home::transaction.count', ['count' => $bankOutCount ?: 0])</small>
                    </div>
                </div>
               <div class="fw-bolder @if($bankOutCount){{ 'text-danger' }}@else{{ 'text-secondary' }}@endif">
                   @if($bankOutCount){{ '-' }}@endif {{ $bankOutTotal }}
               </div>
            </div>
            {{-- Списания --}}

        </div>
    </div>
</div>
