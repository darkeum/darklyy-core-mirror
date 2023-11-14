<?php

namespace Report\Support;

class ReportRangeDateSupport{
    static function date_range_picker_js(){
        return <<<HTML
            <script src="/usr/modules/Report/resources/js/jquery.daterangepicker.min.js"></script>
            <script>
                $(function(){
                    function init_pricker(sel, sel1, sel2, name){
                        sel.dateRangePicker({
	                    	setValue: function(s,s1,s2){
	                    		sel1.val(s1);
	                    		sel2.val(s2);
                                save_date(name, s1, s2);
	                    	},
                            language:'ru',
                            format: 'DD.MM.YYYY',
	                    });
                    }
                    function recover_data(sel1, sel2, name){
                        const date = localStorage.getItem(`\${Report_name}.date_\${name}`);
                        if(date){
                            const date_ = JSON.parse(date);
                            sel1.val(date_[0]);
                            sel2.val(date_[1]);
                        }
                    }
                    function save_date(name, s1, s2){
                        localStorage.setItem(`\${Report_name}.date_\${name}`, JSON.stringify([s1, s2]));
                    }
                    const date_range = [
                        [$('#date-range-0-0'), $('#date-range-0-1')],
                        [$('#date-range-1-0'), $('#date-range-1-1')],
                    ];
                    recover_data(date_range[0][0], date_range[0][1], "0");
                    recover_data(date_range[1][0], date_range[1][1], "1");
                    date_range.forEach((row, name) => {
                        row.forEach(item => {
                            item.on("input", function(){
                                save_date(name, row[0].val(), row[1].val());
                            });
                        })
                    })
                    init_pricker($('#two-inputs-0'), date_range[0][0], date_range[0][1], "0");
                    init_pricker($('#two-inputs-1'), date_range[1][0], date_range[1][1], "1");
                });
            </script>
HTML;
    }

    static function date_range_picker_css(){
        return <<<HTML
            <link href="/usr/modules/Report/resources/css/daterangepicker.min.css" rel="stylesheet">
HTML;
    }

    static function date_range_picker_html(){
        return <<<HTML
            <div class="col-4">
                <label>Дата первого периода</label>
                <div class="d-flex align-items-center" id="two-inputs-0">
                    <input id="date-range-0-0" class="form-control" name="date[0][0]">
                    <div style="border: 1px black solid; width: 20px; margin: 20px;"></div>
                    <input id="date-range-0-1" class="form-control" name="date[0][1]">
                </div>
            </div>
            <div class="col-4">
                <label>Дата второго периода</label>
                <div class="d-flex align-items-center" id="two-inputs-1">
                    <input id="date-range-1-0" class="form-control" name="date[1][0]">
                    <div style="border: 1px black solid; width: 20px; margin: 20px;"></div>
                    <input id="date-range-1-1" class="form-control" name="date[1][1]">
                </div>
            </div>
HTML;
    }

    static function middleware_date($date){
        if(empty($date[0][0]) || empty($date[0][1])){
            throw new \Exception("Первый период не указан");
        }
        if(empty($date[1][0]) || empty($date[1][1])){
            throw new \Exception("Второй период не указан");
        }
        $u_date = static::parse_date($date, "unix");
        if($u_date[1][1] >= $u_date[0][0] && $u_date[1][0] <= $u_date[0][1]){
            throw new \Exception("Даты пересекаются");
        }
    }

    static function parse_date($date, $format = "default"){
        switch($format){
            default:
                return [
                    [date("Y-m-d", strtotime($date[0][0])), date("Y-m-d", strtotime($date[0][1]))],
                    [date("Y-m-d", strtotime($date[1][0])), date("Y-m-d", strtotime($date[1][1]))],
                ];
            case "unix":
                return [
                    [strtotime($date[0][0]), strtotime($date[0][1])],
                    [strtotime($date[1][0]), strtotime($date[1][1])],
                ];
        }
    }
}