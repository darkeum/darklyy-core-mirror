<?php

namespace Catalog\UI\CLI\Commands;

use Boot\Support\Facades\Http;
use Symfony\Component\Console\Helper\ProgressBar;
use \Boot\System\Core\Abstracts\Commands\ConsoleCommand;
use Catalog\Models\Catalog;
use Catalog_v2\Model\CatalogManufacture;

class SyncCatalog extends ConsoleCommand
{
    /**
     * Имя и параметры консольной команды.
     *
     * @var string
     */
    protected $signature = 'catalog:sync:start
    {category : Идентификатор базы программы для синхронизации}
    {--force : Принудительная работа с игнорированием ключевого файла}
    {--once : Выполнить команду однократно}
    {--host= : Имя хоста}';

    /**
     * Описание консольной команды.
     *
     * @var string
     */
    protected $description = 'Parser';
    public $commandOutput;
    /**
     * Обработчик консольной команды.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->commandOutput = $this->output->getOutput();
        $cat = [580,626,582,584,583,590,588,586,585,587,627,620,581,356,358,625,360,361,355,531,323,589];
        foreach ($cat as $kk => $vv) {
            # code...
                //fore
        $response_meta = Http::withToken('69|6wK6OrZZGWltRDBg6cnApGLtoiBUTua8xHWaseiW')->get('http://api.atri-energo.ru/v1/catalog/list', [
            'search' => 'category:' . $vv
        ]);
        $meta = $response_meta->object();
        $sectionMain = $this->commandOutput->section();
        $progressBarMain = new ProgressBar($sectionMain);
        $progressBarMain->setFormat(
            "%message%\n%current%/%max% [%bar%] %percent:3s%%"
        );
        $current_page = 1;
        $progressBarMain->setMessage('Синхронизация начата (страница ' . $current_page . '):', 'message');
        $progressBarMain->start($meta->meta->pagination->total_pages);
        do {
            $response = Http::withToken('69|6wK6OrZZGWltRDBg6cnApGLtoiBUTua8xHWaseiW')->get('http://api.atri-energo.ru/v1/catalog/list', [
                'search' => 'category:' . $vv,
                'page' => $current_page,
            ]);

            $data = $response->object();
            $section = $this->commandOutput->section();
            $progressBarSecond = new ProgressBar($section);
            $progressBarSecond->setFormat("%current%/%max% [%bar%] %percent:3s%% %message%");
            $progressBarSecond->start($data->meta->pagination->count);
            foreach ($data->data as $key => $value) {
                $action = 'add';
                if (Catalog::where('sync_id', $value->id)->count()) {
                    $action = 'update';
                }              
                $progressBarSecond->setMessage(($action == 'add' ? 'Добавление: ' : 'Обновление: ') . $value->title, 'message');
                $progressBarSecond->advance();
                // dump($value->man);
                if (!is_null($value->man)) {
                    // dump(CatalogManufacture::where('sync_id', $value->man)->count());
                    if (empty(CatalogManufacture::where('sync_id', $value->man)->count())) {
                        $manufacture_item = $value->catalog_manufacture;
                        $manufacture_item->sync_id = $value->catalog_manufacture->id;
                        $manufacture_item->id = null;
                        $manufacture_id = CatalogManufacture::create((array)$manufacture_item)->id;
                    } else {
                        $manufacture_id = (CatalogManufacture::where('sync_id', $value->man)->first())->id;
                    }
                } else {
                    $manufacture_id = null;
                }
                $item = $value;
                $item->sync_id = $value->id;
                $item->catalog_manufacture = null;
                $item->man = $manufacture_id;
                $item->id = null;
                $item->uid = 1;
                if ($action == 'add') {
                    Catalog::create((array)$item);
                } else {
                    // Catalog::where('sync_id', $value->id)->update((array)$item);
                }
            }
            $progressBarSecond->finish();
            $section->clear();
            $current_page++;
            $progressBarMain->setMessage('Синхронизация начата (страница ' . $current_page . '):', 'message');
            $progressBarMain->advance();
        } while (isset($data->meta->pagination->links->next));
        $progressBarMain->finish();
    }
    }
}
