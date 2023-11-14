<?php

namespace SearchName;

abstract class SearchName{
    
    protected $url;

    public function init(){
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            $this->search($_POST);
        } else {
            if(empty($this->url)) throw new \Exception("Url is empty!");
            $this->view();
        }
    }

    public function view(){
        global $adminTpl;
        $adminTpl->admin_head('Поиск по номенклатуре');
        echo $this->template();
        $adminTpl->footIncludes[] = $this->script();
        $adminTpl->admin_foot();
    }

    public function view_re(){
        return $this->template().$this->script();
    }

    protected function template(){
        return <<<HTML
        <div id="content" class="animated fadeIn">
            <div class="panel panel-dark panel-border top">
                <div class="panel-heading">
                    <span class="panel-title">Поиск по номенклатуре</span>
                </div>
                <div class="panel-body admin-form">
                    <div class="d-flex flex-column align-items-center">
                        <div class="btn-group d-flex" style="width:50%;">
                            <input type="text" id="searchVal" placeholder="Введите наименование номенклатуры или артикул" class="form-control">
                            <button type="button" id="search-btn" class="btn btn-primary btn-xs">Поиск</button>
                        </div>
                        <div id="result" style="margin-top: 20px;"></div>
                    </div>
                </div>
            </div>
        </div>
HTML;
    }

    protected function script(){
        return <<<HTML
            <script>
                document.getElementById("search-btn").addEventListener('click', search_name);
                async function search_name(){
                    const lw = new LW;
                    try {
                        const search = document.getElementById("searchVal").value;
                        lw.on();
                        const form = new FormData;
                        form.append("search", search);
                        document.getElementById("result").innerHTML = await ( await fetch("{$this->url}", {
                            method: "POST",
                            body: form,
                        }) ).text();
                    } catch (error) {
                        alert(error.message);
                    } finally{
                        lw.off();
                    }
                }
            </script>
HTML;
    }

    abstract public function search($request);
}