<?php

namespace Catalog\UI\CLI\Commands;

use Boot\Support\Facades\Storage;
use Boot\System\Office\Spreadsheet\SpreadsheetSupport;
use \Boot\System\Core\Abstracts\Commands\ConsoleCommand;

class GrundfosCommand extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'grundfos';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Parser';

    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        $patch = "usr/modules/catalog/Grundfos";
        $files = Storage::files($patch);
        $files_xlsx = [];
        $files_name_xlsx = [];
        foreach ($files as $file) {
            $pathinfo = pathinfo($file);
            if ($pathinfo["extension"] == "xlsx") {
                $files_xlsx[] = $pathinfo;
                $files_name_xlsx[] = $pathinfo["filename"];
            }
        }
        $choice = array_search($this->choice(
            'Выберите файл',
            $files_name_xlsx,
        ), $files_name_xlsx);

        $file = $files_xlsx[ $choice ];
        $folder_name = str_replace("Категория ", "", $file["filename"]);
        $folder = $patch."/".$folder_name;
        if(!is_dir(ROOT.$folder)){
            throw new \Exception("Папка `{$folder}` не найдена!");
        }
        $excel = new SpreadsheetSupport;
        $excel->init_file(["tmp_name" => ROOT.$file["dirname"]."/".$file["basename"]]);
        $data = $excel->excel_to_array();
        for($i = 1; $i<count($data);$i++){
            try {
                $line = $data[$i];
                $title = $line[0];
                $sku = $line[1];

                $this->info("Наименование: {$title}; Артикул: {$sku}");

                $points = json_decode($line[6]);

                // if(file_exists($img_patch."dimensionaldrawing.png")){
                //     $files["dimensionaldrawing"] = $img_patch."dimensionaldrawing.png";
                // }
                // if(file_exists($img_patch."electricaldiagram.png")){
                //     $files["electricaldiagram"] = $img_patch."electricaldiagram.png";
                // }

                //создание товара
                // $catalog = \Catalog_v2\Model\Catalog::updateOrCreate(
                //     ["sku" => $sku, "man" => "50",], ["title" => $title, "uid" => 79, "unit" => 796, "category" => ",363,", "hidden" => 0]
                // );
                $img_patch = ROOT."{$folder}/{$sku}/";
                //сохранить изображение
                // if(file_exists($img_patch."picture.png")){
                //     $preview = "/files/catalog/{$catalog->id}/".uniqid().".png";
                //     rename($img_patch."picture.png", ROOT.$preview);
                //     $catalog->update(["preview" => $preview]);
                // }
                //сохранить точки

                //сохранить размеры
                //сохранить схемы подключения
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
    }
}
