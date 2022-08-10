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
    //

    public function send($channelAccessToken, $channelSecret, $storeId)
    {
        $client = new LINEBotTiny($channelAccessToken, $channelSecret);
        foreach ($client->parseEvents() as $event) {

            //ifで書き直しおｋ！

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


                    //ブロードキャスト
                } elseif ($message['text'] == 'ブロキャス') {
                    $param = new sendNarrow($channelAccessToken, $channelSecret, $client);
                    $res = $param->sendMessage();

                //    $rs=json_decode($res,true);


    $client->replyMessage([
        'replyToken' => $event['replyToken'],
        'messages' => [
            [
                'type' => 'text',
                'text' =>$storeId . '　OK!'
            ],
            
            [
                'type' => 'text',
                'text' => 'OK　　：'. $res
            ]
            
        ]
    ]);

              
                  //  $param =new getOrgMenuParam();
                   // $sId =$storeId;
                   // $param ->getParam($sId, $client, $event);


                } elseif ($message['text'] == '分析') {
                    $param = new getAnalysisData($client);
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
