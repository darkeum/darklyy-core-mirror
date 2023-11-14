<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Project\UI\CLI\Commands;

use Boot\System\Database\DB;
use Catalog_v2\Model\Catalog;
use Boot\Support\Facades\Storage;
use Boot\System\Image\Facades\Image;
use Boot\System\Core\Abstracts\Commands\ConsoleCommand;

class PumpImagesCorrection extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'project:pump:images:correction';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Корректировка изображений CNP';

    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach (Catalog::where('man', 270)->get() as $key => $value) {
            $cert = DB::table('cert_relation')
                ->leftJoin('cert', 'cert.id', '=', 'cert_relation.cert_id')
                ->leftJoin('attach', 'cert.aid', '=', 'attach.id')
                ->where('cert_relation.cid', 165465)
                ->whereIn('cert.type', [12, 13])
                ->get()
                ->toArray();
            if(!empty($cert)){
                foreach ($cert as $v) {
                    // var_dump($v->url);
                    // var_dump(Storage::exists($v->url));
                    if (Storage::exists($v->url)) {
                        $img = Image::make(Storage::get($v->url));
                        $img->resize(640, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $img->crop(510, $img->height()-45);                    
               
                        $img->save('bar.jpg');
                        dd($img);
                    }
                }
            }
        }
    }
}
