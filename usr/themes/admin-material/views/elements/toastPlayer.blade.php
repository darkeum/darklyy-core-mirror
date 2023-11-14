{{--

Не забудьте инициализировать класс JS после @include('elements.toastPlayer')!!!
Между директивами @push('scripts') тут @endpush

Пример : <script data-title="Плеер Plyr в Toast - Unit"> let tPlayer = new toastPlayer(); </script>

ps. Может подсвечивать что класса "toastPlayer" нет. Это нормально!
ps.ps. Подробный пример в модуле "Calls"

#DOC
tPlayer.load() - Скрытие предыдущего toast с плеером, остановка плеера и показ уведомления о начале загрузки
tPlayer.play(src) - Начать проигрывание ссылки на файл полученной в src и показ самого плеера для этого в toast
tPlayer.error() - Показать уведомление об ошибки загрузки файла

--}}

{{-- HTML BODY PLAYER --}}
<div class="toast-container toast-player-container position-fixed bottom-0 end-0 p-2 w-auto z-50">
    <div class="toast toast-player hide w-auto" role="alert" aria-live="assertive" aria-atomic="true"
         data-bs-autohide="false">
        <div class="toast-header">
            <strong class="me-auto">Плеер</strong>
            <small class="text-muted toast-player-title">
                {{-- Title - Название файла --}}
            </small>
            <button type="button" class="ms-1 btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>

        <div class="toast-body">
            <audio class="audio-player" controls></audio>
        </div>

    </div>
</div>
{{-- HTML BODY PLAYER --}}

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/css/extensions/plyr.min.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('vendors/js/extensions/plyr.min.js') }}"></script>
    <script src="{{ asset('vendors/js/extensions/plyr.polyfilled.min.js') }}"></script>

    <script data-title="Плеер Plyr в Toast - Config">

        let controls =
            [
                // 'play-large',       // Большая кнопка воспроизведения в центре
                'restart',          // Перезапустите воспроизведение
                // 'rewind',           // Перемотка назад на время поиска (по умолчанию 10 секунд)
                'play',             // Воспроизведение/пауза воспроизведения
                // 'fast-forward',     // Быстрая перемотка вперед по времени поиска (по умолчанию 10 секунд)
                'progress',         // Индикатор выполнения и скруббер для воспроизведения и буферизации
                'current-time',     // Текущее время воспроизведения
                'duration',         // Полная продолжительность носителя
                'mute',             // Переключить режим отключения звука
                'volume',           // Регулятор громкости
                // 'captions',         // Переключать подписи
                'settings',         // Меню настроек
                // 'pip',              // Картинка в картинке (в настоящее время только для Safari)
                // 'airplay',          // Трансляция (в настоящее время только в Safari)
                'download',         // Отобразите кнопку загрузки со ссылкой либо на текущий источник, либо на пользовательский URL, указанный в настройках
                // 'fullscreen'        // Переключение полноэкранного режима
            ];

        let opt = {
            positionClass: 'toast-bottom-right',
            closeButton: true,
            tapToDismiss: false,
            progressBar: true,
            showDuration: 500,
            timeOut: 2000,
            hideDuration: 500
        };

        const player = new Plyr('.audio-player', {
            controls,
            i18n: {
                restart: "Перезапустить",
                rewind: '"Начать с" {seektime}"с"',
                play: "Воспроизвести",
                pause: "Пауза",
                buffered: "Буферизация",
                currentTime: "Текущее время",
                duration: "Длительность",
                volume: "Звук",
                mute: "Заглушить",
                unmute: "Включить звук",
                enterFullscreen: "Перейти на полный экран",
                exitFullscreen: "Выйти из полного экрана",
                settings: "Настройки",
                pip: "Картинка в картинке",
                menuBack: "Назад",
                speed: "Скорость",
                normal: "Нормальная",
                quality: "Качество",
                loop: "Цикл",
                start: "Начало",
                end: "Конец",
                all: "все",
                reset: "Сбросить",
            },
        });

        // Toast Player Init
        let toastPlayerSelector = document.querySelector('.toast-player');
        let toastPlayerUnit = new bootstrap.Toast(toastPlayerSelector, {
            autohide: false
        });

        // Events Toast
        toastPlayerSelector.addEventListener('show.bs.toast', function () {
            player.play();
        });

        toastPlayerSelector.addEventListener('hide.bs.toast', function () {
            player.stop();
        });

        let tLoad, tError;

        class toastPlayer {

            constructor() {
                // this.n = n;
            }

            // Новая загрузка и показ уведомления о ней (Скрыть предыдущее окно с плеером)
            load() {
                if (tLoad) {
                    tLoad.hide()
                }
                toastPlayerUnit.hide();
                tLoad = toastr['info']('Поиск файла...', 'Загрузка!', opt);
            }

            // Показать подсказку и проиграть файл
            play(src) {
                let title = src.substring(src.lastIndexOf('/') + 1);
                let ext = title.substr(title.lastIndexOf('.') + 1);

                // Установить название файла в toast
                $('.toast-player-title').html(title);

                // Ссылка на скачивание файла
                player.config.urls.download = src;
                player.source = {
                    type: 'audio',
                    title: title,
                    sources: [
                        {
                            src: src,
                            type: 'audio/' + ext,
                        },
                    ],
                };
                toastPlayerUnit.show();
            }

            // Ошибка загрузки файла
            error() {
                tLoad.hide();
                tError = toastr['error']('Файл не доступен!', 'Ошибка!', opt);
            }

        }

        // player.on('ended', (event) => {
        //     const instance = event.detail.plyr;
        //     console.log(instance);
        // });

        {{---}}
        $(document).on('click', '[data-audio]', function () {

            tPlayer.load();

            let src = $(this).attr('data-audio');
            $.ajax({
                url: "{{ route('admin.calls.parse') }}",
                method: 'GET',
                dataType: 'json',
                // async : false,
                data: {
                    audio: src
                },
                success: function (data) {
                    if (data.src) {
                        tPlayer.play(data.src);
                    } else {
                        tPlayer.error();
                    }
                }
            });

        });
        {{----}}

    </script>

@endpush