<?php 

namespace App\Http\Service;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\LINEBotTiny;
class getAnalysisData
{

        //chanell_access_token
        private $channelAccessToken;
        //chanell_secret
        private $channelSecret;
    
        //LINEBotTiny client
        private $client;
        public function __construct(String $channelAccessToken, String $channelSecret, $client)
        {
            // $this->userId= $userId;
            $this->channelAccessToken= $channelAccessToken;
            $this->channelSecret= $channelSecret;
            $this->client=$client;
        }
    

    public function getData($requestId){

        $res=$this->client->analys($requestId);

        $rs= json_decode($res,true);

        $ov= $rs['overview'];
        if($ov['uniqueImpression'] == null){
          $ov['uniqueImpression'] ='nullぽ';
        }

        $client->replyMessage([
          'replyToken' => $event['replyToken'],
          'messages' => [
              [
                  'type' => 'text',
                  'text' =>$storeId . '　OK!'
              ],
              
              [
                  'type' => 'text',
                  'text' => 'メッセージを開封した人数：　'. $ov['uniqueImpression'] .
                  ",\\n メッセージの送信数　：　". $ov['delivered'] .
                  ',\\n 　メッセージ内のいずれかのURLをタップした人数：　'. $ov['uniqueClick']
                          
              ]
              
          ]
      ]);


    }

}