<div class="blog-post-wrapper">
    <article class="blog-post-item">
        <div class="inner-box">
            <div class="blog-post-content-desc">
                <div class="blog-post-content">

                    <h1> {%FULL_TITLE%} </h1>

                    <form action="{%TRANSITION%}" method="POST">

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Выберите организацию</label>
                            <div class="col-sm-8">
                                <select name="org" class="form-control">

                                    {%ORG_LIST%}

                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Выберите объект</label>
                            <div class="col-sm-8">
                                <select name="object" class="form-control">

                                    {%OBJECT_LIST%}

                                </select>
                                <p class="form-text text-muted">Чтобы создать новый объект перейдите по этой <a href="/profile/object/add">ссылке</a>!</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Комментарий</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" name="message" rows="3" placeholder="При необходимости укажите комментарий к расчёту"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="textarea">Файл</label>
                            <div class="col-lg-8">
                                <div class="mb10">
                                    <input id="input-upload-img1" name="atta[]" type="file" class="file" data-preview-file-type="text" multiple>
                                </div>

                                <p class="form-text text-muted">
                                    При необходимости вы можете прикрепить файл.
                                </p>
                            </div>
                        </div>
                        <!-- terms -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label p-0"></label>

                            <div class="col-sm-8">

                                {%CAPTCHA%}

                            </div>
                        </div>
                        <!-- Button  -->
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-8">
                                <button type="submit" class="btn btn-success btn-lg">Начать заполнение</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </article>
</div>

<!-- include jquery file upload plugin  -->
<script src="usr/tpl/atri/assest/js/fileinput.min.js" type="text/javascript"></script>
<script src="usr/tpl/atri/assest/js/fileinput/ru.js" type="text/javascript"></script>
<script src="usr/tpl/atri/assest/js/fileinput/fa.js" type="text/javascript"></script>
<script>
    // initialize with defaults

    $("#input-upload-img1").fileinput({
        uploadUrl: "/tmp",
        uploadAsync: true,
        showUpload: false,
        maxFileSize: 1000,
        maxFilesNum: 10,
        showCaption: false,

        theme: 'fa',
        language: 'ru',

        previewFileIcon: '<i class="icon icon-file"></i>',
        allowedPreviewTypes: null, // set to empty, null or false to disable preview for all types
        previewFileIconSettings: {
            'docx': '<i class="icon icon-file-word-o text-primary"></i>',
            'xlsx': '<i class="fa fa-file-excel-o text-success"></i>',
            'pptx': '<i class="fa fa-file-powerpoint-o text-danger"></i>',
            'jpg': '<i class="fa fa-file-photo-o text-warning"></i>',
            'pdf': '<i class="icon icon-file-pdf-o text-danger"></i>',
            'zip': '<i class="fa fa-file-archive-o text-muted"></i>',
        }
    });
</script>