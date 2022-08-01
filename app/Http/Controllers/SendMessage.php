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

                    $rmDetail = new getRichMenu($channelAccessToken, $channelSecret,$client);
                    $resDcd = $rmDetail->create();
                    $tex='dummy';
                    $tex= strval($resDcd[0]);

                   

                    //$ss = new getRichMenu($channelAccessToken, $channelSecret);
               
                   
                //    $mId = $ss->createRichMenu();


                    $client->replyMessage([
        'replyToken' => $event['replyToken'],
        'messages' => [
            [
'type' => 'text',
'text' =>$storeId . '　OK!'
            ],

            [
'type' => 'text',
'text' => $resDcd['richMenuId'] . 'is richmenuID'
            ]
        ]
    ]);
                //限定メニューを要求されたとき
                } elseif ($message['text'] == '限定メニュー') {
                    $param =new getOrgMenuParam();
                    $sId =$storeId;
                    $param ->getParam($sId, $client, $event);

                //DB参照
                } elseif ($message['text'] == 'READ') {
                    $us = $event['source'];
                    $use=$us['userId'];

                    $uP= new getUserProf();
                    $uP->getProf($use, $client, $event);
                  
                 
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
