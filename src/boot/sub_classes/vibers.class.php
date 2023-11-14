<?php
class vibers
{
	 
    private $url_api = "https://chatapi.viber.com/pa/";

    private $token = "491c0a3663e7d14d-a7c84835f8b1809f-21bfe84ad2fa640a";

    public function message_post
    (
        $from,          // ID администратора Public Account.
        array $sender,  // Данные отправителя.
        $text           // Текст.
    )
    {
        $data['receiver']   = $from;
        $data['sender'] = $sender;
        $data['type']   = 'text';
        $data['text']   = $text;
        return $this->call_api('send_message', $data);
    }
	
	public function message_post_plus
    (
        $from,          // ID администратора Public Account.
        array $sender,  // Данные отправителя.
        $text           // Текст.
    )
    {
        $data['receiver']   = $from;
        $data['sender'] = $sender;
        $data['type']   = 'rich_media';
        $data['rich_media']   = array(
			'Type' => 'rich_media',
			'min_api_version' => '7',
			'ButtonsGroupColumns' => '1',
			'ButtonsGroupRows' => '1',			
			'BgColor' => '#FFFFFF',
			'Buttons' => array(
				'Columns' => '1',
				'Rows' => '1',
				'ActionType' => 'reply',
				'ActionBody' => '',
				'Text' => 'bay',
				'TextSize' => 'large',
				'TextVAlign' => 'middle',
				'TextHAlign' => 'middle',
				'Image' => '',
				
			),
		);
		
		
        return $this->call_api('send_message', $data);
    }

    private function call_api($method, $data)
    {
        $url = $this->url_api.$method;

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\nX-Viber-Auth-Token: ".$this->token."\r\n",
                'method'  => 'POST',
                'content' => json_encode($data, JSON_UNESCAPED_UNICODE)
            )
        );
        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
		//print_r($response);
        return json_decode($response);
    }
}