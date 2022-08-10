<?php 

namespace App\Http\Service;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\LINEBotTiny;
class getAnalysisData
{


    
        //LINEBotTiny client
        private $client;

private $event;

        public function __construct($client,$event)
        {

            $this->client=$client;
            $this->event=$event;
        }
    

    public function getData(){

        $requestId='b736e0df-b47d-4b14-9e53-81986609d3a8';
        $res=$this->client->analys($requestId);

        $rs= json_decode($res,true);

        $ov= $rs['overview'];
        if($ov['uniqueImpression'] == null){
          $ov['uniqueImpression'] ='nullぽ';
        }
        date_default_timezone_set('Asia/Tokyo');
        $ovTime= date('Y/m/d H:i:s', $ov['timestamp']);

        $this->client->replyMessage([
          'replyToken' => $this->event['replyToken'],
          'messages' => [
              [
                  'type' => 'text',
                  'text' => "$"."　OK!\n ",
                  'emoji' =>[
                    'index' => 0,
                    'productId' => '5ac21b4f031a6752fb806d59',
                    'emojiId' =>'114',


                  ]
              ],
              
              [
                  'type' => 'text',
                  'text' => 'メッセージを開封した人数：　'. $ov['uniqueImpression'] .
                  ",\n メッセージの送信数　：　". $ov['delivered'] .
                  ",\n 　メッセージ内のいずれかのURLをタップした人数：　". $ov['uniqueClick'] .
                  ",\n 　メッセージが配信された時刻 ：　".  $ovTime .
                  ",\n 　メッセージ内のいずれかのURLをタップした人数：　". $ov['uniqueClick'] .
                  ",\n 　メッセージ内のいずれかのURLをタップした人数：　". $ov['uniqueClick'] 
                          
              ],
              [

                'type' => 'text',
                'text' => 'メッセージを開封した人数：　'. $ov['uniqueImpression'] .
                ",\n メッセージの送信数　：　". $ov['delivered'] .
                ",\n 　メッセージ内のいずれかのURLをタップした人数：　". $ov['uniqueClick'] 

              ]
              
          ]
      ]);


    }

}