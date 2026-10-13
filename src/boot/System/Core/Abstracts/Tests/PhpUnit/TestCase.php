<?php

namespace Boot\System\Core\Abstracts\Tests\PhpUnit;

use Boot\Contracts\Console\Kernel;
use Boot\System\Core\Traits\HashIdTrait;
// use Boot\System\Core\Traits\TestCaseTrait;
use Boot\System\Core\Traits\TestsTraits\PhpUnit\TestsAuthHelperTrait;
use Boot\System\Core\Traits\TestsTraits\PhpUnit\TestsMockHelperTrait;
use Boot\System\Core\Traits\TestsTraits\PhpUnit\TestsRequestHelperTrait;
use Boot\System\Core\Traits\TestsTraits\PhpUnit\TestsResponseHelperTrait;
use Boot\System\Testing\Foundation\LazilyRefreshDatabase;
use Boot\System\Testing\Foundation\RefreshDatabaseState;
use Boot\System\Testing\Foundation\TestCase as LaravelTestCase;

abstract class TestCase extends LaravelTestCase
{

}
