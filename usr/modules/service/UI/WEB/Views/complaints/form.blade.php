@extends('app')

@section('content')

    <link rel="stylesheet" type="text/css"
          href="{{ asset('assets/css/pages/page-complaints.css') }}"/>

    <div id="wrapper">
        <div class="main-container">
            <div class="container">
                <div class="row">

                    <div id="shopify-section-blog-sidebar"
                         class="shopify-section blog-sidebar col-sidebar sidebar"
                    >
                        @include('layouts.sidebar', [
                            'categories' => $categories,
                            'url' => 'service'
                        ])
                    </div>

                    <div id="shopify-section-blog-template" class="shopify-section">

                        {{-- Заголовок --}}
                        @include('layouts.title', [
                            'name' => 'service',
                        ])
                        {{-- Заголовок --}}

                        {{-- Хлебные крошки --}}
                        @include('layouts.breadcrumbs')
                        {{-- Хлебные крошки --}}

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card box-shadow">

                                    <div class="col-12 mt-3 mb-3">
                                        <div class="section-title">
                                            <div class="title-header">
                                                <h5>ПРИЕМ РЕКЛАМАЦИЙ</h5>
                                                <a href="{{ route('service.complaints.template') }}">template</a>
                                                <h2 class="title"><strong>Как с нами связаться?</strong></h2>
                                            </div>
                                            <div class="title-desc">Свяжитесь с нами по телефону, задайте вопрос
                                                специалисту по почте или решите
                                                возникшие сложности с помощью этого раздела
                                            </div>
                                            <p style="line-height: 1.3;">Наша компания специализируется на установке
                                                систем водоснабжения,
                                                систем отопления. <br> ООО «АТРИ-ЭНЕРГО» - динамично развивающаяся
                                                многопрофильная компания,
                                                направлением деятельности которой является внедрение и применение
                                                современных технологий для
                                                решения инженерных задач в области систем тепловодоснабжения.</p>
                                        </div>

                                        {{-- Форма --}}
                                        <form id="complaintForm" class="complaints" method="POST"
                                              action="{{ route('service.complaints.send') }}"
                                        >
                                            {{ csrf_field() }}

                                            {{-- One "tab" for each step in the form: --}}
                                            <div class="tab form-row">

                                                <div class="form-group col-md-6">
                                                    <label for="c_type">Кто вы <a>*</a></label>
                                                    <select class="form-control" id="c_type" name="c_type" required>
                                                        <option value="">Не выбрано</option>
                                                        <option value="1">Физическое лицо</option>
                                                        <option value="2">Юр. лицо - Резидент РФ</option>
                                                        <option value="3">Юр. лицо - Не резидент РФ</option>
                                                    </select>
                                                    <small id="c_type_i" class="form-text text-muted">
                                                        Информация
                                                    </small>
                                                    <div class="invalid-feedback">Это обязательное поле</div>
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label for="c_why">Причина обращения <a>*</a></label>
                                                    <select class="form-control" id="c_why" name="c_why" required>
                                                        <option value="">Не выбрано</option>
                                                        <option value="1">Технический брак</option>
                                                        <option value="2">Недовоз, излишек, пересорт</option>
                                                        <option value="3">Нарушение товарного вида (Механические
                                                            повреждения)
                                                        </option>
                                                        <option value="4">Жалоба на торгового агента</option>
                                                        <option value="5">Брак с ущербом</option>
                                                    </select>
                                                    <small id="c_why_i" class="form-text text-muted">
                                                        Информация
                                                    </small>
                                                    <div class="invalid-feedback">Это обязательное поле</div>
                                                </div>

                                            </div>

                                            <div class="tab form-row">

                                                <div class="col-md-12 mb-3">
                                                    <h3 id="c_why_title" class=""></h3>
                                                </div>

                                                <div id="c_fio_id" class="form-group col-md-6" style="display: none">
                                                    <label for="c_fio">ФИО Клиента <a>*</a></label>
                                                    <input type="text" class="form-control" id="c_fio" name="c_fio"
                                                           aria-describedby="" placeholder="ФИО Клиента" disabled required>
                                                    <small id="c_fio_i" class="form-text text-muted">
                                                        Информация доп.
                                                    </small>
                                                    <div class="invalid-feedback">Это обязательное поле</div>
                                                </div>

                                                <div id="c_company_id" class="form-group col-md-6"
                                                     style="display: none">
                                                    <label for="c_company">Наименование или ИНН <a>*</a></label>
                                                    <input type="text" class="form-control" id="c_company"
                                                           name="c_company"
                                                           aria-describedby="" placeholder="Укажите наименование или ИНН"
                                                           disabled required>
                                                    <small id="c_company_i" class="form-text text-muted">
                                                        Информация доп.
                                                    </small>
                                                    <div class="invalid-feedback">Это обязательное поле</div>
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label for="c_phone">Номер телефона клиента <a>*</a></label>
                                                    <input type="text" class="form-control" id="c_phone" name="c_phone"
                                                           aria-describedby="" placeholder="Номер телефона клиента" required>
                                                    <small id="c_phone_i" class="form-text text-muted">
                                                        Информация доп.
                                                    </small>
                                                    <div class="invalid-feedback">Это обязательное поле</div>
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label for="c_email">Электронная почта клиента <a>*</a></label>
                                                    <input type="email" class="form-control" id="c_email" name="c_email"
                                                           aria-describedby="" placeholder="Электронная почта клиента" required>
                                                    <small id="c_email_i" class="form-text text-muted">
                                                        Информация доп.
                                                    </small>
                                                    <div class="invalid-feedback">Это обязательное поле</div>
                                                </div>

                                                <div id="c_manager_id" class="form-group col-md-6"
                                                     style="display: none">
                                                    <label for="c_manager">ФИО менеджера <a>*</a></label>
                                                    <input type="text" class="form-control" id="c_manager"
                                                           name="c_manager"
                                                           aria-describedby="" placeholder="ФИО менеджера"
                                                           disabled required>
                                                    <small id="c_manager_i" class="form-text text-muted">
                                                        Информация доп.
                                                    </small>
                                                    <div class="invalid-feedback">Это обязательное поле</div>
                                                </div>

                                                <div class="form-group col-md-12">
                                                    <label for="c_comment">Комментарий</label>
                                                    <textarea rows='5' class="form-control" id="c_comment"
                                                              name="c_comment"
                                                              aria-describedby="" placeholder="Комментарий"></textarea>
                                                    <small id="c_manager_i" class="form-text text-muted">
                                                        Информация доп.
                                                    </small>
                                                </div>

                                                {{-- Файлы --}}
                                                <div id="c_file_id" class="row">
                                                    <div class="col-md-12 mb-3">
                                                        Вложенные файлы (в форматах DOC, DOCX, RTF, XLS, XLSX, TXT, JPG, JPEG, BMP, GIF, PNG, ZIP, 7Z, AVI, MP4, MOV или PDF) не превышающие 25МБ
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="custom-file mb-2">
                                                            <input type="file"
                                                                   class="custom-file-input"
                                                                   id="c_file"
                                                                   accept=".doc,.docx,.rtf,.xls,.xlsx,.txt,.jpg,.jpeg,.bmp,.gif,.png,.zip,.7z,.avi,.mp4,.mov,.pdf"
                                                                   multiple required>
                                                            <label id="c_file_l" class="custom-file-label" for="c_file">Укажите файл...</label>
                                                            <div class="invalid-feedback">Это обязательное поле</div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12 font-italic">
                                                        <h5>Для рассмотрения претензии необходимо приложить следующие файлы:</h5>
                                                        <ul>
                                                            <li>1. Рекламация с подписью и печатью клиента (<a id="c_file_temp" href="#" target="_blank">скачать шаблон</a>) <span>*</span></li>
                                                            <li>2. Копия товарной накладной (все страницы) <span>*</span></li>
                                                            <li>3. Фото (4-6 шт. с видимыми повреждениями) <span>*</span></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                {{-- Файлы --}}

                                            </div>

                                            <div class="buttons-actions overflow-auto">
                                                <div class="float-right">
                                                    <button type="button" id="prevBtn" class="prevBtn"
                                                            onclick="nextPrev(-1)">
                                                        Назад
                                                    </button>
                                                    <button type="button" id="nextBtn" class="nextBtn"
                                                            onclick="nextPrev(1)">
                                                        Далее
                                                    </button>
                                                </div>
                                                <span>
                                                    Поля помеченные ( <span>*</span> ) являются обязательными.
                                                </span>
                                            </div>

                                            {{-- Круги, которые указывают на этапы формы: --}}
                                            <div id="steps" class="text-center mt-4">
                                                {{-- Генератор JS по кол-ву tab --}}
                                                <span class="step"></span>
                                                <span class="step"></span>
                                            </div>
                                        </form>
                                        {{-- Форма --}}

                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <script type="application/javascript">

        $("#c_file").on("change", function() {
            if (validateSize(this, 25)) {
                let files = Array.from(this.files)
                let fileName = files.map(f =>{return f.name}).join(", ")
                $(this).siblings("#c_file_l").addClass("selected").html(fileName);
            }
        });

        // Объявление const
        const c_f = document.getElementById("c_fio");
        const c_c = document.getElementById("c_company");
        const c_m = document.getElementById("c_manager");
        const c_file = document.getElementById("c_file");

        const c_f_id = document.getElementById("c_fio_id");
        const c_c_id = document.getElementById("c_company_id");
        const c_m_id = document.getElementById("c_manager_id");
        const c_file_id = document.getElementById("c_file_id");

        var currentTab = 0; // Current tab is set to be the first tab (0)
        showTab(currentTab); // Display the current tab

        function showTab(n) {
            // This function will display the specified tab of the form...
            var x = document.getElementsByClassName("tab");

            x[n].style.display = "flex";
            //... and fix the Previous/Next buttons:
            if (n == 0) {
                document.getElementById("prevBtn").style.display
                    = c_f_id.style.display
                    = c_m_id.style.display
                    = c_c_id.style.display
                    = c_file_id.style.display
                    = "none";

                c_f.disabled = c_m.disabled = c_c.disabled = c_file.disabled = true;

            } else {
                document.getElementById("prevBtn").style.display = "inline";
            }
            if (n == (x.length - 1)) {
                document.getElementById("nextBtn").innerHTML = "Отправить";
            } else {
                document.getElementById("nextBtn").innerHTML = "Далее";
            }
            //... and run a function that will display the correct step indicator:
            fixStepIndicator(n)
        }

        function nextPrev(n) {
            // This function will figure out which tab to display
            var x = document.getElementsByClassName("tab");
            // Exit the function if any field in the current tab is invalid:
            if (n == 1 && !validateForm()) return false;
            // Hide the current tab:
            x[currentTab].style.display = "none";

            let c_t = document.getElementById("c_type");
            let c_w = document.getElementById("c_why");
            let c_w_t = document.getElementById("c_why_title");

            if (c_t && c_w) {
                c_w_t.innerHTML = c_t.options[c_t.selectedIndex].text + ' - ' + c_w.options[c_w.selectedIndex].text;

                // Кто вы
                if (parseInt(c_t.options[c_t.selectedIndex].value) === 1) {
                    c_f_id.style.display = c_m_id.style.display = 'block';
                    c_f.disabled = c_m.disabled = false;
                } else {
                    c_c_id.style.display = 'block';
                    c_c.disabled = false;
                }

                // Причина
                if (parseInt(c_w.options[c_w.selectedIndex].value) !== 4) {
                    c_file_id.style.display = 'block';
                    c_file.disabled = false;
                }
            }

            // Increase or decrease the current tab by 1:
            currentTab = currentTab + n;
            // if you have reached the end of the form...
            if (currentTab >= x.length) {
                // ... the form gets submitted:
                document.getElementById("complaintForm").submit();
                return false;
            }
            // Otherwise, display the correct tab:
            showTab(currentTab);
        }

        $(".tab select, .tab input").focus(function () {
            $(this).removeClass('is-invalid');
        })

        function validateForm() {
            // This function deals with validation of the form fields
            var x, y, i, valid = true;
            x = document.getElementsByClassName("tab");

            // Указать обязательные поля (теги) для валидации
            y = x[currentTab].querySelectorAll("input, select");

            // A loop that checks every input field in the current tab:
            for (i = 0; i < y.length; i++) {
                if (y[i].disabled == false) {

                    // If a field is empty...
                    if (y[i].required = true && y[i].value == '') {

                        y[i].classList.remove("is-invalid");

                        // add an "invalid" class to the field:
                        y[i].className += " is-invalid";

                        // and set the current valid status to false
                        valid = false;
                    }
                }
            }
            // If the valid status is true, mark the step as finished and valid:
            if (valid) {
                document.getElementsByClassName("step")[currentTab].className += " finish";
            }
            return valid; // return the valid status
        }

        function fixStepIndicator(n) {
            // This function removes the "active" class of all steps...
            var i, x = document.getElementsByClassName("step");
            for (i = 0; i < x.length; i++) {
                x[i].className = x[i].className.replace(" active", "");
            }
            //... and adds the "active" class on the current step:
            x[n].className += " active";
        }

        function validateSize(input, size) {
            const fileSize = input.files[0].size / 1024 / 1024; // in MiB
            if (fileSize > size) {
                alert('Этот файл больше ' + size + ' MB');
                return false;
            } else {
                return true;
            }
        }

    </script>

@endsection