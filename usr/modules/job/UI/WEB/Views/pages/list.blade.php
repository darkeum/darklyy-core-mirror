{{-- Этот шаблон был в папке но не где не использовался... добавлен на всякий случай... --}}


<div class="news-wrap blog-grid row mt-3">

    {% for item in items %}
    {% set postTitle = '' %}
    {% set postShort = '' %}
    {% set postFull = '' %}
    {% set postFullTitle = '' %}
    {% for lang in item.lang %}
    {% if lang.lang  == 'ru' %}
    {% set postTitle = lang.title %}
    {% set postShort = lang.short|raw %}
    {% set postFull = lang.full|raw %}
    {% set postFullTitle = lang.fulltitle %}
    {% endif %}
    {% endfor %}
    <div class="col-md-12 mb-3">
        <div class="news-block">
            <div class="news-info">
                <div class="row ml-1 align-items-center text-center">
                    <div class="col-3 col-md-12 text-center news-info-col">
                        <div class="news-info-right"><i class="fad fa-folder-open" aria-hidden="true"></i><span class="news-name-cat">{{ item.categories.name }}</span></div>
                    </div>
                    <div class="col-3 col-md-12 text-center news-info-col">
                        <div class="news-info-right"><i class="fad fa-calendar" aria-hidden="true"></i><span>{{ item.published_at|date("d.m") }}</span></div>
                    </div>
                    <div class="col-3 col-md-12 text-center news-info-col">
                        <div class="news-info-right"><i class="fad fa-eye" aria-hidden="true"></i><span>{{ item.views }}</span></div>
                    </div>
                    <div class="col-3 col-md-12 text-center news-info-col">
                        <div class="news-info-right">
                                <span class="favorite-btn" data-id="{{ item.id }}" data-module="news" data-text="false">
                                    <i class="fad fa-heart" aria-hidden="true"></i>
                                    <span class="favorite-counter"></span>
                                </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="news-context">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="news-title">
                            <a href="{{ item.url }}" title="" alt="">	{{ postTitle }}</a>
                        </h2>
                    </div>
                    <div class="col-md-4">
                        <div class="news-image"><img class="img-fluid" src="{{ item.preview.full }}"></div>
                    </div>
                    <div class="col-md-8">
                        <div class="news-text">
                            {{ postShort|truncate_words(400) }}
                        </div>
                    </div>
                    <div class="col-md-12 text-right align-self-end">
                        <div class="news-element-next"><a href="{{ item.url }}">читать далее...</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {% endfor %}
</div>
<div class="row mt-3">
    <div class="col-12">
        {{ items.render|raw }}
    </div>
</div>
<style>
    .favorite-btn-active {
        color: #fa381d;
    }

    .favorite-btn {
        cursor: pointer;
    }

    .favorite-btn:hover {
        color: #fa381d;
    }

    .favorite-btn-active:hover {
        color: #555 !important;
    }

    .favorite-btn:after {
        display: none !important;
    }

    @media only screen and (max-width: 768px) {
        .news-block {
            grid-template-columns: 1fr !important;
        }
        .news-calendar {
            margin: 15px;
        }
    }

    @media only screen and (min-width: 768px) {
        .news-info-col {
            padding: 0;
            margin-left: -8px;
        }
    }
</style>
<script>
    $('.favorite-btn').each(function() {
        const favorite = localStorage.getItem('favorite');
        const arr = favorite == null ? {} : JSON.parse(favorite);
        const id = $(this).data("id");
        const module = $(this).data("module");
        const textFlag = $(this).data("text");
        const key = module + id;
        if (key in arr) {
            $(this).addClass('favorite-btn-active');
            if (textFlag) {
                $(this).html('<i class="fad fa-heart"></i>Удалить из избранного');
            }
        } else {
            $(this).removeClass('favorite-btn-active');
            if (textFlag) {
                $(this).html('<i class="fad fa-heart"></i>Добавить в избранное');
            }
        }
    });
    $(".favorite-btn").on('click', function(event) {
        const favorite = localStorage.getItem('favorite');
        const arr = favorite == null ? {} : JSON.parse(favorite);
        const id = $(this).data("id");
        const module = $(this).data("module");
        const key = module + id;
        const textFlag = $(this).data("text");
        let action = 'add';
        if (key in arr) {
            delete arr[key];
            $(this).removeClass('favorite-btn-active');
            if (textFlag) {
                $(this).html('<i class="fad fa-heart"></i>Добавить в избранное');
            }
            const favorite_counter = $(this).find(".favorite-counter").eq(0);
            const count = favorite_counter.html();
            favorite_counter.html(count - 1);
            action = 'remove';
        } else {
            arr[key] = {
                id,
                module
            };
            $(this).addClass('favorite-btn-active');
            if (textFlag) {
                $(this).html('<i class="fad fa-heart"></i>Удалить из избранного');
            }
            const favorite_counter = $(this).find(".favorite-counter").eq(0);
            const count = +favorite_counter.html();
            favorite_counter.html(count + 1);
        }
        $.post('ajax.php?do=actionFavorite', {
            module,
            id,
            action,
            arr
        });
        localStorage.favorite = JSON.stringify(arr);
    });
</script>