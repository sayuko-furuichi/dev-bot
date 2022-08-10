<?php

namespace App\Http\Service;

use Illuminate\Support\Facades\Storage;

class sendNarrow
{
    //chanell_access_token
    private $channelAccessToken;
    //chanell_secret
    private $channelSecret;

    //LINEBotTiny client
    private $client;


    public function __construct(String $channelAccessToken, String $channelSecret, $client)
    {
        // $this->userId= $userId;
        $this->channelAccessToken= $channelAccessToken;
        $this->channelSecret= $channelSecret;
        $this->client=$client;
    }

    //とりあえずブロードキャストで送信
    //送信後、ヘッダーからrequestIDを貰う
    public function sendMessage()
    {
        return 'あああああ';

        
        $res = $this->client->sendBroad([
            'messages' => [
                [
    'type' => 'text',
    'text' =>$storeId . '　OK!'
                ],

                [
    'type' => 'text',
    'text' => ' OK 3'
                ],
                                    [
    'type' => 'text',
    'text' => 'OK  2'
                ],
                                    [
    'type' => 'text',
    'text' => 'OK  1'
                ],
                                    [
    'type' => 'text',
    'text' => 'fire!!'
                ],

            ]



        ]);


    }
}
