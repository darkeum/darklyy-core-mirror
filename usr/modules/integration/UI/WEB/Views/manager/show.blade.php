@extends('away::manager.body')

@section('title', __('away::general.editor'))

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card w-100">
                <div class="card-header">
                    <strong class="card-title">@lang('away::show.info')</strong>
                    <div class="card-title text-primary">
                        <a href="{{ route('admin.away.edit', $away->id) }}" class="btn btn-outline-primary btn-sm">
                            @lang('away::show.edit')
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-10">
                            <div class="d-flex mt-2">
                                <div class="flex-shrink-0">
                                    <i class="text-info fa-regular fa-hashtag-lock fa-2x me-1"
                                       style="padding-top: 5px; width: 40px;"></i>
                                </div>
                                <div class="d-flex align-item-center justify-content-between flex-grow-1">
                                    <div class="me-1">
                                        <p class="fw-bolder mb-0">@lang('away::show.numb')</p>
                                        <span>{{ $away->id }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-2">
                                <div class="flex-shrink-0">
                                    <i class="text-warning fa-regular fa-comments fa-2x me-1"
                                       style="padding-top: 5px; width: 40px;"></i>
                                </div>
                                <div class="d-flex align-item-center justify-content-between flex-grow-1">
                                    <div class="me-1">
                                        <p class="fw-bolder mb-0">@lang('away::show.description')</p>
                                        <span>{{ $away->description }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-2">
                                <div class="flex-shrink-0">
                                    <i class="text-primary fa-regular fa-link fa-2x me-1"
                                       style="padding-top: 5px; width: 40px;"></i>
                                </div>
                                <div class="d-flex align-item-center justify-content-between flex-grow-1">
                                    <div class="me-1">
                                        <p class="fw-bolder mb-0">@lang('away::show.url')</p>
                                        <span><a href="{{ $away->url }}" target="_blank">{{ $away->url }}</a></span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-2">
                                <div class="flex-shrink-0">
                                    <i class="text-success fa-regular fa-arrow-up-right-from-square fa-2x me-1"
                                       style="padding-top: 5px; width: 40px;"></i>
                                </div>
                                <div class="d-flex align-item-center justify-content-between flex-grow-1">
                                    <div class="me-1">
                                        <p class="fw-bolder mb-0">@lang('away::show.slug')</p>
                                        <span><a href="{{ route('away.index', $away->slug) }}"
                                                 target="_blank">{{ route('away.index', $away->slug) }}</a></span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex mt-2">
                                <div class="flex-shrink-0">
                                    <i class="text-system fa-regular fa-gear fa-2x me-1"
                                       style="padding-top: 5px; width: 40px;"></i>
                                </div>
                                <div class="d-flex align-item-center justify-content-between flex-grow-1">
                                    <div class="me-1">
                                        <p class="fw-bolder mb-0">@lang('away::show.status')</p>
                                        {!! $status !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <strong class="card-title text-center d-block">
                                @lang('away::show.qr')
                            </strong>
                            <div class="text-center">
                                <div id="qrCode"></div>
                                <button class="btn btn-outline-success btn-sm mt-2"
                                        onclick="downloadPng()">@lang('away::show.qr.download')</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <strong class="card-title d-block mb-50">
                        @lang('away::show.share')
                    </strong>
                    <div>
                        <a class="social-button vk" onclick="share.vk();" href="javascript:void(0);"
                           data-bs-toggle="tooltip" title="@lang('away::show.share.vk')">
                            <i class="fab fa-vk"></i>
                        </a>
                        <a class="social-button facebook" onclick="share.fb();" href="javascript:void(0);"
                           data-bs-toggle="tooltip" title="@lang('away::show.share.fb')">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a class="social-button twitter" onclick="share.tw();" href="javascript:void(0);"
                           data-bs-toggle="tooltip" title="@lang('away::show.share.tw')">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a class="social-button whatsapp" onclick="share.wa();" href="javascript:void(0);"
                           data-bs-toggle="tooltip" title="@lang('away::show.share.wa')">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a class="social-button viber" onclick="share.vb();" href="javascript:void(0);"
                           data-bs-toggle="tooltip" title="@lang('away::show.share.vb')">
                            <i class="fab fa-viber"></i>
                        </a>
                        <a class="social-button tg" onclick="share.tg();" href="javascript:void(0);"
                           data-bs-toggle="tooltip" title="@lang('away::show.share.tg')">
                            <i class="fab fa-telegram-plane"></i>
                        </a>
                        <a class="social-button ok" onclick="share.ok();" href="javascript:void(0);"
                           data-bs-toggle="tooltip" title="@lang('away::show.share.ok')">
                            <i class="fab fa-odnoklassniki"></i>
                        </a>
                        <a class="social-button website" onclick="share.copy();" href="javascript:void(0);"
                           data-bs-toggle="tooltip" title="@lang('away::show.share.copy')">
                            <i class="far fa-copy"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{---}}
    <div class="row">
        <div class="col-lg-6 mb-3 d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-header">
                    <strong class="card-title">@lang('away::show.info')</strong>
                    <div class="card-title text-primary">
                        <a href="{{ route('admin.away.edit', $away->id) }}" class="btn btn-outline-primary btn-sm">
                            @lang('away::show.edit')
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex mt-2">
                        <div class="flex-shrink-0">
                            <i class="text-info fa-regular fa-hashtag-lock fa-2x me-1"
                               style="padding-top: 5px; width: 40px;"></i>
                        </div>
                        <div class="d-flex align-item-center justify-content-between flex-grow-1">
                            <div class="me-1">
                                <p class="fw-bolder mb-0">@lang('away::show.numb')</p>
                                <span>{{ $away->id }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex mt-2">
                        <div class="flex-shrink-0">
                            <i class="text-warning fa-regular fa-comments fa-2x me-1"
                               style="padding-top: 5px; width: 40px;"></i>
                        </div>
                        <div class="d-flex align-item-center justify-content-between flex-grow-1">
                            <div class="me-1">
                                <p class="fw-bolder mb-0">@lang('away::show.description')</p>
                                <span>{{ $away->description }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex mt-2">
                        <div class="flex-shrink-0">
                            <i class="text-primary fa-regular fa-link fa-2x me-1"
                               style="padding-top: 5px; width: 40px;"></i>
                        </div>
                        <div class="d-flex align-item-center justify-content-between flex-grow-1">
                            <div class="me-1">
                                <p class="fw-bolder mb-0">@lang('away::show.url')</p>
                                <span><a href="{{ $away->url }}" target="_blank">{{ $away->url }}</a></span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex mt-2">
                        <div class="flex-shrink-0">
                            <i class="text-success fa-regular fa-arrow-up-right-from-square fa-2x me-1"
                               style="padding-top: 5px; width: 40px;"></i>
                        </div>
                        <div class="d-flex align-item-center justify-content-between flex-grow-1">
                            <div class="me-1">
                                <p class="fw-bolder mb-0">@lang('away::show.slug')</p>
                                <span><a href="{{ route('away.index', $away->slug) }}"
                                         target="_blank">{{ route('away.index', $away->slug) }}</a></span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex mt-2">
                        <div class="flex-shrink-0">
                            <i class="text-system fa-regular fa-gear fa-2x me-1"
                               style="padding-top: 5px; width: 40px;"></i>
                        </div>
                        <div class="d-flex align-item-center justify-content-between flex-grow-1">
                            <div class="me-1">
                                <p class="fw-bolder mb-0">@lang('away::show.status')</p>
                                {!! $status !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 mb-3 d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-header">
                    <strong class="card-title">@lang('away::show.share')</strong>
                </div>
                <div class="card-body d-table">
                    <a class="social-button vk" onclick="share.vk();" href="javascript:void(0);"
                       data-bs-toggle="tooltip" title="@lang('away::show.share.vk')">
                        <i class="fab fa-vk"></i>
                    </a>
                    <a class="social-button facebook" onclick="share.fb();" href="javascript:void(0);"
                       data-bs-toggle="tooltip" title="@lang('away::show.share.fb')">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a class="social-button twitter" onclick="share.tw();" href="javascript:void(0);"
                       data-bs-toggle="tooltip" title="@lang('away::show.share.tw')">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a class="social-button whatsapp" onclick="share.wa();" href="javascript:void(0);"
                       data-bs-toggle="tooltip" title="@lang('away::show.share.wa')">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a class="social-button viber" onclick="share.vb();" href="javascript:void(0);"
                       data-bs-toggle="tooltip" title="@lang('away::show.share.vb')">
                        <i class="fab fa-viber"></i>
                    </a>
                    <a class="social-button tg" onclick="share.tg();" href="javascript:void(0);"
                       data-bs-toggle="tooltip" title="@lang('away::show.share.tg')">
                        <i class="fab fa-telegram-plane"></i>
                    </a>
                    <a class="social-button ok" onclick="share.ok();" href="javascript:void(0);"
                       data-bs-toggle="tooltip" title="@lang('away::show.share.ok')">
                        <i class="fab fa-odnoklassniki"></i>
                    </a>
                    <a class="social-button website" onclick="share.copy();" href="javascript:void(0);"
                       data-bs-toggle="tooltip" title="@lang('away::show.share.copy')">
                        <i class="far fa-copy"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-header">
                    <strong class="card-title">@lang('away::show.qr')</strong>
                </div>
                <div class="card-body text-center">
                    <div id="qrCode"></div>
                    <button class="btn btn-outline-success btn-sm mt-2"
                            onclick="downloadPng()">@lang('away::show.qr.download')</button>
                </div>
            </div>
        </div>
    </div>
    {{----}}

    <style>
        .social-button {
            display: inline-block;
            background-color: #efefef;
            width: 60px;
            height: 60px;
            line-height: 60px;
            margin: 10px;
            text-align: center;
            position: relative;
            overflow: hidden;
            opacity: .99;
            border-radius: 28%;
            box-shadow: 0 0 30px 0 rgba(0, 0, 0, 0.05);
            -webkit-transition: all 0.35s cubic-bezier(0.31, -0.105, 0.43, 1.59);
            transition: all 0.35s cubic-bezier(0.31, -0.105, 0.43, 1.59);
        }

        .dark-layout .social-button {
            background-color: #161d31;
        }

        .social-button:before {
            content: '';
            background-color: #000;
            width: 120%;
            height: 120%;
            position: absolute;
            top: 90%;
            left: -110%;
            -webkit-transform: rotate(45deg);
            transform: rotate(45deg);
            -webkit-transition: all 0.35s cubic-bezier(0.31, -0.105, 0.43, 1.59);
            transition: all 0.35s cubic-bezier(0.31, -0.105, 0.43, 1.59);
        }

        .social-button .fab,
        .social-button .far {
            font-size: 38px;
            vertical-align: middle;
            -webkit-transform: scale(0.8);
            transform: scale(0.8);
            -webkit-transition: all 0.35s cubic-bezier(0.31, -0.105, 0.43, 1.59);
            transition: all 0.35s cubic-bezier(0.31, -0.105, 0.43, 1.59);
        }

        .social-button.facebook:before {
            background-color: #3B5998;
        }

        .social-button.facebook .fab {
            color: #3B5998;
        }

        .social-button.twitter:before {
            background-color: #55acee;
        }

        .social-button.twitter .fab {
            color: #55acee;
        }

        .social-button.whatsapp:before {
            background-color: #4dc247;
        }

        .social-button.whatsapp .fab {
            color: #4dc247;
        }

        .social-button.viber:before {
            background-color: #7d51a0;
        }

        .social-button.viber .fab {
            color: #7d51a0;
        }

        .social-button.ok:before {
            background-color: #ee8208;
        }

        .social-button.ok .fab {
            color: #ee8208;
        }

        .social-button.tg:before {
            background-color: #0f89d0;
        }

        .social-button.tg .fab {
            color: #0f89d0;
        }

        .social-button.vk:before {
            background-color: #4872a3;
        }

        .social-button.vk .fab {
            color: #4872a3;
        }

        .social-button:focus:before,
        .social-button:hover:before {
            top: -10%;
            left: -10%;
        }

        .social-button:focus .fab,
        .social-button:hover .fab,
        .social-button:focus .far,
        .social-button:hover .far {
            color: #fff;
            -webkit-transform: scale(1);
            transform: scale(1);
        }
    </style>
@endsection

@section('vendor-script')
    <script src="https://cdn.jsdelivr.net/npm/easyqrcodejs@4.4.13/dist/easy.qrcode.min.js"
            integrity="sha256-ysnj5Mp9adRzpXyhbGW2YTdUSpdapignH3Z57bMq6G8=" crossorigin="anonymous"></script>
    <script>
        var options = {
            text: "{{ route('away.index', $away->slug) }}",
            colorDark: "#000000",
            colorLight: "#ffffff",
            width: 150,
            height: 150,
            drawer: 'svg',
        };
        new QRCode(document.getElementById("qrCode"), options);

        downloadPng = function () {
            var img = new Image();
            img.onload = function () {
                var canvas = document.createElement("canvas");
                canvas.width = img.naturalWidth;
                canvas.height = img.naturalHeight;
                var ctxt = canvas.getContext("2d");
                ctxt.fillStyle = "#fff";
                ctxt.fillRect(0, 0, canvas.width, canvas.height);
                ctxt.drawImage(img, 0, 0);
                var a = document.createElement("a");
                a.href = canvas.toDataURL("image/png");
                a.download = "image.png"
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            };
            var innerSvg = document.querySelector("#qrCode svg");
            var svgText = (new XMLSerializer()).serializeToString(innerSvg);
            img.src = "data:image/svg+xml;utf8," + encodeURIComponent(svgText);
        }
        var link = "{{ route('away.index', $away->slug) }}";
        var url;
        var share = {
            vk: function (purl, ptitle, pimg, text) {
                url = 'http://vk.com/share.php?';
                url += 'url=' + link;
                share.popup(url);
            },
            ok: function (purl, text) {
                url = 'https://connect.ok.ru/offer?';
                url += 'url=' + encodeURIComponent(link);
                share.popup(url);
            },
            fb: function (purl, ptitle, pimg, text) {
                url = 'http://www.facebook.com/sharer.php?s=100';
                url += '&p[url]=' + link;
                share.popup(url);
            },
            tw: function (purl, ptitle) {
                url = 'http://twitter.com/share?';
                url += '&url=' + link;
                share.popup(url);
            },
            wa: function (purl, ptitle) {
                url = 'https://wa.me/?';
                url += 'text=' + encodeURIComponent(link);
                share.popup(url);
            },
            vb: function (purl, ptitle) {
                url = 'viber://forward?';
                url += 'text=' + link;
                share.popup(url);
            },
            tg: function (purl, ptitle) {
                url = 'tg://msg?';
                url += 'text=' + encodeURIComponent(link);
                share.popup(url);
            },
            copy: function () {
                var $tmp = $("<textarea>");
                $("body").append($tmp);
                $tmp.val(link).select();
                document.execCommand("copy");
                $tmp.remove();
            },
            popup: function (url) {
                window.open(url, '', 'toolbar=0,status=0,width=626,height=436');
            }
        };
    </script>
@endsection
