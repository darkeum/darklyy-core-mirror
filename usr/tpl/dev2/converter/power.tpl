<div class="pm-information">
    <i class="fa fa-calculator" aria-hidden="true"></i>
    <p>В любой функционирующей системе необходимо учитывать показатели работоспособности приборов и машин.
    Существует несколько величин измерения мощности. Для удобства их конвертации предлагаем использовать калькулятор перевода.
    Все что вам понадобится это выбрать нужные единицы, ввести значение и увидеть результат.</p>
</div>

<div class="pm-table calc container" id="power">
    <div class="pm-table-form">
        <div class="pm-table-form-dv">
            <label><strong>Выберите единицу мощности, из которой надо перевести</strong></label><br>
            <select class="from form-control">
                <option value="1">Вт</option>
                <option value="1000">кВт</option>
                <option value="1000000">МВт</option>
                <option value="1000000000">Гвт</option>
                <option value="1.163">ккал/ч</option>
                <option value="1163">Мкал/ч</option>
                <option value="1163000">Гкал/ч</option>
            </select><br>
            <label><strong>Выберите единицу мощности, в которую надо перевести</strong></label><br>
            <select class="to form-control">
                <option value="1">Вт</option>
                <option value="1000">кВт</option>
                <option value="1000000">МВт</option>
                <option value="1000000000">Гвт</option>
                <option value="1.163">ккал/ч</option>
                <option value="1163">Мкал/ч</option>
                <option value="1163000">Гкал/ч</option>
            </select>

            <label><strong>Введите количество</strong></label><br>
            <input class="q form-control" type="text"/>

            <div class="result convertResult">
                <div class="item koef">
                    <div class="title">Переводной коэффициент: <span class="number">0</span></div>
                </div>
                <div class="item otherpower">
                    <div class="title">Результат: <span class="number">0</span></div>

                </div>

            </div>
        </div>
    </div>
    <div class="pm-table-img"><img src="/{%THEME%}/assest/images/1/pressure.png" alt=""/></div>

</div>


<script>
    $(function(){

        if ($('.calc#power').length>0) calcPower();
        if ($('.calc#fuel').length>0) calcFuel();
    });

    function antiE(what) {

        sep = 'e-';
        what = what.toString();
        if (what.indexOf(sep)>0) {
            spl = what.split(sep);
            what = spl[0] + '*10<sup>-' + spl[1] + '</sup>';

        }
        return what;
    }

    function calcPower() {
        var form = $('.calc#power');

        var selectFrom = $('select.from', form);
        var selectTo = $('select.to', form);
        var qInput = $('input.q', form);
        var resultDisplay = $('.result .otherpower .number', form);
        var koefDisplay = $('.result .koef .number', form);

        $('button', form).click(calculate);

        function calculate() {
            var from = selectFrom.val();
            var to = selectTo.val();
            var q = qInput.val();
            var koef = from/to;
            var result = q* koef;

            if (result == 0 || result == '') {
                resultDisplay.html('&nbsp;');
                koefDisplay.html('&nbsp;');
                qInput.removeClass('error');
            } else if (isNaN(result)) {
                qInput.addClass('error');
                resultDisplay.html('&nbsp;');
                koefDisplay.html('&nbsp;');

            } else {
                qInput.removeClass('error');
                resultDisplay.html(antiE(result));
                koefDisplay.html(antiE(koef));


            }
        }

        selectFrom.change(calculate);
        selectTo.change(calculate);
        qInput.keyup(calculate);
    }


    function calcFuel(){

        var units = {
            a: 'тонна',
            b: 'тыс м3',
            c: 'складской м3',
            d: 'плотный м3'
        };

        var form = $('.calc#fuel');


        var select = $('select.fuel', form);
        var qInput = $('input.q', form);
        var unitsDisplay = $('td>.units', form); //только спан рядом с селектом
        $('.unifuel .units').text('тонна');
        var koefDisplay = $('.result .koef .number', form);
        var unifuelDisplay = $('.result .unifuel .number', form);

        var measure;
        var koef;
        var q;

        select.change(fuelSelect);
        select.keyup(fuelSelect);
        fuelSelect();

        function fuelSelect(){
            var v = select.val();
            measure = units[v.split('-')[0]];
            koef = parseFloat(v.split('-')[1]);
            unitsDisplay.text(measure);
            koefDisplay.html(koef);
            calculate();

        };


        $('button', form).click(function(){
            calculate();
        });

        qInput.keyup(calculate);

        function calculate() {
            q = qInput.val();
            if (q == 0) {
                unifuelDisplay.html('&nbsp;');
            }

            var result = q*koef;
            if (result == 0) {
                unifuelDisplay.html('&nbsp;');
                qInput.removeClass('error');
            } else if (isNaN(result)) {
                unifuelDisplay.html('&nbsp;');
                qInput.addClass('error');
            } else {
                qInput.removeClass('error');
                koefDisplay.html(antiE(koef));
                unifuelDisplay.html(antiE(result));

            }
        }
    }
</script>