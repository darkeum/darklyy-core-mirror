<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan & Volkov Aleksandr
* @description Messages class
*/


namespace Boot\System\Messages\Adapter;

use \Boot\Support\Facades\Config;

class EmailAdapter extends AbstractAdapter
{
    protected $title = null;
    protected $text = null;
    protected $url = null;
    protected $uid = null;
    protected $email = null;

    public function __construct($parameters)
    {
        // print_r($parameters->parameterSms);
        $this->title = $parameters->title ?? null;
        $this->text = $parameters->text ?? null;
        $this->url = $parameters->url ?? null;
        $this->uid = $parameters->uid ?? null;
        $this->email = $parameters->parameterEmail['email'] ?? null;
    }

    public function checkParameters()
    {
    }

    public function getParameters()
    {
    }

    public function send()
    {
        require_once ROOT . '/boot/sub_classes/path_to_swift/lib/swift_required.php';
        require_once ROOT . '/boot/sub_classes/sendemail.class.php';
        $sm = new \sendemail();
        $sm->templatesPath = ROOT . "/usr/tpl/mail/";
        $sm->emailFrom = 'info@atri-energo.ru';
        $sm->fromName = 'ООО «АТРИ-ЭНЕРГО»';
        $sm->emailTo = $this->email;
        // $sm->toName = $result_user['name'];
        $sm->subject = $this->title;
        $sm->mTitle = $this->title;
        $sm->mText = $this->text;
        $varDataArray = array(
            '{title}' => $this->title,
            '{text}' => $this->text,
        );
        if (!empty($this->url)) {
            $varDataArray['{url}'] = '<a href="' . $this->url . '" style="background-color:#ff5722;border-radius:3px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:18px;line-height:38pt;text-align:center;text-decoration:none;-webkit-text-size-adjust:none; width: 142pt;font-family:\'Roboto\',\'Helvetica Neue\',Helvetica,Arial,sans-serif;box-shadow: 0 4px 6px 0 rgba(0, 0, 0, 0.07);">Перейти</a>';
        }
        $sm->varData = $varDataArray;
        $sm->templateDirname = "default";
        $send = $sm->Send();
    }
}
