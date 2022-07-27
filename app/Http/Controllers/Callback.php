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
    //
  // private String $userId;


    public function tiny()
    {

    


        //  require_once(dirname(__FILE__)."/LINEBotTiny.php");

        $channelAccessToken = '/4Ejv8i8d4NB1+KSUMMXZA7zEGoCpcBQgIbEng9HYYgcOL1xPcgolcwDSXKbOlRxHvUUhmocgJDvFQrqH7IfpMkxYBt1O2NcU0wSU8bPIIDI9Rpe2VQCHa7ngQp57ptBA7oEAkNxdkZTweVAR0RF1QdB04t89/1O/w1cDnyilFU=';
        $channelSecret = '0b0aadd7b81ec25d7d861c28846e4048';

        $client = new LINEBotTiny($channelAccessToken, $channelSecret);
        foreach ($client->parseEvents() as $event) {
    


            //ifで書き直しおｋ！

            //eventtypeがmessageで、messagetypeがtextの時起動


            if ($event['type'] == 'message') {
                $message = $event['message'];

                //ユーザID取得のために、event配列からsoureを代入
                //　$us['userId']　でユーザIDを持ってこれる。
               $us = $event['source'];

               $use=$us['userId'];
              $results =getUserProf($use);
              $id= $results->id;
              $userId= $results->line_user_id;
              $usernm= $data->line_user_name;
                 

                if ($message['text'] == 'おはよう') {
                    $client->replyMessage([
            'replyToken' => $event['replyToken'],
            'messages' => [
                [
                    'type' => 'text',
                    'text' => 'いい朝ですね'
                ],
                [
                    'type' => 'text',
                    'text' =>  'あなたのユーザID：'.$us['userId']
                ],
                [
                    'type' => 'text',
                    'text' =>  $id.$userId.$usenm
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
