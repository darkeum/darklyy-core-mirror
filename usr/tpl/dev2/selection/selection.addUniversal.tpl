вава<div class="blog-post-wrapper">
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