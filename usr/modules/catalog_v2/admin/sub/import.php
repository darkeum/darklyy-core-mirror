<?php
    function importExcel(){
    ?>
    
    <div class="panel panel-dark panel-border top">
        <div class="panel-heading">
            <span class="panel-title">Импорт</span>	
        </div>
        <div class="panel-body pn">
            <div class="col-md-6 col-md-offset-3">
                <form action="/administration/module/catalog_v2/importExcelUpdate" method="POST" enctype=multipart/form-data>
                    <div class="form-group">
                        <label for="sku">Артикул</label>
                        <input type="text" value="A" id="sku" name="sku" class="form-control" placeholder="Буква столбца с артикулом" required>
                    </div>
                    <div class="form-group">
                        <label for="sku">Наименование</label>
                        <input type="text" value="D" id="name" name="name" class="form-control" placeholder="Буква столбца с наименованием" required>
                    </div>
                    <div class="form-group">
                        <label for="sku">Количество</label>
                        <input type="text" value="H" id="quantity" name="quantity" class="form-control" placeholder="Буква столбца с количеством" required>
                    </div>
                    <div class="form-group">
                        <label for="sku">Начало таблицы</label>
                        <input type="number" value="1" id="startTable" name="startTable" class="form-control" placeholder="Номер начала таблицы" required>
                    </div>
                    <div class="form-group">
                        <label for="sku">Файл</label>
                        <input type="file" id="uploadfile" name="uploadfile" class="form-control-file" required>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Отправить" id="submit" class="btn btn-success">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
    }
?>