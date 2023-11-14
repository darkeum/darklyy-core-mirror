<div class="col-xl-4 col-md-6 col-12">

    <div class="card card-congratulation-medal">
        <div class="card-body">
            <h5>
                @lang('home::rating.title', ['name' => Auth::user()->name])
            </h5>
            <p class="card-text font-small-3 mb-25">
                @lang('home::rating.text', ['place' => $salesRating])
            </p>
            <h3 class="mb-75 text-primary">
                {{ $salesCompleteTotal }}
            </h3>
            <a href="{{ route('admin.sales.index') }}" class="btn btn-primary">@lang('home::rating.go')</a>

            @if ($salesRating == 1)
                <img src="{{ asset('images/illustration/ae-badge-g.svg') }}"
                     class="congratulation-medal"
                     alt="Atri Medal Gold"
                     title="@lang('home::rating.medals.title', ['medal' => trans('home::rating.medals.gold'), 'count' => $salesRating])"
                />
            @elseif($salesRating == 2)
                <img src="{{ asset('images/illustration/ae-badge-s.svg') }}"
                     class="congratulation-medal"
                     alt="Atri Medal Silver"
                     title="@lang('home::rating.medals.title', ['medal' => trans('home::rating.medals.silver'), 'count' => $salesRating])"
                />
            @elseif($salesRating == 3)
                <img src="{{ asset('images/illustration/ae-badge-b.svg') }}"
                     class="congratulation-medal"
                     alt="Atri Medal Bronze"
                     title="@lang('home::rating.medals.title', ['medal' => trans('home::rating.medals.bronze'), 'count' => $salesRating])"
                />
            @elseif($salesRating >= 4 and $salesRating <= 50)
                <span class="congratulation-medal"
                      title="@lang('home::rating.medals.title', ['medal' => trans('home::rating.medals.other'), 'count' => $salesRating])"
                >
                 <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 77 132"><path d="M56.5 0L32 62h19.1L77 0z" fill-rule="evenodd" fill="#a9a9a9"/><g transform="translate(7 58)"><defs><filter id="A" filterUnits="userSpaceOnUse" x=".3" y=".2" width="65.4" height="73.8"><feColorMatrix values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 1 0"/></filter></defs><mask maskUnits="userSpaceOnUse" x=".3" y=".2" width="65.4" height="73.8" id="B"><g filter="url(#A)"><path d="M.3.2h65.4v73.7H.3z" fill-rule="evenodd" fill="#fff"/></g></mask><linearGradient id="C" gradientUnits="userSpaceOnUse" x1="-913.195" y1="542.48" x2="-913.592" y2="541.651" gradientTransform="matrix(65.3575 0 0 -73.7945 59731.0352 40040.3203)"><stop offset=".006" stop-color="silver"/><stop offset="1" stop-color="#d3d3d3"/></linearGradient><path d="M20.3 11V5.6A1.58 1.58 0 0 1 21.9 4h22.2a1.58 1.58 0 0 1 1.6 1.6V11C41.8 9.3 37.5 8.4 33 8.4s-8.8.9-12.7 2.6m29.2 1.9V5.6c0-3-2.4-5.4-5.4-5.4H21.9c-3 0-5.4 2.4-5.4 5.4v7.3C6.8 18.6.3 29.1.3 41.2.3 59.3 14.9 74 33 74c18 0 32.7-14.7 32.7-32.8 0-12.1-6.5-22.6-16.2-28.3" mask="url(#B)" fill-rule="evenodd" fill="url(#C)"/></g><linearGradient id="D" gradientUnits="userSpaceOnUse" x1="-903.131" y1="597.972" x2="-903.73" y2="597.215" gradientTransform="matrix(54 0 0 -53 48826 31771)"><stop offset="0" stop-color="#a9a9a9"/><stop offset="1" stop-color="gray"/></linearGradient><g fill-rule="evenodd"><path d="M40 126c-14.9 0-27-11.9-27-26.5S25.1 73 40 73s27 11.9 27 26.5S54.9 126 40 126" fill="url(#D)"/><path d="M0 0h22.8L52 63H30.6z" fill="gray"/></g><text x="52%" y="76.5%" dominant-baseline="middle" text-anchor="middle" fill="#fff" font-size="27">{{ $salesRating }}</text></svg>
             </span>
            @else

                {{-- Ни чего не выводить --}}

            @endif

        </div>
    </div>
</div>
