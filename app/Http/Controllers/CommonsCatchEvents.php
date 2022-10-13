<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use  App\Http\Service\SendPushMessage;
use App\Http\Service\getCommonsRm;

use App\Http\Service\Messages;
use App\Http\Service\SendFirstMessage;
use App\Http\Controllers\C_LINEBotTiny;



//あとで消す
use App\Models\RichMenu;
use App\Models\Store;
use App\Models\LineStoreStatus;

class CommonsCatchEvents extends Controller
{
    private $channelAccessToken;
    //chanell_secret
    private $channelSecret;

    //LINEBotTiny client
    private $storeId;
    //postbackアクションについて
    //ユーザが、メッセージを送信せずにデータのみ送信できる機能。
    //botは、if($pt['data']＝action=***)　などで判定したらよい。

    public function __construct($channelAccessToken, $channelSecret, $storeId)
    {
        $this->channelAccessToken=$channelAccessToken;
        $this->channelSecret=$channelSecret;
        $this->storeId=$storeId;
    }

    public function send()
    {
        $client = new C_LINEBotTiny($this->channelAccessToken, $this->channelSecret);
        foreach ($client->parseEvents() as $event) {
            $us = $event['source'];


            // if ($event['type'] == 'postback') {

            // }
            //eventtypeがmessageで、messagetypeがtextの時起動

            //友達登録画面
            if ($event['type'] == 'follow') {
                //すでに入力していた場合は受け付けない

                $first = new SendFirstMessage($this->channelAccessToken, $this->channelSecret, $client);
                $first->send($event['replyToken'], $us['userId'], $this->storeId);
            }
            //ブロック時
            if ($event['type'] == 'unfollow') {
                //TODO:ブロック時の記録
            }

            if ($event['type'] == 'message') {
                $message = $event['message'];

                if ($message['text'] == '申し込み' && $this->storeId==1) {
                    $client->replyMessage([
                            'replyToken' => $event['replyToken'],
                            'messages' => [
                                [
                    'type' => 'text',
                    'text' => 'こちらからどうぞ'
                                ],
                                [
                    'type' => 'text',
                    'text' =>  'https://dev-ext-app.herokuapp.com/public/addMember?user='.$us['userId']
                                ]

                            ]
                        ]);
                } elseif ($message['text'] == '完了' && $us['type']=='web') {
                    // $us['useId'];
                    $lineStore= LineStoreStatus::where('store_id', $this->storeId)->first('member_richmenu_id');
                    $richMenu =RichMenu::where('id', $lineStore->member_richmenu_id)->first('richmenu_id');
                    $client->linkUser($message['text2'], $richMenu->richmenu_id);
                    $msg = new SendPushMessage($this->channelAccessToken, $this->channelSecret, $client, '登録', 'ありがとうございます！', $message['text2']);
                    $msg->sendPushMessage();
                } elseif ($message['text'] == 'オフラインを購入') {
                    if ($this->storeId ==1) {
                        $msg = new Messages($this->channelAccessToken, $this->channelSecret, $client, $event['replyToken']);
                        $msg->sendMessage();
                    }



                // メニュー　と言われたら、返す　OK！
                } elseif ($message['text'] == 'create Rich Menu') {
                    //__construct　は、newした時に実行されるので、これが正解？

                    $rmDetail = new getCommonsRm($this->channelAccessToken, $this->channelSecret, $client);
                    $res = $rmDetail->creater($this->storeId);
                    
                    $msg = new Messages($this->channelAccessToken, $this->channelSecret, $client, $event['replyToken']);
                    $msg->result($res);




                //どこの条件にも引っかからないメッセージ
                } elseif ($message['type']=='text') {
                    $client->replyMessage([
                                    'replyToken' => $event['replyToken'],
                                    'messages' => [
                                        [
                                            'type' => 'text',
                                            'text' => "メッセージありがとうございます\n大変申し訳ありませんが、こちらのアカウントでは個別に返信を行うことができません。"
                                        ]
                                    ]
                                        ]);
                } else {
                    error_log('Unsupported message type: ' . $message['type']);
                    break;
                }
            } else {
                error_log('Unsupported event type: ' . $event['type']);
                break;
            }
        }
    }
}
