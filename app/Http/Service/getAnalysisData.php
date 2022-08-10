<?php 

namespace App\Http\Service;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\LINEBotTiny;
class getAnalysisData
{


    
        //LINEBotTiny client
        private $client;
        public function __construct($client)
        {

            $this->client=$client;
        }
    

    public function getData($requestId){

        $res=$this->client->analys($requestId);

        $rs= json_decode($res,true);

        $ov= $rs['overview'];
        if($ov['uniqueImpression'] == null){
          $ov['uniqueImpression'] ='nullぽ';
        }

        $this->client->replyMessage([
          'replyToken' => $event['replyToken'],
          'messages' => [
              [
                  'type' => 'text',
                  'text' => '　OK!'
              ],
              
              [
                  'type' => 'text',
                  'text' => 'メッセージを開封した人数：　'. $ov['uniqueImpression'] .
                  ",\n メッセージの送信数　：　". $ov['delivered'] .
                  ',\\n 　メッセージ内のいずれかのURLをタップした人数：　'. $ov['uniqueClick']
                          
              ]
              
          ]
      ]);


    }

}