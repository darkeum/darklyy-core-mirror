<?php

namespace Boot\System\Core\Generator\Traits;

trait PrinterTrait
{
    public function printStartedMessage($containerName, $fileName)
    {
        $this->printInfoMessage('> Создание (' . $fileName . ') в модуле (' . $containerName . ').');
    }

    /**
     * @param $message
     */
    public function printInfoMessage($message)
    {
        $this->info($message);
    }

    /**
     * @param $type
     *
     * @return void
     */
    public function printFinishedMessage($type)
    {
        $this->printInfoMessage($type . ' успешно создан.');
    }

    /**
     * @param $message
     */
    public function printErrorMessage($message)
    {
        $this->error($message);
    }
}
