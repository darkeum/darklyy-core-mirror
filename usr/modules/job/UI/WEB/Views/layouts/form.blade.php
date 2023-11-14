<div class="row">
    <div class="col-12 mt-3 mb-3">
        <div class="section-title">
            <div class="title-header">
                <h5>Отправь свое резюме</h5>
                <h2 class="title">Присоединяйся <strong>к нам</strong></h2>
            </div>
        </div>
        <div class="card">
            <div class="card-body card-body-cert">
                <div class="pt-15 pb-15 pr-30 mb_30 res-991-pr-0 res-991-pt-30">
                    <p>Подпишитесь на публикацию новых вакансий в нашей компании и вы всегда будете вкурсе открытых
                        должностей и сможете претендовать на них!</p>

                    <form id="jobSend">
                        @if($send_cat ?? false)
                            <input type="hidden" name="category" id="category" value="{{ $send_cat }}"/>
                        @else
                            <div class="row">
                                <div class="col-3 mt-3"> Профессиональная область:</div>
                                <div class="col-9 mt-3">
                                    <select class="input-text" name="category">
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                                        @endforeach

                                        <option value="other">Другие вакансии</option>
                                    </select>
                                </div>
                            </div>
                        @endif

                        @if($send_position ?? false)
                            <input type="hidden" name="position" id="position" value="{{ $send_position }}"/>
                        @else
                            <div class="row">
                                <div class="col-3 mt-3">Интересующая должность</div>
                                <div class="col-9 mt-3">
                                    <input type="text" name="position" id="position" class="input-text"
                                           placeholder="Укажите интересующую вас должность" required="required"/>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-3 mt-3">ФИО</div>
                            <div class="col-9 mt-3">
                                <input type="text" name="name" id="name" class="input-text"
                                       placeholder="Укажите ваше ФИО" required="required"/>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-3 mt-3">Телефон</div>
                            <div class="col-9 mt-3">
                                <input type="phone" name="phone" id="phone" class="input-text"
                                       placeholder="Например: +7 (999) 123 32 32" required="required"/>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-3 mt-3">Электронная почта:</div>
                            <div class="col-9 mt-3">
                                <input type="email" name="email" id="email" class="input-text"
                                       placeholder="Укажите ваш E-mail адрес" required="required"/>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-3 mt-3">Резюме:</div>
                            <div class="col-9 mt-3">
                                <input type="file" name="summary" id="summary"/>
                                <div class="form-input-comment">Например: тестовое задание, портфолио и т.д.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-3 mt-3">Другие файлы:</div>
                            <div class="col-9 mt-3">
                                <input type="file" name="other_file" id="other_file"/>
                                <div class="form-input-comment">Например: тестовое задание, портфолио и т.д.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-3 mt-3">Сообщение:</div>
                            <div class="col-9 mt-3">
                                <textarea style="padding-top: 10px;" name="message" class="input-text"
                                          placeholder="Напишите то, что не вошло в резюме, но интересно и важно: ссылку на портфолио, проекты, которые вам интересны, или задайте свой вопрос"></textarea>
                            </div>
                        </div>

                        <input type="hidden" name="recaptcha_response" id="recaptchaResponse"/>
                        <script src="https://www.google.com/recaptcha/api.js?render=6LfddtUZAAAAAELCRiYyqMJSTIeYCNHvU7E5_xbP"></script>
                        <script>
                            $('#jobSend').on("submit", function (e) {
                                const form = this;
                                e.preventDefault();
                                $("#btnFetch").prop("disabled", true);
                                $("#btnFetch").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Отправка...');
                                grecaptcha.ready(function () {
                                    grecaptcha.execute('6LfddtUZAAAAAELCRiYyqMJSTIeYCNHvU7E5_xbP', {action: 'contact'}).then(async function (token) {
                                        var recaptchaResponse = document.getElementById('recaptchaResponse');
                                        recaptchaResponse.value = token;
                                        try {
                                            const data = new FormData(form);
                                            const _response = await (await fetch(`/job/send`, {
                                                method: "POST",
                                                body: data
                                            })).json();
                                            var type = _response.type;
                                            var error = _response.error;
                                            var success = _response.success;
                                            if (type != "success") {
                                                Swal.fire('Ошибка', error, 'error');
                                                $("#btnFetch").prop("disabled", false);
                                                $("#btnFetch").html('Отправить');
                                            } else {
                                                Swal.fire('Информация', success, 'success');
                                                $("#btnFetch").prop("disabled", false);
                                                $("#btnFetch").html('Отправить');
                                                //$('#btnFetch').remove();
                                            }
                                        } catch (e) {
                                            const error = e.message;
                                            $("#btnFetch").prop("disabled", false);
                                            $("#btnFetch").html('Отправить');
                                            Swal.fire('Ошибка', error, 'error');
                                        }
                                    });
                                });

                            });
                        </script>

                        <div class="row">
                            <div class="col-3 mt-3"></div>
                            <div class="col-9 mt-3 mb-3">
                                <button id="btnFetch"
                                        class="cmt-btn cmt-btn-size-md cmt-btn-shape-round cmt-btn-style-border cmt-btn-color-skincolor"
                                        type="submit">
                                    Отправить
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    textarea.input-text {
        height: 105px;
    }
</style>