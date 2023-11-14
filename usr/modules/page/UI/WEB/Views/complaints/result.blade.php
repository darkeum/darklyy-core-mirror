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

                                        {{-- Результат --}}
                                        <div class="complaints">Результат</div>
                                        {{-- Результат --}}

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

        // Объявление const
        const c_f = document.getElementById("c_fio");
        const c_c = document.getElementById("c_company");
        const c_m = document.getElementById("c_manager");
        const c_c_i = document.getElementById("c_company_inn");

        const c_f_id = document.getElementById("c_fio_id");
        const c_c_id = document.getElementById("c_company_id");
        const c_m_id = document.getElementById("c_manager_id");
        const c_c_i_id = document.getElementById("c_company_inn_id");

        // Считам tab и добавляем span
        // for (let i = 0; i < document.getElementsByClassName('tab').length; i++) {
        //     let span = document.createElement('span');
        //     span.classList.add("step");
        //     document.getElementById("steps").appendChild(span);
        // }

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
                    = c_c_i_id.style.display
                    = "none";

                c_f.disabled = c_m.disabled = c_c.disabled = c_c_i.disabled = true;

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

                if (parseInt(c_t.options[c_t.selectedIndex].value) === 1) {
                    c_f_id.style.display = c_m_id.style.display = 'block';
                    c_f.disabled = c_m.disabled = false;
                } else {
                    c_c_id.style.display = c_c_i_id.style.display = 'block';
                    c_c.disabled = c_c_i.disabled = false;
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
        
    </script>

@endsection