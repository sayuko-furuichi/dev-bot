<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Service\getEnisRm;
use App\Http\Service\getAnalysisData;
use  App\Http\Service\getMember;
use  App\Http\Service\getAudience;
use App\Http\Service\Messages;



//あとで消す
use App\Models\RichMenu;
use App\Models\Store;
use App\Models\LineStoreStatus;

class CatchEvents extends Controller
{
    private $storeId;
    private $lineBot;

    //初期化時に店舗IDとLINE Botを渡す
    public function __construct($storeId, $lineBot)
    {
        $this->storeId = $storeId;
        $this->lineBot =$lineBot;
    }

//eventを受け取る
    public function send()
    {
        foreach ($this->lineBot->parseEvents() as $event) {
            $us = $event['source'];


            if ($event['type'] == 'postback') {
                $pt=$event['postback'];

                //会員登録するユーザ
                // if (preg_match('/name=/', $pt['data'])) {
                //     $member = new getMember($this->lineBot);
                //     $member->createMember($event, $pt, $this->storeId);
                // }
            }
            //eventtypeがmessageで、messagetypeがtextの時起動

            //友達登録画面
            if ($event['type'] == 'follow') {
                //すでに入力していた場合は受け付けない
                $first = new Messages($this->lineBot, $event['replyToken']);
                $first->sendFirstMessage($us['userId'], $this->storeId);
            }
            //ブロック時
            if ($event['type'] == 'unfollow') {
                //TODO:ブロック時の記録
            }

            if ($event['type'] == 'message') {
                $message = $event['message'];
                //"ID"と入力されたら、ユーザIDを返す

                if ($message['text'] == 'ID') {
                    //ユーザID取得のために、event配列からsoureを代入
                    //　$us['userId']　でユーザIDを持ってこれる。

                    $use=$us['userId'];

                    $this->lineBot->replyMessage([
                    'replyToken' => $event['replyToken'],
                    'messages' => [
                    [
    'type' => 'text',
    'text' => 'This is ' . $this->storeId . '号店'
                    ],
                    [
    'type' => 'text',
    'text' =>  'あなたのユーザID：'.$us['userId']
                    ]

            ]
        ]);
                } elseif ($message['text'] == '完了'&& $us['type']=='web') {
                    // $us['useId'];
                    $lineStore= LineStoreStatus::where('store_id', $this->storeId)->first('member_richmenu_id');
                    $richMenu =RichMenu::where('id', $lineStore->member_richmenu_id)->first('richmenu_id');
                    $this->lineBot->linkUser($message['text2'], $richMenu->richmenu_id);

                    $msg = new Messages($this->lineBot, '');
                    $res= $msg->sendPushMessage($message['text2'], $message='登録が完了しました！');

                } elseif ($message['text'] == '予約確認') {
                    $lineStore = LineStoreStatus::where('store_id',$this->storeId)->first('liff_url');
                    $msg = new Messages($this->lineBot, $event['replyToken']);
                    $msg->reserveConf($this->storeId,$lineStore);


                // メニュー　と言われたら、返す　OK！
                } elseif ($message['text'] == 'create Rich Menu') {
                    //__construct　は、newした時に実行されるので、これが正解？

                    $rmDetail = new getEnisRm($lineBot);
                    $old=$rmDetail->is_set($this->storeId);

                    if (isset($old->id)) {
                        $result="メッセージありがとうございます\n大変申し訳ありませんが、こちらのアカウントでは個別に返信を行うことができません。";
                    } else {
                        $result = $rmDetail->creater($this->storeId);
                    }

                    $msg = new Messages($this->lineBot, $event['replyToken']);
                    $msg->result($result);
                } elseif ($message['text'] == 'audience') {
                    $us = new getAudience($this->lineBot);
                    $res= $us->createAud($this->storeId);

                    $this->lineBot->replyMessage([
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
                    $param = new sendNarrow($this->lineBot);
                    $msgId = $param->sendMessage();
                //     $params = new getAnalysisData($this->lineBot,$event);
                // $params->getData($requestId);

                //    $rs=json_decode($res,true);
                } elseif ($message['text'] == '利用状況') {
                    $resq= $this->lineBot->getQuota();
                    $resq=json_decode($resq, true);

                    $ress= $this->lineBot->getSent();
                    $ress=json_decode($ress, true);


                    $this->lineBot->replyMessage([
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


                //  $param =new getOrgMenuParam();
                // $sId =$this->storeId;
                // $param ->getParam($sId, $this->lineBot, $event);
                } elseif ($message['text'] == '分析') {
                    $param = new getAnalysisData($this->lineBot, $event);
                    $param->getData();


                //送信したMsgのIDを、ID:　という形で入力してもらい、IDからRequestIdを持ってきて分析に回す
                } elseif (preg_match('/ID:/', $message['text'])) {
                    $rqMsgId = $message['text'];
                    $param = new getAnalysisData($this->lineBot, $event);
                    $param->getData($rqMsgId);

                //ここから
                } elseif ($message['type']=='text') {
                    $this->lineBot->replyMessage([
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
