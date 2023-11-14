<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
*/

namespace Boot\App\Loaders;

use \Boot\App\Application;
use \Dotenv\Dotenv;
use \Dotenv\Exception\InvalidFileException;
use \Boot\Support\Env;
use \Symfony\Component\Console\Input\ArgvInput;
use \Symfony\Component\Console\Output\ConsoleOutput;


class LoadEnvironmentVariables
{
    /**
     * Загрузка данных приложения
     *
     * @param  \Boot\App\Application  $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        if ($app->configurationIsCached()) {
            return;
        }

        $this->checkForSpecificEnvironmentFile($app);

        try {
            $this->createDotenv($app)->safeLoad();
        } catch (InvalidFileException $e) {
            $this->writeErrorAndDie($e);
        }
    }

    /**
     * Определение, существует ли файл пользовательской среды, соответствующий APP_ENV.
     *
     * @param  Boot\Contracts\App\Application  $app
     * @return void
     */
    protected function checkForSpecificEnvironmentFile($app)
    {
        if (
            $app->runningInConsole() &&
            ($input = new ArgvInput)->hasParameterOption('--env') &&
            $this->setEnvironmentFilePath($app, $app->environmentFile() . '.' . $input->getParameterOption('--env'))
        ) {
            return;
        }

        $environment = Env::get('APP_ENV');

        if (!$environment) {
            return;
        }

        $this->setEnvironmentFilePath(
            $app,
            $app->environmentFile() . '.' . $environment
        );
    }

    /**
     * Установка пользовательского файл среды.
     *
     * @param  Boot\Contracts\App\Application  $app
     * @param  string  $file
     * @return bool
     */
    protected function setEnvironmentFilePath($app, $file)
    {
        if (is_file($app->environmentPath() . '/' . $file)) {
            $app->loadEnvironmentFrom($file);

            return true;
        }

        return false;
    }

    /**
     * Создание экземпляра Dotenv.
     *
     * @param  Boot\Contracts\App\Application  $app
     * @return \Dotenv\Dotenv
     */
    protected function createDotenv($app)
    {
        return Dotenv::create(
            Env::getRepository(),
            $app->environmentPath(),
            $app->environmentFile()
        );
    }

    /**
     * Вывод информации об ошибке на экран и выход.
     *
     * @param  \Dotenv\Exception\InvalidFileException  $e
     * @return void
     */
    protected function writeErrorAndDie(InvalidFileException $e)
    {
        $output = (new ConsoleOutput)->getErrorOutput();

        $output->writeln('Файл среды недействителен!');
        $output->writeln($e->getMessage());

        exit(1);
    }
}
