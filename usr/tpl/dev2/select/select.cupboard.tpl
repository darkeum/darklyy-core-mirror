<style>
    .block_1, .block_2, .block_3{
        border: solid 1px #ddd;
        margin-top: -1px;
        border-left: 0;
        background: #fff;
        padding: 15px;
        overflow: hidden;
        display: block;
    }
</style>
<div class="container">
    <h1>Конфигуратор приборов управления</h1>
    <div class="row">
        <div class="col-sm-12">
            <form action="" method="post" class="form-horizontal">
                <div class="form-group row block_1 block_2 block_3" id="primary">
                    <h2>Назначение</h2>
                    <div class="col-sm-3">
                        <select name="purpose" id="purpose" class="form-control">
                            <option value="1">Управление тепловым пунктом</option>
                            <option value="2">Насосные станции</option>
                            <option value="3">Другие АСУ ТП</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row block_1" hidden>
                    <h2>Контролер</h2>
                    <div class="col-xs-12 col-sm-3">
                        <select name="controller" id="controller" class="form-control">
                            {%CONTROLLER%}
                        </select>
                    </div>
                </div>
                <div class="form-group row block_1" hidden>
                    <h2>Системы управления</h2>
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="controlSystem_1" id="controlSystem_1">Система
                        отопления
                    </label>
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="controlSystem_2" id="controlSystem_2">Система
                        вентиляции
                    </label>
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="controlSystem_3" id="controlSystem_3">Система
                        ГВС</label>
                </div>
                <div class="form-group row block_1" hidden>
                    <h2>Характеристика регулирующего клапана</h2>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="controlValve_1" id="controlValve_1" value="1" checked>220V
                            </label>
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="controlValve_1" id="controlValve_2" value="2">24V
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="controlValve_2" id="controlValve_3" value="1" checked>3-х
                                точечный
                            </label>
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="controlValve_2" id="controlValve_4" value="2">Аналоговый
                            </label>
                        </div>
                    </div>
                    <h2>Характеристики насосного оборудования</h2>
                    <div class="col-sm-12">
                        <label>
                            Количество: <input type="text" size="1" value="1" placeholder="Введите количество" name="count" class="form-control" required>
                        </label>
                        <div class="form-group">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="pumpingEquipment_1" id="pumpingEquipment_1" value="1" checked>220V
                            </label>
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="pumpingEquipment_1" id="pumpingEquipment_2" value="2">380V
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="floatStart" id="floatStart">Плавный пуск
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="frequencyControl" id="frequencyControl">Частотное управление
                            </label>
                            <div class="frequencyControl" hidden>
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input" name="frequencyControlChoice" id="frequencyControlChoice_1" value="1" checked>по давлению
                                </label>
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input" name="frequencyControlChoice" id="frequencyControlChoice_2" value="2">по перепаду давлению
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                        <div class='col-sm-7'>
                            <label>
                                Ток, А <input type="text" class="form-control" name="currentA" id="currentA" placeholder="Введите ток" required>
                            </label>
                        </div>
                        <div class='col-sm-7'>
                            <label>
                                Мощность, кВ <input type="text" class="form-control" name="powerKB" id="powerKB" placeholder="Введите мощность" required>
                            </label>
                        </div> 
                        </div>
                        <div class="form-group">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="additionalComp" id="additionalComp">Наличие дополнительных контантов
                            </label>
                            <div class="additionalComp" hidden>
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" name="SSM" id="SSM" value="1">SSM
                                </label>
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" name="PTC" id="PTC" value="2">PTC
                                </label>
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" name="SSB" id="SSB" value="3">SSB
                                </label>
                            </div>
                        </div>
                    </div>
                    <h2>Характеристика датчиков, t&deg;</h2>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="sensors" id="sensors_1" value="1" checked>pt100
                            </label>
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="sensors" id="sensors_2" value="2">pt500
                            </label>
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="sensors" id="sensors_3" value="3">pt1000
                            </label>
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="sensors" id="sensors_4" value="4">Ni1000
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="recharge" id="recharge">Наличие подпитки
                            </label>
                            <div class="recharge" hidden>
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" name="solenoidValve" id="solenoidValve" value="1">Клапан соленоидный
                                </label>
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" name="pump" id="pump" value="2">Насос
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 block_2" hidden>

                </div>
                <div class="col-sm-12 block_3" hidden>

                </div>
                <div class="col-xs-12 col-sm-12">
                        <input type="button" value="Предыдущий шаг" class="btn btn-primary" id="previous" hidden>
                        <input type="button" value="Следующий шаг" class="btn btn-primary" id="next">
                        <input type="submit" value="Отправить" class="btn btn-primary" id="send" name="send" hidden>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $("#next").click(function () {
        let sel = $('#purpose').val();
        let block = $('.block_' + sel);
        for (let i = 0; i < block.length; i++) {
            if (!$(block[i]).prop('hidden')) {
                $('#next').removeProp('hidden');
                $('#previous').removeProp('hidden');
                $('#send').prop('hidden', true);
                $(block[i + 1]).removeProp('hidden');
                $(block[i]).prop('hidden', true);
                if (i + 1 == block.length - 1) {
                    $('#next').prop('hidden', true);
                    $('#send').removeProp('hidden');
                }
                return;
            }
        }
    });
    $("#previous").click(function () {
        let sel = $('#purpose').val();
        let block = $('.block_' + sel);
        for (let i = 0; i < block.length; i++) {
            if (!$(block[i]).prop('hidden')) {
                $('#next').removeProp('hidden');
                $('#previous').removeProp('hidden');
                $('#send').prop('hidden', true);
                $(block[i - 1]).removeProp('hidden');
                $(block[i]).prop('hidden', true);
                if (i - 1 == 0) {
                    $('#previous').prop('hidden', true);
                }
                return;
            }
        }
    });
    function changeHide(value) {
        $('#'+value).change(function () {
            if ($(this).prop('checked')) {
                $('.'+value).removeProp('hidden');
            } else {
                $('.'+value).prop('hidden', true);
            }
        });
    }

    changeHide('frequencyControl');
    changeHide('additionalComp');
    changeHide('recharge');

    $('#controlSystem_1').change(function () {
        if ($(this).prop('checked')) {
        }
    });
</script>