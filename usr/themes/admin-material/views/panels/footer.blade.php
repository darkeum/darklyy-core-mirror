{{-- BEGIN: Footer--}}
<footer class="footer footer-light {{($configData['footerType'] === 'footer-hidden') ? 'd-none':''}} {{$configData['footerType']}}">
    <p class="clearfix mb-0">
        <span class="float-md-start d-block d-md-inline-block mt-25">COPYRIGHT &copy;
            <script>document.write(new Date().getFullYear())</script>
            <a class="ms-25" href="https://прис.рус" target="_blank">ООО «ПРИС»</a>,
        <span class="d-none d-sm-inline-block">Все права защищены!</span>
    </span>
    </p>
</footer>
<button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>
{{--/ END: Footer --}}

@push('scripts')
    <script type="text/javascript" data-info="">
        $(document).ready(function () {

            pushSearch({'name': 'Простое поле', 'url': '/', 'icon': 'star'});
            pushSearch({
                'Файлы': {
                    // 'icon': 'far fa-files',
                    'icon': 'file',
                    'items': [
                        {'name': 'Файл JPG', 'url': '/', 'type': 'jpg', 'size': '99kb', 'point': 'Marketing Manager'},
                        {'name': 'Файл DOC', 'url': '/', 'type': 'doc', 'size': '99kb', 'point': 'Marketing Manager'},
                        {'name': 'Файл PDF', 'url': '/', 'type': 'pdf', 'size': '99kb', 'point': 'Marketing Manager'},
                        {'name': 'Файл PNG', 'url': '/', 'type': 'png', 'size': '99kb', 'point': 'Marketing Manager'},
                        {'name': 'Файл PPT', 'url': '/', 'type': 'ppt', 'size': '99kb', 'point': 'Marketing Manager'},
                        {'name': 'Файл SQL', 'url': '/', 'type': 'sql', 'size': '99kb', 'point': 'Marketing Manager'},
                        {'name': 'Файл TXT', 'url': '/', 'type': 'txt', 'size': '99kb', 'point': 'Marketing Manager'},
                        {'name': 'Файл XLS', 'url': '/', 'type': 'xls', 'size': '99kb', 'point': 'Marketing Manager'},
                        {'name': 'Файл XML', 'url': '/', 'type': 'xml', 'size': '99kb', 'point': 'Marketing Manager'},
                        {'name': 'Файл ZIP', 'url': '/', 'type': 'zip', 'size': '99kb', 'point': 'Marketing Manager'},
                        {'name': 'Файл RAR', 'url': '/', 'type': 'rar', 'size': '99kb', 'point': 'Marketing Manager'},
                    ]
                }
            });

            pushSearch({
                'Пользователи': {
                    'icon': 'user',
                    'items': [
                        {'name': 'Иван Комаров', 'url': '/',
                            'ava': '{{ asset("images/portrait/small/avatar-s-1.jpg") }}', 'role': 'Админ'},
                        {'name': 'Сергей Переверзев', 'url': '/',
                            'ava': '{{ asset("images/portrait/small/avatar-s-2.jpg") }}', 'role': 'Заместитель админа'},
                        {'name': 'Иван Иванов', 'url': '/',
                            'ava': '{{ asset("images/portrait/small/avatar-s-3.jpg") }}', 'role': 'Редактор'},
                        {'name': 'Сидор Сидоров', 'url': '/',
                            'ava': '{{ asset("images/portrait/small/avatar-s-4.jpg") }}', 'role': 'Пользователь'},
                        {'name': 'Петр Петров', 'url': '/',
                            'ava': '{{ asset("images/portrait/small/avatar-s-5.jpg") }}', 'role': 'Прачка'},
                        {'name': 'Денис Денисов', 'url': '/',
                            'ava': '{{ asset("images/portrait/small/avatar-s-6.jpg") }}', 'role': 'Раб'},
                    ]
                }
            });

            // Тестовые данные
            globalSearch.push({'name': 'Чат', 'url': '#', 'icon': 'message-square'});
            globalSearch.push({'name': 'Электронная почта', 'url': '#', 'icon': 'mail'});
            globalSearch.push({'name': 'Новости', 'url': '#', 'icon': 'mail'});

            // globalSearch.push({'name' : '888', 'url' : '444', 'icon' : 'star' });
            // globalSearch.push({'Файлы' : [{'name' : '999', 'url' : '444', 'icon' : 'star' }] });

        });
    </script>
@endpush