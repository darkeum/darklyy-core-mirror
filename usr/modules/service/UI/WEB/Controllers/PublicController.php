<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Mark Andreev
* @description Service Public Controller
*/

namespace Service\UI\WEB\Controllers;

use \Boot\Controller\AbstractController;
use Certificates\Models\Certificates;
use \Portfolio\Model\Portfolio;
use \Boot\System\Categories\Categories;
use \Boot\System\Validation\Validator;
use \Boot\Services\Router;
use \Boot\Services\Request;
use \Boot\Support\Cache;
use \Boot\Support\Facades\Auth;
use \Order\Model\Order;
use \Order\Model\OrderJob;
use \Boot\Support\Facades\Url;

use Illuminate\Support\Facades\View;

use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;


class PublicController extends AbstractController
{
    // index
    public static function index($route = null)
    {
        $breadcrumbs = breadcrumbs('service');

        $items = [
            [
                'name' => 'Насосное оборудование «WILO»',
                'url' => 'service/product/wilo',
                'img' => asset('assets/images/service/s3.png')
            ],
            [
                'name' => 'Теплообменное оборудование «РИДАН»',
                'url' => 'service/product/ridan',
                'img' => asset('assets/images/service/s1.png')
            ],
            [
                'name' => 'Оборудование для учета воды и тепла «ТЕРМОТРОНИК»',
                'url' => 'service/product/termotronic',
                'img' => asset('assets/images/service/s2.png')
            ],
            [
                'name' => 'Оборудование «Danfoss»',
                'url' => 'service/product/danfoss',
                'img' => asset('assets/images/service/s4.png')
            ],
            [
                'name' => 'Другое теплотехническое оборудование',
                'url' => 'service/product/other',
                'img' => asset('assets/images/service/s5.png')
            ],
        ];

        $categories = [
            ['name' => 'Сервис', 'url' => router('service.index')],
            ['name' => 'Прайс-лист', 'url' => url('news')],
            ['name' => 'Сертификаты', 'url' => url('certificates/sertifikaty-servisa')],
            ['name' => 'Рекламации', 'url' => route('service.complaints.index')],
            ['name' => 'Заявки на сервис', 'url' => router('service.order.index')],
        ];

        return view('service::list', [
            'breadcrumbs' => $breadcrumbs,
            'categories' => $categories,
            'items' => $items,
        ]);

    }

    // Product
    public static function product($route = null)
    {
        $t_route = [
            'wilo' => 'Насосное оборудование «WILO»',
            'ridan' => 'Теплообменное оборудование «РИДАН»',
            'danfoss' => 'danfoss',
            'termotronic' => 'Оборудование для учета воды и тепла «ТЕРМОТРОНИК»',
            'other' => 'Другое теплотехническое оборудование',
        ];

        if (isset($t_route[$route]) and View::exists('service::product.' . $route)) {
            $title = $t_route[$route];
        } else {
            return redirect()->route('service.index');
        }

        $categories = [
            ['name' => 'Сервис', 'url' => router('service.index')],
            ['name' => 'Прайс-лист', 'url' => url('news')],
            ['name' => 'Сертификаты', 'url' => url('certificates/sertifikaty-servisa')],
            ['name' => 'Рекламации', 'url' => route('service.complaints.index')],
            ['name' => 'Заявки на сервис', 'url' => router('service.order.index')],
        ];

        $breadcrumbs = [
            ['name' => 'Главная', 'url' => url('/')],
            ['name' => 'Сервис', 'url' => route('service.index')],
            ['name' => $title, 'url' => url('/')],
        ];

        // Получаем сертификаты по тегу
        //$certs = Certificates::with('categories')
        //    ->orderBy('id', 'desc')
        //    ->where('status', 1)
        //    ->whereDate('date_end', '<', date('Y-m-d H:i:s'))
        //    ->where('tag', $route);

        return view('service::product.' . $route, [
            'title' =>  $title,
            'breadcrumbs' => $breadcrumbs,
            'categories' => $categories,
             // 'certs' => $certs->get(),
        ]);
    }

    // Other
    public static function order($categories = null, $post = null)
    {

        $breadcrumbs = [
            ['name' => 'Главная', 'url' => '/'],
            ['name' => 'Сервис', 'url' => router('service.index')],
            ['name' => 'Форма связи', 'url' => router('service.order.index')],
        ];

        $categories = [
            ['name' => 'Сервис', 'url' => router('service.index')],
            ['name' => 'Прайс-лист', 'url' => url('news')],
            ['name' => 'Сертификаты', 'url' => url('certificates/sertifikaty-servisa')],
            ['name' => 'Рекламации', 'url' => url('content/documents.html')],
            ['name' => 'Заявки на сервис', 'url' => router('service.order.index')],
        ];

        $cats = new Categories();

        return view('service::order', [
            'breadcrumbs' => $breadcrumbs,
            'cats' => $cats->getModuleCategories('portfolio'),
            'categories' => $categories

        ]);
    }

    // Other - Sends
    public function service()
    {

        // request = {"cat":"605","org":null,"object":null,"address":null,"name":null,"email":null,"phone":null,"message":null,"sku":null,"type":null,"serial":null,"date":null,"numb":null,"url":"\/service\/order\/send\/service"}

        return request();
    }

    // Complaints
    public static function complaints()
    {
        $breadcrumbs = [
            ['name' => 'Главная', 'url' => '/'],
            ['name' => 'Сервис', 'url' => router('service.index')],
            ['name' => 'Рекламации', 'url' => ''],
        ];

        $categories = [
            ['name' => 'Сервис', 'url' => router('service.index')],
            ['name' => 'Прайс-лист', 'url' => url('news')],
            ['name' => 'Сертификаты', 'url' => url('certificates/sertifikaty-servisa')],
            ['name' => 'Рекламации', 'url' => route('service.complaints.index')],
            ['name' => 'Заявки на сервис', 'url' => router('service.order.index')],
        ];

        return view('service::complaints.form', [
            'title' => 'Приём рекламаций',
            'breadcrumbs' => $breadcrumbs,
            'categories' => $categories
        ]);
    }

    // Complaints - Sends
    public static function complaints_send()
    {
        // Физ. лицо - request = {"c_type":"","c_why":"","c_fio":"","c_phone":"","c_email":"","c_manager":"","c_comment":"","url":""}
        // Компания - request = {"c_type":"","c_why":"","c_company":"","c_phone":"","c_email":"","c_comment":"","url":""}
        // "_token":"" - Обязательный токен Laravel защиты
    }

    public static function template()
    {
        // Word
        //$templateProcessor = new TemplateProcessor(modules_path('service') . '\Resources\Templates\test.docx');
        //$templateProcessor->setValue('id', '999');
        //$templateProcessor->setValue('name', '888');
        //$templateProcessor->setValue('time', '666');
        //
        //$fileName = 'Новый файл' . '.docx';
        //$templateProcessor->saveAs($fileName);
        //return response()->download($fileName)->deleteFileAfterSend(true);

        // Exel
        $spreadsheet = IOFactory::load(modules_path('service') . '\Resources\Templates\test.xlsx');
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World !');
        $sheet->setCellValue('A5', 'Hello World !');
        $sheet->setCellValue('A10', 'Hello World !');
        $fileName = 'Новый файл' . '.xlsx';

        // $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileName);

        return response()->download($fileName)->deleteFileAfterSend(true);

        // Или
        //$data = [
        //    ['name' => 'id', 'value' => 'ididid'],
        //    ['name' => 'name', 'value' => 'namenamename'],
        //    ['name' => 'time', 'value' => 'timetimetime'],
        //];
        // return response()->download(generate_office('docx', 'service', 'test', $data, 'Test'))->deleteFileAfterSend(true);

    }

}
