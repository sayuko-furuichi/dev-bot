<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Service\GetCommonsRm;

use App\Http\Service\Messages;
use App\Http\Controllers\LINEBotTiny;

//あとで消す
use App\Models\RichMenu;
use App\Models\Store;
use App\Models\LineStoreStatus;

class C_CatchEvents extends Controller
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
        $client = new LINEBotTiny($this->channelAccessToken, $this->channelSecret);
        foreach ($client->parseEvents() as $event) {
            $us = $event['source'];


            // if ($event['type'] == 'postback') {

            // }
            //eventtypeがmessageで、messagetypeがtextの時起動

            //友達登録画面
            if ($event['type'] == 'follow') {
                //すでに入力していた場合は受け付けない

                $first = new Messages($this->channelAccessToken, $this->channelSecret, $client,$event['replyToken']);
                $first->sendFirstMessage($us['userId'], $this->storeId);
            }


           elseif ($event['type'] == 'message') {
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
                        

                        //登録完了時にリッチメニューを切り替える
                } elseif ($message['text'] == '完了' && $us['type']=='web') {
                    // $us['useId'];
                    $lineStore= LineStoreStatus::where('store_id', $this->storeId)->first('member_richmenu_id');
                    $richMenu =RichMenu::where('id', $lineStore->member_richmenu_id)->first('richmenu_id');
                    $client->linkUser($message['text2'], $richMenu->richmenu_id);

                    $msg = new Messages($this->channelAccessToken, $this->channelSecret, $client,'');
                   $res= $msg->sendPushMessage($message['text2'],$message='登録が完了しました！');

                    

                } elseif ($message['text'] == 'オフラインを購入') {

                        $msg = new Messages($this->channelAccessToken, $this->channelSecret, $client, $event['replyToken']);
                        $msg->sendMessage();
                    

                // メニュー　と言われたら、返す　OK！
                } elseif ($message['text'] == 'create Rich Menu') {
                    //すでに有効なリッチメニューが存在している場合は、作成しない
                    $rmDetail = new GetCommonsRm($this->channelAccessToken, $this->channelSecret, $client);
                    $old=$rmDetail->is_set($this->storeId);
                   
                    if(isset($old->id)){
                        $result="メッセージありがとうございます\n大変申し訳ありませんが、こちらのアカウントでは個別に返信を行うことができません。";
                    }else{
                        $result = $rmDetail->creater($this->storeId);
                    }
                   
                    $msg = new Messages($this->channelAccessToken, $this->channelSecret, $client, $event['replyToken']);
                    $msg->result($result);


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
