<div class="row">
    <div class="col-6">
        <strong>Объект</strong>
        {%CHOOSE_OBJECT_MY%}
        <strong>Разработал</strong>
        <input type="text" value="{%developed_name%}" name="project_data[developed][name]" class="form-control project_data">
        <strong>Проверил</strong>
        <input type="text" value="{%checked_name%}" name="project_data[checked][name]" class="form-control project_data">
        <strong>ГИП</strong>
        <input type="text" value="{%isu_name%}" name="project_data[isu][name]" class="form-control project_data">
        <strong>Норм контроль</strong>
        <input type="text" value="{%norm_control_name%}" name="project_data[norm_control][name]" class="form-control project_data">
    </div>
    <div class="col-6">
        <strong>Орагнизация</strong>
        {%CHOOSE_ORG_MY%}
        <strong>Дата</strong>
        <input type="date" value="{%developed_date%}" name="project_data[developed][date]" class="form-control project_data">
        <strong>&nbsp;</strong>
        <input type="date" value="{%checked_date%}" name="project_data[checked][date]" class="form-control project_data">
        <strong>&nbsp;</strong>
        <input type="date" value="{%isu_date%}" name="project_data[isu][date]" class="form-control project_data">
        <strong>&nbsp;</strong>
        <input type="date" value="{%norm_control_date%}" name="project_data[norm_control][date]" class="form-control project_data">
    </div>
</div>
<div class="row">
    <div class="col-12">
        <strong>Шифр</strong>
        <input type="text" value="{%SHIFR%}" name="shifr" class="form-control project_data">
        <strong>Наименование</strong>
        <input type="text" value="{%NAME%}" name="name" class="form-control project_data">
    </div>
</div>