<?php

use Boot\Models\Model;
use Boot\Support\Facades\Auth;
use Boot\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \Boot\System\Grid\Utils\Utils;
use Intervention\Image\Facades\Image;
use \Illuminate\Support\Str;


// Контролируемый рандом если нужны шаги в рандоме
if (!function_exists('decimalRand')) {
    function decimalRand($iMin, $iMax, $fSteps = 0.5)
    {
        $a = range($iMin, $iMax, $fSteps);
        return $a[mt_rand(0, count($a) - 1)];
    }
}

// Такое себе ... 
if (!function_exists('grid_column')) {

    function grid_column($col)
    {
        $cols = [
            'title' => $col['title'],
            'ariaTitle' => $col['title'],
            'name' => $col['db'],
            'label' => $col['label'],

            'visible' => $col['visible'] ? 'true' : 'false',
            'className' => $col['className'] ?? '',
            'iconClass' => $col['iconClass'] ?? 'false',
            'width' => $col['width'] ?? 'false',
            'displayFormat' => $col['format_date'] ?? 'false',

            'sortable' => isset($col['sortable']) ? ($col['sortable'] ? 'true' : 'false') : 'true',
            'searchable' => isset($col['search']) ? ($col['search'] === false ? 'false' : 'true') : 'false',
            'render' => isset($col['render']) ? 'render: function ( data, type, row ) { return g_render(data, ' . $col['render'] . ') }' : false,

            'data' => $col['name'],

            'type' => $col['type'] ?? 'input',
            'options' => isset($col['options']) ? Utils::jsonEncode($col['options']) : false,
            'priority' => isset($col['priority']) ? $col['priority'] : false
        ];

        return Utils::jsonEncode($cols, JSON_FORCE_OBJECT) . ',';

    }
}


// Получаем кол-во дней с момента чего либо
if (!function_exists('howMany')) {
    function howMany($date) // $date --> время в формате Unix time
    {
        // if($date == null) { return ''; }

        // $date = strtotime('2019-04-16 09:58:16');
        $stf = 0;
        $cur_time = time();
        $diff = $cur_time - strtotime($date);

        $seconds = array('секунда', 'секунды', 'секунд');
        $minutes = array('минута', 'минуты', 'минут');
        $hours = array('час', 'часа', 'часов');
        $days = array('день', 'дня', 'дней');
        $weeks = array('неделю', 'недели', 'недель');
        $months = array('месяц', 'месяца', 'месяцев');
        $years = array('год', 'года', 'лет');
        $decades = array('десятилетие', 'десятилетия', 'десятилетий');

        $phrase = array($seconds, $minutes, $hours, $days, $weeks, $months, $years, $decades);
        $length = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);

        for ($i = sizeof($length) - 1; ($i >= 0) && (($no = $diff / $length[$i]) <= 1); $i--) {
            ;
        }
        if ($i < 0) {
            $i = 0;
        }
        $_time = $cur_time - ($diff % $length[$i]);
        $no = floor($no);
        $value = sprintf("%d %s ", $no, getPhrase($no, $phrase[$i]));

        if (($stf == 1) && ($i >= 1) && (($cur_time - $_time) > 0)) {
            $value .= time_ago($_time);
        }

        return $value;
    }
}

if (!function_exists('getPhrase')) {
    function getPhrase($number, $titles)
    {
        $cases = array(2, 0, 1, 1, 1, 2);

        return $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    }
}

// ****************** Пользователи и компании
// *****************************************************************************
// Проверка какой это сайт по ENV APP_NAME
if (!function_exists('check_company')) {

    function check_company($text = false, $delimiter = '')
    {
        switch (env('APP_NAME')) {
            case 'AE':
                return isset($text) ? $delimiter . 'atri' : true;
            // break;
            case 'PRIS':
                return isset($text) ? $delimiter . 'pris' : false;
            // break;
            default:
                return true;
        }
    }
}

// Проверка на текущего пользователя
if (!function_exists('current_user')) {

    function current_user($user)
    {
        if (Auth::user() == $user) {
            return true;
        }
        return false;
    }
}

// ****************** Файловые помощники
// *****************************************************************************
// Проверка наличия файла на сервере (в директории)
if (!function_exists('storage_ext')) {

    function storage_ext($file, $disk = 'custom')
    {
        $file = rawurldecode($file);
        $ext = Storage::disk($disk)->exists($file);
        // dd($file);
        // $path = Storage::url($file);
        $path = $file;

        if ($ext) {
            return $path;
        } else {
            return false;
        }
    }
}

// Проверка наличия файла на сервере (в директории) для photo и img
if (!function_exists('storage_ext_img')) {

    function storage_ext_img($file, $bool = true, $disk = 'custom')
    {
        $path = storage_ext($file);

        if ($path) {
            return $path;
        } else {
            return '/media/no-photo.jpg';

        }
    }
}

// Получаем размер файла Storage
if (!function_exists('storage_size')) {

    function storage_size($file, $disk = 'custom')
    {
        $file_url = rawurldecode($file);
        if (storage_ext($file_url)) {

            $size = Storage::disk($disk)->size($file_url);

            if ($size < 1000 * 1024) {
                return number_format($size / 1024, 2) . " Кб";
            } elseif ($size < 1000 * 1048576) {
                return number_format($size / 1048576, 2) . " Мб";
            } elseif ($size < 1000 * 1073741824) {
                return number_format($size / 1073741824, 2) . " Гб";
            } else {
                return number_format($size / 1099511627776, 2) . " Тб";
            }

        } else {
            return false;
        }
    }
}

// Получаем размер файла "Простое"
if (!function_exists('file_size')) {

    function file_size($size = false)
    {
        if ($size) {

            if ($size < 1000 * 1024) {
                return number_format($size / 1024, 2) . " Кб";
            } elseif ($size < 1000 * 1048576) {
                return number_format($size / 1048576, 2) . " Мб";
            } elseif ($size < 1000 * 1073741824) {
                return number_format($size / 1073741824, 2) . " Гб";
            } else {
                return number_format($size / 1099511627776, 2) . " Тб";
            }

        } else {
            return false;
        }
    }
}

// Получаем превью для постов (с проверкой заглушки no photo)
if (!function_exists('preview')) {

    function preview($unit, $param = false, $bool = false)
    {
        // $disk = 'local';
        $no_photo = '/media/no-photo.jpg';

        // Проверка переменной на empty
        if ($unit == false or empty($unit->preview)) {
            return $no_photo;
        }

        // Проверка на string
        if (is_string($unit->preview)) {
            $image = explode('files/', $unit->preview);
            if (storage_ext($image[1])) {
                return 'files/' . $image[1];
            } else {
                return $no_photo;
            }
        }

        switch ($param) {
            default:
                $image = explode('files/', $unit->preview['source']);
                $preview = 'files/' . $image[1];
                break;

            case 'source':
                // $preview = $preview->preview['source'] ?? false;
                $image = explode('files/', $unit->preview['source']);
                $preview = 'files/' . $image[1];
                break;

            case 'full':
                $preview = storage_ext($unit->preview['full']);
                break;

            case 'full_webp':
                $preview = storage_ext($unit->preview['full_webp']);

                if (empty($preview)) {
                    $preview = storage_ext($unit->preview['full']);
                }
                break;
        }

        // $preview = rawurldecode($preview);
        if ($preview) {
            return url($preview);
        } else {
            // Вернуть bool после проверки наличия файла на сервере
            if ($bool) {
                return false;
            } else {
                return $no_photo;
            }
        }
    }
}

// Получаем аватары для пользователей
if (!function_exists('avatar')) {

    function avatar($avatar, $disk = false)
    {
        // $disk = 'local';
        $no_ava = '/media/no-photo.jpg';

        // $avatar = storage_ext($avatar->avatar);

        // Проверка переменной на empty
        if (empty($avatar->avatar)) {
            return $no_ava;
        }

        if ($avatar) {
            return $avatar;
        } else {
            return $no_ava;
        }
    }
}


// ****************** Файловые помощники - Генераторы
// *****************************************************************************
// Генератор картинок (Подготовитель изображений для сайта) (Создает webp если нет)
if (!function_exists('prepare_image')) {

    function prepare_image($path)
    {
        if (!$path) {
            return false;
        }
        $image = rawurldecode('files/' . explode('files/', $path)[1]);

        if (!Storage::disk('custom')->exists(strval($image)) or $image == false) {
            return false;
        }

        $src = ROOT . $image;
        //$src = public_path($image);
        $info = pathinfo($src);
        $ext = $info['extension'];
        $name = $info['filename'];

        //$module = explode('/', $image)[1];
        $full_webp = explode($name, $image)[0];

        ## Main
        // Создание основного файла
        switch (mb_strtolower($ext)) {
            default:
                $img = imageCreateFromJpeg($src);
                break;

            case 'png':
                $img = imageCreateFromPng($src);
                break;

            case 'jpg':
                $img = imageCreateFromJpeg($src);
                break;

            case 'gif':
                $img = imageCreateFromGif($src);
                break;
        }

        ## thumb
        // Проверка папки thumb
        $thumb = $info['dirname'] . '/thumb';
        if (!file_exists($thumb)) {
            mkdir($thumb, 0777, true);
        }

        // Создание файла webp в папке thumb
        imageWebp($img, $thumb . '/' . $name . '.webp', 80);
        //imagedestroy($img);

        $data = [
            'source' => $image,
            'full' => $image,
            'full_webp' => '/' . $full_webp . $name . '.webp'
        ];

        return $data;

    }

}

// Генератор файлов MS Office (doc, docx, xls, xlsx)
if (!function_exists('generate_office')) {

    function generate_office($type, $module, $template, $data, $filename = 'New File')
    {
        $fileName = false;
        $template = modules_path($module) . '\Resources\Templates\/' . $template . '.' . $type;

        // Файлы MS Word
        if ($type == 'docx' || $type == 'doc') {
            $templateProcessor = new TemplateProcessor($template);

            if (is_array($data)) {
                foreach ($data as $d) {
                    $templateProcessor->setValue($d['name'], $d['value']);
                }
            }

            $fileName = $filename . '.' . $type;
            $templateProcessor->saveAs($fileName);
        }

        // Файлы MS Exell
        if ($type == 'xlsx' || $type == 'xls') {

            $spreadsheet = IOFactory::load($template);
            $sheet = $spreadsheet->getActiveSheet();

            if (is_array($data)) {
                foreach ($data as $d) {
                    $sheet->setCellValue($d['name'], $d['value']);
                }
            }

            $fileName = $filename . '.' . $type;

            // $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
            $writer = new Xlsx($spreadsheet);
            $writer->save($fileName);
        }

        return $fileName;

    }

}

// ****************** Ещё
// *****************************************************************************
// Генератор breadcrumbs
// Подать на вход name (Имя модуля для Lang файле blade и crumbs массив дополнительных "крошек" )
if (!function_exists('breadcrumbs')) {

    function breadcrumbs($name, $crumbs = false)
    {
        // Первые default пункты из файла конфига
        $breadcrumbs = config('menu.breadcrumbs');

        if ($name) {
            $lang = trans($name . '::lang.module');
            array_push($breadcrumbs, ['name' => $lang, 'url' => url($name)]);
        }

        if (is_array($crumbs)) {
            foreach ($crumbs as $crumb) {
                array_push($breadcrumbs, $crumb);
            }
        }

        return $breadcrumbs;
    }
}

// Получаем название месяца на русском
if (!function_exists('getRusMonthName')) {

    function getRusMonthName($n, $param = false)
    {
        $rusMonthNames = [
            1 => 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
        ];

        if ($param) {
            return $rusMonthNames[$n];
        } else {
            return substr($rusMonthNames[$n], 0, 6);
        }

    }
}

// Генератор категорий для меню sidebar
if (!function_exists('gen_cats')) {

    function gen_cats($data)
    {
        $cats = [];
        $data = explode(',', preg_replace('/\s+/', '', $data));

        // Добавляем маркер генерации в начало массива
        array_push($cats, 'gen');

        // Config/menu.config.php
        $conf = config('menu.cats');

        foreach ($data as $key => $val) {

            if ($conf[$key]['tag'] ?? false) {

                $key = array_search($val, array_column($conf, 'tag'));
                $cats[] = $conf[$key];
                // $cats[] = Arr::add($conf[$key], 'tag', $val);
            }
        }

        return $cats;
    }
}

// Получить категорию по ее имени для ссылки
if (!function_exists('get_cats')) {

    function get_cats($cat)
    {
        $conf = config('menu.cats');

        foreach ($conf as $con) {
            if ($con['tag'] = $cat) {
                return $con;
            }
        }

        return false;
    }

}


// Текущий модуль из URI
if (!function_exists('now_module')) {
    function now_module($param = false)
    {
        if ($param) {
            $path = explode('/', explode('module/', Route::current()->uri())[1])[0];
        } else {
            $path = Request::segment(3);
        }
        return $path;
    }
}

// Генератор preview
if (!function_exists('preview_create')) {
    function preview_create($file, $module, $eq)
    {
        $conf = config('setting.preview');
        $disk = $conf['disk'];

        //
        foreach ($eq['preview'] as $path) {
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        }

        $path = $module . '/' . $eq->id;
        // Создаем главный файл
        $full = storage_img_load($file, $path);

        // Создаем мини файл
        $webp = storage_img_load($file, $path . '/thumb', false, 'webp', 80);

        return [
            'source' => $full,
            'full' => $full,
            'full_webp' => $webp
        ];
    }
}

// Сжатие и загрузка картинки
if (!function_exists('storage_img_load')) {
    function storage_img_load($img_url, $folder, $custom_width = false, $custom_ext = false, $custom_quality = false)
    {
        // ПАРАМЕТРЫ
        // Стандартная ширина загружаемого изображения
        $conf = config('setting.preview');

        $ext = $conf['ext'];                // jpg default
        $disk = $conf['disk'];              // local default
        $watermark = $conf['watermark'];    // false default
        $width = $conf['width'];            // 1000 default
        $height = null;                     // null default
        $quality = $conf['quality'];        // 100 default

        $image = Image::make($img_url);

        if ($custom_ext) {
            $ext = $custom_ext;
        }
        if ($custom_width) {
            $width = $custom_width;
        }
        if ($custom_width == null) {
            $width = $image->width();
            $height = $image->height();
        }
        if ($custom_quality) {
            $quality = $custom_quality;
        }
        //---------------------------------------

        // Водяной знак по умолчанию - Выкл
        if ($watermark) {
            // insert watermark at bottom-right corner with 10px offset
            //$resize->insert(asset('assets/images/watermark.png'), 'bottom-right', 10, 5);
        }

        $image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });

        $image->encode($ext, $quality);

        $hash = md5($image->__toString());
        $path = "files/" . $folder . "/{$hash}." . $ext;

        // Загружаем в папку
        Storage::disk($disk)->put($path, $image);

        return $path;
    }
}

if (!function_exists('storage_img_delete')) {
    function storage_img_delete($img)
    {
        if (Storage::disk('local')->exists($img)) {
            Storage::disk('local')->delete($img);
        }
    }
}

// Функция транслитерации
if (!function_exists('text_translit')) {
    function text_translit($string)
    {
        // Массив символов. элемент массива - русская буква, значение элемента - латинская буква на выходе
        $letters = array(
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
            'Е' => 'E', 'Ё' => 'YO', 'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I',
            'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
            'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C', 'Ч' => 'CH',
            'Ш' => 'SH', 'Щ' => 'CSH', 'Ь' => 'MZN', 'Ы' => 'Y', 'Ъ' => 'TZN',
            'Э' => 'EA', 'Ю' => 'YU', 'Я' => 'YA',
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
            'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
            'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
            'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'csh', 'ь' => 'mzn', 'ы' => 'y', 'ъ' => 'tzn',
            'э' => 'ea', 'ю' => 'yu', 'я' => 'ya', ' ' => '_'
        );
        return str_replace(array_keys($letters), array_values($letters), $string);  // Замена символов
    }
}

/**
 * Краткое ФИО пользователя `Васькин П. П.`
 *
 * @param object $user
 * @return string
 */
if (!function_exists('user_short_name')) {
    function user_short_name($user, $full = false): string
    {
        // Если не объект то значит это ID и создадим объект сами
        if (!is_object($user)) {
            $user = \Boot\Models\Users::find($user);
        }

        if (!$full) {

            $str = function ($text) {
                return Str::substr($text, 0, 1);
            };

            return $user?->surname . ' ' . $str($user?->name) . '. ' . $str($user?->ochestvo) . '.';

        } else {
            return $user?->surname . ' ' . $user?->name . ' ' . $user?->ochestvo;
        }
    }
}
