<?php

namespace Boot\System\Translation;

use Boot\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('translation', function ($app) {
            return new TranslationManager('ru');
        });
    }

    public function boot()
    {

        $module = ['news', 'support', 'feedback', 'job', 'organizations', 'scan', 'portfolio', 'order', 'service', 'cashbox', 'reviews', 'project', 'promotions', 'certificates', 'bank', 'home', 'partner', 'control', 'scada', 'victoria', 'task2', 'selection', 'sales2', 'dev', 'warranty_card', 'users', 'chat', 'away', 'org', 'calculation', 'rept', 'catalog', 'price2'];
        $translator = app('translation');
        $translator->addLoader('array', new  \Symfony\Component\Translation\Loader\ArrayLoader());
        $translator->addLoader('php', new  \Symfony\Component\Translation\Loader\PhpFileLoader());
        foreach ($module as $key => $value) {
            $lang = ROOT . 'usr/modules/' . $value . '/resources/langs/' . $value . '.ru.lang.php';
            if (file_exists($lang)) {
                $translator->addResource('php', $lang, 'ru');
            }
        }
        // $translator->addResource('php', ROOT . 'resources/langs/ru.grid.lang.php', 'ru'); // OLD
        // Файл мог быть удалён (переводы переехали в пакет грида) — без проверки
        // Symfony бросает NotFoundResourceException на первом же `trans()`.
        // Домен `grid`: legacy-код обращается к ключам как `grid.null`, `grid.uid`, …
        $gridLang = ROOT . 'resources/lang/ru/grid.php';
        if (is_file($gridLang)) {
            $translator->addResource('php', $gridLang, 'ru', 'grid');
        }
    }
}
