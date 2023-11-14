@foreach($categories as $cat)
    <div class="col-lg-6 col-md-4 col-sm-12">
        <div class="featured-icon-box icon-align-top-content style4 bor_rad_5">
            <div class="bg_icon" style="top: 60px;">
                <i class="fad fa-{{ $cat['icon'] }}"></i>
            </div>
            <div class="featured-icon">
                <div class="cmt-icon cmt-icon_element-onlytxt cmt-icon_element-color-skincolor cmt-icon_element-size-lg">
                    <i class="fal fa-{{ $cat['icon'] }}"></i>
                </div>
            </div>
            <div class="featured-content">
                <div class="content-inner">
                    <div class="featured-title">
                        <h5>{{ $cat['name'] }}</h5>
                    </div>
                    <div class="featured-desc">
                        <p style="line-height: 1.2;">
                            {{ $cat['description'] }}
                        </p>
                    </div>
                </div>
                <a class="cmt-btn btn-inline cmt-btn-size-md cmt-icon-btn-left cmt-btn-color-skincolor"
                   href="{{ $cat['url'] }}" title="">
                    <i class="fa fa-minus"></i>
                    Посмотреть вакансии
                </a>
            </div>
        </div>
    </div>
@endforeach