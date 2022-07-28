<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot;
use Illuminate\Foundation\helpers;
use LINE\LINEBot\Event\Parser;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\MessageBuilder;
use App\Http\Controllers\LINEBotTiny;
use App\Http\Service\getUserProf;

class Callback extends Controller
{

    public function callback(Request $request)
    {
        if($request->store_id == '1'){
            $channelAccessToken = '/4Ejv8i8d4NB1+KSUMMXZA7zEGoCpcBQgIbEng9HYYgcOL1xPcgolcwDSXKbOlRxHvUUhmocgJDvFQrqH7IfpMkxYBt1O2NcU0wSU8bPIIDI9Rpe2VQCHa7ngQp57ptBA7oEAkNxdkZTweVAR0RF1QdB04t89/1O/w1cDnyilFU=';
            $channelSecret = '0b0aadd7b81ec25d7d861c28846e4048';
            $storeId=$request->store_id;

        }elseif($request->store_id == '2'){

            $channelAccessToken ='8jCwB7uJHNCjdiXhcUmlpFheWXIAUB2mnScBaSBvcSPp209NiJ9c/dTdKv3EF0+ufDJFR7ZBZ3MhcRe7RfFO6iGo5DugZXsO+Hdw7hht2JfYP/m/HgOvWl5FDkrrAKKZUddiWqaBY5rSytRV7q/POQdB04t89/1O/w1cDnyilFU=';
        $channelSecret ='b1361c02edc2e4fe84b7bef6c3bddf9d';
        $storeId=$request->store_id;

        }elseif($request->store_id == '3'){

            $channelAccessToken ='paq5m/NvcbF96Rk5UUJoQlbLAS7FD5LRYMqzEWM+Ov8q1mb87WZWSd53enK+feHdV+pHfh6o3in2KI/cY4tEWmCJ7VOCuBHBSNkveeGFULhHWdLswizYiafmhee9yRgATphoOA1ZUY5F53R6/S4VigdB04t89/1O/w1cDnyilFU=';
        $channelSecret ='0bedd5514772fb1d4160813f870386d4';
        $storeId=$request->store_id;
        }



        $this->send($channelAccessToken, $channelSecret,$store_id);



    $this->send($channelAccessToken, $channelSecret);
}
public function send($channelAccessToken, $channelSecret){


   
        $client = new LINEBotTiny($channelAccessToken, $channelSecret,$store_id);
        foreach ($client->parseEvents() as $event) {
    
            //ifで書き直しおｋ！

            //eventtypeがmessageで、messagetypeがtextの時起動

            if ($event['type'] == 'message') {
                $message = $event['message'];

                //ユーザID取得のために、event配列からsoureを代入
                //　$us['userId']　でユーザIDを持ってこれる。
                $us = $event['source'];

                $use=$us['userId'];

//"ID"と入力されたら、ユーザIDを返す

                if ($message['text'] == 'ID') {
                    $client->replyMessage([
            'replyToken' => $event['replyToken'],
            'messages' => [
                [
    'type' => 'text',
    'text' => 'This is '. $store_id .'号店です'
                ],
                [
    'type' => 'text',
    'text' =>  'あなたのユーザID：'.$us['userId']
                ]

            ]
        ]);
        // メニュー　と言われたら、返す
                } elseif ($message['text'] == 'メニュー') {
                    $client->replyMessage([
        'replyToken' => $event['replyToken'],
        'messages' => [
            [
'type' => 'text',
'text' => 'まだメニューないよ！'
            ],
            [
'type' => 'text',
'text' =>  'あなたのユーザID：'.$us['userId']
            ],
            [
'type' => 'text',
'text' =>  '作業中...'
            ]
        ]
    ]);

                }elseif($message['text'] == '限定メニュー'){
                    
                    $client->replyMessage([
                        'replyToken' => $event['replyToken'],
                        'messages' => [
                            [
                'type' => 'text',
                'text' => 'OK!'
                            ],
                            [
                'type' => 'text',
                'text' =>  ''
                            ]
            
                        ]
                    ]);

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

