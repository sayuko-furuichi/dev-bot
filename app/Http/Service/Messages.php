<?php

namespace App\Http\Service;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\LINEBotTiny;
use App\Models\SentMessage;
use Illuminate\Support\Facades\DB;

class Messages
{
    //chanell_access_token
    private $channelAccessToken;
    //chanell_secret
    private $channelSecret;

    //LINEBotTiny client
    private $client;

    private $replyToken;


    public function __construct(String $channelAccessToken, String $channelSecret, $client,$replyToken)
    {
        // $this->userId= $userId;
        $this->channelAccessToken= $channelAccessToken;
        $this->channelSecret= $channelSecret;
        $this->client=$client;
        $this->replyToken=$replyToken;
    }

    //とりあえずブロードキャストで送信
    //送信後、ヘッダーからrequestIDを貰う
    public function sendMessage()
    {
        $this->client->replyMessage(
            [
'replyToken' => $this->replyToken,
'messages' => [
[
'type'=> 'template',
'altText'=> '分岐テンプレート',
'template'=> [
'type'=> 'confirm',
'text'=> 'どちらの催しに参加しますか？',
'actions'=> [
        [
          'type'=> 'message',
          'label'=> 'イベント',
          'text'=>'イベントページへ遷移',
        ],
        [
          'type'=> 'message',
          'label'=> '勉強会',
          'text'=> '勉強会ページへ遷移',
           
        ]
        ]]]]]
        );

    }
}
