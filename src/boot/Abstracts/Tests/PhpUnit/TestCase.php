<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\Abstracts\Tests\PhpUnit;

use Boot\System\Core\Abstracts\Tests\PhpUnit\TestCase as AbstractTestCase;
use Faker\Generator;
use Illuminate\Contracts\Console\Kernel as CoreConsoleKernel;

abstract class TestCase extends AbstractTestCase
{
    /**
     * Настройте тестовую среду перед каждым тестом.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function createApplication()
    {
        $this->baseUrl = env('API_FULL_URL'); 

        $this->overrideSubDomain();

        $app = require __DIR__ . '/../../../../../bootstrap/app.php';

        $app->make(CoreConsoleKernel::class)->bootstrap();

        $this->faker = $app->make(Generator::class);

        return $app;
    }
}
