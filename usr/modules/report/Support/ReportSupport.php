<?php

namespace Report\Support;


use Boot\System\Http\Request;
use Boot\Support\Facades\Route;
use Boot\Support\Facades\Router;
use Boot\System\Office\OfficeSupport;

class ReportSupport
{
    static function download_excel(Request $request)
    {
        $link = $request->get("link");
        OfficeSupport::get_file($link);
    }

    static function router_gen($class)
    {
        Route::prefix("/" . $class::getReportName())
            ->name($class::getReportName() . ".")
            ->group(function () use ($class) {
                Route::get('/',    [$class, "index"])->name("index");
                Route::post('/view',   [$class, "view"])->name("view");
                Route::post('/excel',   [$class, "excel"])->name("excel");
            });
    }

    static function victoria_add($index)
    {
        $victoria = new \Victoria;
        $victoria->id = $index;
        $victoria->transfer_to_document_disabled = true;
        $victoria->local_script = <<<HTML
			    \$victoria.add_basket = async function(cid){
			    	const index = this.id;
                    const data = this.get_data(cid);
			    	opener.setValue(index, cid, data.title);
			    	window.close();
			    }
HTML;
        echo $victoria->popup();
    }
}
