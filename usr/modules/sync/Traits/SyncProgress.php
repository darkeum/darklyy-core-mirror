<?php

/**
 * @name        JMY CMS
 * @link        http://jmy.su/
 * @copyright   Copyright (C) 2012-2022 JMY LTD
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      Komarov Ivan
 */

namespace Sync\Traits;

use Symfony\Component\Console\Helper\ProgressBar;

trait SyncProgress
{
    protected $section;
    protected $progressBar;

    public function progressStart($count, $message)
    {
        $this->section = $this->console->commandOutput->section();
        $this->progressBar = new ProgressBar(  $this->section);
        $this->progressBar->setFormat("%current%/%max% [%bar%] %percent:3s%% %message%");
        $this->progressBar->setMessage($message, 'message');
        $this->progressBar->start($count);
    }

    public function progressFinish()
    {
        $this->progressBar->finish();
        $this->section->clear();
    }

    public function progressAdvance()
    {
        $this->progressBar->advance();
    }

    public function progressSetMessage($message)
    {
        $this->progressBar->setMessage($message, 'message');
    }
}
