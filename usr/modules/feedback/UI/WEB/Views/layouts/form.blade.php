<div class="col-md-12 mt-4" id="feedback">

    <div class="section-title">
        <div class="title-header">
            <h5>Напишите нам сообщение</h5>
            <h2 class="title">Обратная связь</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-sm-12">
            <form id="feedbackSend" method="post" name="feedbackSend" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-4 col-sm-6 mb-3">
                        <input type="text" name="name" id="name" value="" class="input-text"
                               placeholder="Укажите ваше имя" required=""/>
                    </div>

                    <div class="col-md-4 col-sm-6 mb-3">
                        <input type="email" name="email" id="email" value="" class="input-text"
                               placeholder="Укажите ваш E-mail" required=""/>
                    </div>

                    <div class="col-md-4 col-sm-6 mb-3">
                        <input type="text" name="phone" id="phone"
                               {{-- pattern="\+7\s?[\(]{0,1}9[0-9]{2}[\)]{0,1}\s?\d{3}[-]{0,1}\d{2}[-]{0,1}\d{2}" --}} value=""
                               class="input-text" placeholder="Укажите ваш телефон" required=""/>
                    </div>

                    <div class="col-12 mb-3">
                        <textarea name="message" cols="40" rows="10" class="input-text"
                                  placeholder="Укажите ваше сообщение" required=""></textarea>
                    </div>

                    <div class="col-12">

                        <div class="custom-control custom-checkbox custom-required-checkbox">
                            <input type="checkbox" class="custom-control-input" id="custom-required-checkbox"
                                   name="custom-required-checkbox" required>
                            <label class="custom-control-label" for="custom-required-checkbox">
                                Настоящим подтверждаю, что я ознакомлен с условиями и согласен на
                                <a href="{{ url('content/privacy_policy.html') }}">
                                    обработку персональных данных.
                                </a>
                            </label>
                        </div>

                    </div>
                    @recaptcha
                    <div class="col-12 mt-3">
                        <button id="btnFetch" name="btnFetch"
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

<script type="text/javascript">
    _submitEvent = function () {
        console.log('submit button clicked.');
        $.ajax({
            type: "POST",
            url: "{{ route('feedback.send') }}",
            data: {
                "name": $("#name").val(),
                "email": $("#email").val(),
                "content": $("#content").val(),
                // важный! не забудьте отправить `g-recaptcha-response`
                "g-recaptcha-response": $("#g-recaptcha-response").val()
            },
            dataType: "json",
            success: function (data) {
                var type = _response.type;
                var error = _response.error;
                var errorMessage = '';
                var success = _response.success;
                if (type != "success") {

                    for (var k in error) {
                        errorMessage = errorMessage + error[k] + '<br>';
                        console.log(errorMessage);
                    }
                    console.log(errorMessage);
                    Swal.fire('Ошибка', errorMessage, 'error');
                    $("#btnFetch").prop("disabled", false);
                    $("#btnFetch").html('Отправить');
                } else {
                    Swal.fire('Информация', success, 'success');
                    $('#btnFetch').remove();
                    $('#feedbackSend').html('<div class="alert alert-success alert-lg w-100 mt-3">' +
                        '<h4 class="no-margin no-padding">Информация</h4>' +
                        '<p>' + success + '</p>' +
                        '</div>');
                }
            },
            error: function (data) {
                const error = e.message;
                $("#btnFetch").prop("disabled", false);
                $("#btnFetch").html('Отправить');
                Swal.fire('Ошибка', error, 'error');
            }
        });
    };

</script>
