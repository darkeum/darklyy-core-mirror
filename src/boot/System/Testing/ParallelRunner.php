<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2026 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Testing;

use Illuminate\Testing\Concerns\RunsInParallel;

if (interface_exists(\ParaTest\RunnerInterface::class)) {
    class ParallelRunner implements \ParaTest\RunnerInterface
    {
        use RunsInParallel;

        /**
         * Runs the test suite.
         *
         * @return int
         */
        public function run(): int
        {
            return $this->execute();
        }
    }
} else {
    class ParallelRunner implements \ParaTest\Runners\PHPUnit\RunnerInterface
    {
        use RunsInParallel;

        /**
         * Runs the test suite.
         *
         * @return void
         */
        public function run(): void
        {
            $this->execute();
        }
    }
}
