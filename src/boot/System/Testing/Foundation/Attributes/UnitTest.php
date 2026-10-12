<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2026 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Testing\Foundation\Attributes;

use Attribute;

/**
 * Run a test without configuring the Laravel framework.
 */
#[Attribute(Attribute::TARGET_METHOD)]
class UnitTest
{
}
