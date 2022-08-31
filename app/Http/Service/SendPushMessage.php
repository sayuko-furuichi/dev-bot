<?php

namespace App\Http\Service;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\LINEBotTiny;
use App\Models\SentMessage;
use Illuminate\Support\Facades\DB;

class SendPushMessage{
    //chanell_access_token
    private $channelAccessToken;
    //chanell_secret
    private $channelSecret;

    //LINEBotTiny client
    private $client;

    //sent message from web 
    private $webMsg;

    //送信先のユーザID
    private $uid;


    public function __construct(String $channelAccessToken, String $channelSecret, $client,$webMsg,$uid)
    {
        // $this->userId= $userId;
        $this->channelAccessToken= $channelAccessToken;
        $this->channelSecret= $channelSecret;
        $this->client=$client;
        $this->webMsg =$webMsg;
        $this-> uid=$uid;
    }

    //とりあえずブロードキャストで送信
    //送信後、ヘッダーからrequestIDを貰う
    public function sendPushMessage()
    {
       

        //$resに、requestidが入る
        $res = $this->client->sendPush([
            'to' => $this->uid,

        'messages' => [
                [
    'type' => 'text',
    'text' =>$this->webMsg
                ],
    //             [
    // 'type' => 'text',
    // 'text' =>'OK!!'
    //             ]

            ]
              ]);


              //TODO:DBにINSERTするのも実装するかな
            //   $msg = new SentMessage;
            //   $msg->request_id=$res;
            //   $msg->save();
            
              return $res;



    }
}
