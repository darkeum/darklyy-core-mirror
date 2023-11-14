<div class="col-md-12 mb-3">
    <div class="card">
        <div class="card-body card-body-cert">
            <div class="row">
                <div class="col-md-9 mb-2">
                    <div class="title-border">
                        <a href="{%FULL_LINK%}" class="ah2"><h3 class="title-h2">{%OID_NAME%}, {%OID_CITY%}</h3></a>
                        <div class="title-description text-muted"><em>{%OID_DESCRIPTION%}</em></div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p>{%TEXT%}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 text-right mb-2 align-self-center">
                    <p><a data-fancybox="gallery" href="{%PREVIEW_FULL%}" target="_blank"><img class="img_left mr-2"
                                                                                               style="float: right;"
                                                                                               title="{%TITLE%}"
                                                                                               src="{%PREVIEW_THUMB_WEBP%}"
                                                                                               alt="{%TITLE%}"></a></p>
                </div>
                <div class="col-md-6">
                    <strong>Сотрудничество с {%DATE_COOP%} г.</strong>
                </div>
                <div class="col-md-6 text-right">
                    {%TAGS%}
                </div>
                <div class="col-md-12 mb_25">
                    <div class="document-group">
                        <div class="document-group-title-uni"><strong>Прикрепленные файлы</strong></div>
                        <div class="document-group__documents">
                            <a href="{%PDF%}" target="_blank" class="ui-link document">
                                <i class="document__icon document__icon_pdf"></i>
                                <div class="document__title document__title__cert">
                                    <div class="document__name">{%TITLE%}.pdf</div>
                                    <div class="document__size">161.82 Кб</div>
                                </div>
                            </a>
                            <a href="{%PREVIEW_FULL%}" target="_blank" class="ui-link document">
                                <i class="document__icon document__icon_dwg"></i>
                                <div class="document__title document__title__cert">
                                    <div class="document__name">{%TITLE%}.jpg</div>
                                    <div class="document__size">2.5 Мб</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>