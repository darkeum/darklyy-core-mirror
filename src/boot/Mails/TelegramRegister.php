<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/
 
namespace Boot\Mails;
 
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
 
class TelegramRegister extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
 
    public function __construct($code)
    {
        $this->code = $code;
    } 
  
    public function build()
    {
        return $this->view('telegram');
    }
}
    