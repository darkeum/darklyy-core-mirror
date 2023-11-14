<?php

namespace Boot\System\Core\Generator\Interfaces;

interface ComponentsGenerator
{
    /**
     * Считывает все данные для создаваемого компонента (а также сопоставления пути, файла и заглушек)
     *
     * @return  mixed
     */
    public function getUserInputs();
}
