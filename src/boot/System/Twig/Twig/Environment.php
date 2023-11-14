<?php

namespace Boot\System\Twig\Twig;

use \Boot\Support\Str;

class Environment extends \Twig\Environment
{
    public function display($name, array $context = []): void
    {
        if(Str::endsWith($name, '.html.twig')) {
            $this->load($name)->display($context);
        } else {
            try {
                $this->load($name)->display($context);
            } catch (\Twig\Error\LoaderError $e) {               
                try {
                    $this->load($name.'.html.twig')->display($context);
                } catch (\Twig\Error\LoaderError $e) {      
                    echo 'Ошибка: ',  $e->getMessage(), "\n";
                }               
            }
        }
        
    }
} 
