<?php
/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Alexander (15.08.2023 16:30)
*/

namespace Controls\UI\WEB\Components\God;

use Boot\System\Livewire\Component;
use Illuminate\Contracts\View\View;

class God_button extends Component {

    public bool $toogle = false;
    public string $file;

    function mount(): void
    {
        $this->file = ROOT . get_key_god('minus');
    }

    function render(): View
    {
        $this->checkFile();
        return view('controls::manager.god.components.button', [
            'toogle' => $this->toogle,
        ]);
    }

    /**
     * @return void
     */
    function toggleMinus(): void
    {
        if(file_exists($this->file)){
            unlink($this->file);
            $this->toogle = false;
        } else {
            $fp = fopen($this->file, 'w'); // ("r" - считывать "w" - создавать "a" - добовлять к тексту),мы создаем файл
            fwrite($fp, '');
            fclose($fp);
            $this->toogle = true;
        }
    }

    private function checkFile(): void
    {
        $this->toogle = !file_exists($this->file);
    }
}