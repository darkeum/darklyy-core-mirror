<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @description ViewTransfer Command
*/

namespace Storage\Bots\Telegram;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use \Boot\Models\UsersToken;
use Storage\Model\StockTransfer;
use Storage\Model\StockTransferDetail;

class ViewTransferCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "view_transfer";

    protected $pattern = "{id}";

    /**
     * @var string Command Description
     */
    protected $description = "Просмотр перемещений";

    /**
     * @inheritdoc
     */
    public function handle()
    {      
        $this->replyWithChatAction(['action' => Actions::TYPING]);
        $argument = $this->getArguments();
        if(!empty($argument['id'])){
            $transferDetail = StockTransferDetail::where('transfer_id', $argument['id'])->get();
            $text  = '';
            foreach ($transferDetail as $value) {
                $text .= $value->s_name." (<i>".$value->s_sku."</i>) "."\n<b>Количество: ".$value->count." </b>\n" ;
                $text .= "----------------------------\n";
            }            
        } else {
            $text = 'Не указан номер перемещения';
        }
        $this->replyWithMessage([
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);   
        
    }
}
