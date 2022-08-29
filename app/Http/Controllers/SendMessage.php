<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\LINEBotTiny;

use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot;
use Illuminate\Support\Facades\Storage;

use App\Http\Service\getOrgMenuParam;
use App\Http\Service\getRichMenu;
use App\Http\Service\sendNarrow;
use App\Http\Service\getAnalysisData;

class SendMessage extends Controller
{
    //postbackアクションについて
    //ユーザが、メッセージを送信せずにデータのみ送信できる機能。
    //botは、if($pt['data']＝action=***)　などで判定したらよい。

    public function send($channelAccessToken, $channelSecret, $storeId)
    {
        $client = new LINEBotTiny($channelAccessToken, $channelSecret);
        foreach ($client->parseEvents() as $event) {

            if ($event['type'] == 'postback'){
                $pt=$event['postback'];
                $ptD = $pt['data'];

                $pra = new getAnalysisData($client,$event);
                   $param->getData($ptD); 


                // if($pt['data'] ==''){


                // }

            }

            //eventtypeがmessageで、messagetypeがtextの時起動

            if ($event['type'] == 'message') {
                $message = $event['message'];
                //"ID"と入力されたら、ユーザIDを返す

                if ($message['text'] == 'ID') {

                    //ユーザID取得のために、event配列からsoureを代入
                    //　$us['userId']　でユーザIDを持ってこれる。
                    $us = $event['source'];

                    $use=$us['userId'];


                    $client->replyMessage([
            'replyToken' => $event['replyToken'],
            'messages' => [
                [
    'type' => 'text',
    'text' => 'This is ' . $storeId . '号店'
                ],
                [
    'type' => 'text',
    'text' =>  'あなたのユーザID：'.$us['userId']
                ]

            ]
        ]);
                // メニュー　と言われたら、返す　OK！
                } elseif ($message['text'] == 'create Rich Menu') {
                    //__construct　は、newした時に実行されるので、これが正解？

                    $rmDetail = new getRichMenu($channelAccessToken, $channelSecret, $client);
                    $res = $rmDetail->creater();

                    $imres=json_decode($res, true);

                    if ($res==false || $res== null ||$res== 'undefine' || isset($res['message'])) {
                        $flag='false';
                     
                    } elseif(!isset($imres['message']))  {
                        $flag='true';
                      //  $imres['message']='true';
                    
                    }

                    //$ss = new getRichMenu($channelAccessToken, $channelSecret);


                    //    $mId = $ss->createRichMenu();

                    //      $imres['richMenuId']

                    $client->replyMessage([
        'replyToken' => $event['replyToken'],
        'messages' => [
            [
'type' => 'text',
'text' =>$storeId . '　OK!'
            ],

            [
'type' => 'text',
'text' => $flag . ' is richmenuID'   . $res . $imres['message']
            ]
        ]
    ]);
                //限定メニューを要求されたとき
                } elseif ($message['text'] == '限定メニュー') {
                    $param =new getOrgMenuParam();
                    $sId =$storeId;
                    $param ->getParam($sId, $client, $event);


                    //ブロードキャスト送信する。
                    //送信したMsgのRequestIdをDBに格納し、IDを返却する
                } elseif ($message['text'] == 'ブロキャス') {
                    $param = new sendNarrow($channelAccessToken, $channelSecret, $client);
                    $msgId = $param->sendMessage();
                //     $params = new getAnalysisData($client,$event);
                // $params->getData($requestId);

                //    $rs=json_decode($res,true);


    $client->replyMessage([
        'replyToken' => $event['replyToken'],
        'messages' => [
            [
                'type' => 'text',
                'text' => "　OK!\nThis　Message ID　is"
            ],
            
            [
                'type' => 'text',
                'text' => 'ID:' . $msgId->id
            ]
            
        ]
    ]);

              
                  //  $param =new getOrgMenuParam();
                   // $sId =$storeId;
                   // $param ->getParam($sId, $client, $event);


                } elseif ($message['text'] == '分析') {
                    $param = new getAnalysisData($client,$event);
                   $param->getData(); 

                //DB参照
                } elseif ($message['text'] == 'READ') {
                    $us = $event['source'];
                    $use=$us['userId'];

                    $uP= new getUserProf();
                    $uP->getProf($use, $client, $event);

                //richメニュー画像
                } elseif ($message['text'] == '画像') {
                    $param =new getOrgMenuParam();
                    $sId =$storeId;
                    $param ->getParam($sId, $client, $event);


                    //送信したMsgのIDを、ID:　という形で入力してもらい、IDからRequestIdを持ってきて分析に回す
                } elseif (preg_match('/ID:/',$message['text'])) {
                    $rqMsgId = $message['text'];
                    $param = new getAnalysisData($client,$event);
                   $param->getData($rqMsgId);
                    

                    // $client->replyMessage([
                    //     'replyToken' => $event['replyToken'],
                    //     'messages' => [
                    //         [
                    //             'type' => 'text',
                    //             'text' => 'true'
                    //         ],
                    //         [
                    //             'type' => 'text',
                    //             'text' => $qr
                    //         ]
                    //     ]
                    //         ]);


                //ここから
                } elseif ($message['type']=='text') {
                    $client->replyMessage([
                                    'replyToken' => $event['replyToken'],
                                    'messages' => [
                                        [
                                            'type' => 'text',
                                            'text' => $message['text']
                                        ],
                                        [
                                            'type' => 'text',
                                            'text' => 'まねしないで！'
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
