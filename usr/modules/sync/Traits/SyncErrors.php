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

trait SyncErrors
{
    public function syncError($error){
        $this->console->sectionError->writeln('<error>'.$error.'</error>');  
    }
}
