<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Service\sendNarrow;
use App\Http\Service\getAnalysisData;
use  App\Http\Service\SendPushMessage;
use  App\Http\Service\getAudience;
use App\Http\Service\getCommonsRm;

use App\Http\Service\Messages;
use App\Http\Service\SendFirstMessage;



//あとで消す
use App\Models\RichMenu;
use App\Models\Store;


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

    function __construct($channelAccessToken, $channelSecret, $storeId){
        $this->channelAccessToken=$channelAccessToken;
         $this->channelSecret=$channelSecret;
          $this->storeId=$storeId;
    }

    public function send()
    {
        $client = new LINEBotTiny($this->channelAccessToken, $this->channelSecret);
        foreach ($client->parseEvents() as $event) {
            $us = $event['source'];


            if ($event['type'] == 'postback') {
                $pt=$event['postback'];

                //会員登録するユーザ
                if (preg_match('/name=/', $pt['data'])) {
                    $member = new getMember($this->channelAccessToken, $this->channelSecret, $client);
                    $member->createMember($event, $pt, $this->storeId);

                //退会するユーザ
                } elseif (preg_match('/removeMember&id=/', $pt['data'])) {
                    $member = new getMember($this->channelAccessToken, $this->channelSecret, $client);
                    $member->remove($event, $pt, $this->storeId);

                } elseif (preg_match('/changed=/', $pt['data'])) {
                    $mm = new getMember($this->channelAccessToken, $this->channelSecret, $client);
                    $uid=$us['userId'];
                    $res=$mm->changeMenu($uid, $this->storeId);
                    if ($res !=null || $res !='') {
                        $client->replyMessage([
                            'replyToken' => $event['replyToken'],
                            'messages' => [
                                [
        'type' => 'text',
        'text' => "会員登録後にご利用頂けます"
                                ],
                            ]
                        ]);
                    }

                //経路の入力を受け付ける
                } elseif (preg_match('/transition=/', $pt['data'])) {
                    $tra = new getTransition($this->channelAccessToken, $this->channelSecret, $client);

                    $tra->insertData($us['userId'], $pt['data'], $event);
                }
            }
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
                } elseif ($message['text'] == '完了' && $this->storeId==54 && $us['type']=='web') {
                    // $us['useId'];
                    $member = new getMember($this->channelAccessToken, $this->channelSecret, $client);
                    $member->createCMember($us['userId'], $this->storeId);
                    $store= Store::where('id', 54)->first();
                    $client->linkUser($message['text2'], $store->member_menu);
                    $msg = new SendPushMessage($this->channelAccessToken, $this->channelSecret, $client, '登録', 'ありがとうございます！', $message['text2']);
                    $msg->sendPushMessage();
                } elseif ($message['text'] == '予約確認') {
                    $store = Store::where('id', $this->storeId)->first();
                    $msg = new Messages($this->channelAccessToken, $this->channelSecret, $client, $event['replyToken']);
                    $msg->reserveConf($store);

                 
                } elseif ($message['text'] == 'オフラインを購入') {
                    if ($this->storeId ==1) {
                        $msg = new Messages($this->channelAccessToken, $this->channelSecret, $client, $event['replyToken']);
                        $msg->sendMessage();
                    }



                // メニュー　と言われたら、返す　OK！
                } elseif ($message['text'] == 'create Rich Menu') {
                    //__construct　は、newした時に実行されるので、これが正解？

                    if ($this->storeId ==1) {
                        $rmDetail = new getCommonsRm($this->channelAccessToken, $this->channelSecret, $client);
                        $res = $rmDetail->creater($this->storeId);
                    } else {
                        $rmDetail = new getRichMenu($this->channelAccessToken, $this->channelSecret, $client);
                        $res = $rmDetail->creater($this->storeId);
                    }


                    $client->replyMessage([
'replyToken' => $event['replyToken'],
'messages' => [
[
'type' => 'text',
'text' =>$this->storeId . '　OK!'
],

[
'type' => 'text',
'text' =>  ' is richmenuID'   . $res
]
]
]);

                } elseif ($message['text'] == '会員ステータス確認') {
                    //TODO:確認
                    $mm = new getMember($this->channelAccessToken, $this->channelSecret, $client);
                    $uid=$us['userId'];
                    $res=$mm->index($uid, $this->storeId);

                    $client->replyMessage([
                        'replyToken' => $event['replyToken'],
                        'messages' => [
                            [
'type' => 'text',
'text' => '　OK!'
                            ],

                            [
'type' => 'text',
'text' =>  $res . '　です！'
                            ]
                        ]
                    ]);
                } elseif ($message['text'] == '会員登録する') {
                    $mm = new getMember($this->channelAccessToken, $this->channelSecret, $client);
                    $uid=$us['userId'];
                    $mm->addMember($uid, $event, $this->storeId);
                } elseif ($message['text'] == '退会する') {
                    $mm = new getMember($this->channelAccessToken, $this->channelSecret, $client);
                    $uid=$us['userId'];
                    $mm->removeMember($uid, $event, $this->storeId);


                //TODO:クーポンの配信など調査
                } elseif ($us['type']=='web' || $message['text']=='push!') {
                    $webMsg= $message['text'];
                    if (isset($message['text2'])) {
                        $webMsg2= $message['text2'];
                    } else {
                        $webMsg2='プッシュメッセージ';
                    }

                    $uid=$us['userId'];
                    $msg = new SendPushMessage($this->channelAccessToken, $this->channelSecret, $client, $webMsg, $webMsg2, $uid);
                    $msg->sendPushMessage();
                } elseif ($message['text'] == 'audience') {
                    $us = new getAudience($this->channelAccessToken, $this->channelSecret, $client);
                    $res= $us->createAud($this->storeId);

                    $client->replyMessage([
                                           'replyToken' => $event['replyToken'],
                                           'messages' => [
                                               [
                                                   'type' => 'text',
                                                   'text' => "　OK!\n"
                                               ],

                                               [
                                                   'type' => 'text',
                                                   'text' => 'plz create !'. $res
                                               ]

                                           ]
                                       ]);


                //ブロードキャスト送信する。
                //送信したMsgのRequestIdをDBに格納し、IDを返却する
                } elseif ($message['text'] == 'ブロキャス') {
                    $param = new sendNarrow($this->channelAccessToken, $this->channelSecret, $client);
                    $msgId = $param->sendMessage();
                //     $params = new getAnalysisData($client,$event);
                // $params->getData($requestId);

                //    $rs=json_decode($res,true);
                } elseif ($message['text'] == '利用状況') {
                    $resq= $client->getQuota();
                    $resq=json_decode($resq, true);

                    $ress= $client->getSent();
                    $ress=json_decode($ress, true);


                    $client->replyMessage([
                        'replyToken' => $event['replyToken'],
                        'messages' => [
                            [
                                'type' => 'text',
                                'text' => "　当月の送信数：".$ress['totalUsage']
                            ],

                            [
                                'type' => 'text',
                                'text' => '当月の送信上限目安：'.$resq['value']
                            ],
                            [
                                'type' => 'text',
                                'text' => '当月の送信可能数： 約'. $resq['value'] - $ress['totalUsage']
                            ]

                        ]
                    ]);


                } elseif ($message['text'] == '分析') {
                    $param = new getAnalysisData($client, $event);
                    $param->getData();



                //送信したMsgのIDを、ID:　という形で入力してもらい、IDからRequestIdを持ってきて分析に回す
                } elseif (preg_match('/ID:/', $message['text'])) {
                    $rqMsgId = $message['text'];
                    $param = new getAnalysisData($client, $event);
                    $param->getData($rqMsgId);

                //ここから
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
