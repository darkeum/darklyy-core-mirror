<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2022 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @description ElectronicSignTransfer Notification
*/

namespace Storage\Notifications;

use \Illuminate\Bus\Queueable;
use \Illuminate\Contracts\Queue\ShouldQueue;
use \Boot\System\Notifications\Messages\MailMessage;
use \Boot\System\Notifications\Messages\ViberMessage;
use \Boot\System\Notifications\Notification;
use \NotificationChannels\Telegram\TelegramMessage;
use \Boot\Support\Facades\Config;
use \Storage\Model\StockTransfer;
use \Boot\Models\Users;
use \Stocks\Models\Stock;


class ElectronicSignTransfer extends Notification implements ShouldQueue
{
    use Queueable;

    public $transferId = '';
    public $transferData = '';
    public $confirmationId = '';
    public $confirmationCode = '';

    /**
     * Создание экземпляра сообщения
     *
     * @return void
     */
    public function __construct($params = [])
    {
       $this->transferId = $params['id'] ?? '';
       $this->transferData = $params['data'] ?? '';
       $this->uid = $params['uid'] ?? '';
       $this->getElectronicConfirmation();
    }

    /**
     * Указание каналов рассылки
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['telegram', 'viber'];
    }
    
    /**
     * Получение данных о подтверждении
     *
     * @return void
     */
    public function getElectronicConfirmation(){
        $ElectronicConfirmationAdd = [
            "pid" => $this->transferId,
            "module" => "transfer",
            "uid" => $this->uid,
        ];
        $where = $ElectronicConfirmationAdd;
        $where["used"] = 0;
        $eData = \Boot\Models\ElectronicConfirmation::where($where)->orderByDesc("id")->first();
        if(!empty($eData)){
            $iid = $eData->id;
            $code = $eData->code;
        } else {
            $code = \Boot\Models\ElectronicConfirmation::generate_code();
            $ElectronicConfirmationAdd["code"] = $code;
            $ElectronicConfirmation = \Boot\Models\ElectronicConfirmation::create($ElectronicConfirmationAdd);
            $iid = $ElectronicConfirmation->id;
        }
        $this->confirmationId = $iid;
        $this->confirmationCode = $code;
    }
    
    /**
     * Получение уведомления в Telegram
     *
     * @param  mixed $notifiable
     * @return void
     */
    public function toTelegram($notifiable)
    {
        $userToken = $notifiable->token->where('platform', 'telegram')->first();     
        if(!empty($userToken)){
            $token = $userToken->token;
            $transferUrl = Config::get('app.url').'/administration/module/storage/transfer/detail/'.$this->transferId;
            $transfer = StockTransfer::find($this->transferId);
            $user_gave = Users::find($transfer->uid_gave)->short_user_name;
            $user_accepted = Users::find($transfer->uid_accepted)->short_user_name;
            $stock_in = Stock::find($transfer->stock_in)->name;
            $stock_out = Stock::find($transfer->stock_out)->name;
            return TelegramMessage::create()
                ->to($token) 
                ->content("Запрошено подтверждение на перемещение *№{$this->transferData['numb']}.*\nСклад отправить: {$stock_in} ({$user_gave})\nСклад получатель: {$stock_out} ({$user_accepted})\n \nКод подтверждения перемещения: `{$this->confirmationCode}`")
                ->button('Перейти в перемещение',  $transferUrl)
                ->buttonWithCallback('Посмотреть товары', 'view_transfer ' . $this->transferId)
                ->buttonWithCallback('Подтвердить', 'confirm ' . $this->confirmationId);
        } else {
            return TelegramMessage::create();
        }
       
    }   

    /**
     * Получение уведомления на почту
     *
     * @param  mixed  $notifiable
     * @return \Boot\System\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', '')
            ->line('Thank you for using our application!');
    }
    
    /**
     * Получение уведомления в Viber
     *
     * @param  mixed $notifiable
     * @return \Boot\System\Notifications\Messages\ViberMessage
     */
    public function toViber($notifiable)
    {
        $confirmationUrl = Config::get('app.url').'/administration/module/storage/transfer/activeElectronicSign/'.$this->confirmationId;
        $token = $notifiable->token->where('platform', 'viber')->first()->token;
        return ViberMessage::create()->to($token) 
            ->content("Код подтверждения перемещения №{$this->transferData['numb']}\nКод подтерждения: $this->confirmationCode\n$confirmationUrl");
    }

    /**
     * Получение уведомления в массиве
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
