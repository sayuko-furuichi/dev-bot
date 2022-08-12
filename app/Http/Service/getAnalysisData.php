<?php 

namespace App\Http\Service;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\LINEBotTiny;
use App\Models\SentMessage;
use Illuminate\Support\Facades\DB;
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
    

    public function getData($rqMsgId){

      //messageから、ID:を切り落とす
      $rqMsgId=  str_replace('ID:','',$rqMsgId);

      //Idで検索する
       $requestId = SentMessage::where('id',$rqMsgId)->first();

       //HITしない場合
       if($requestId==null || !isset($requestId)){
        $this->client->replyMessage([
          'replyToken' => $this->event['replyToken'],
          'messages' => [
              [
                  'type' => 'text',
                  'text' => 'sorry'
              ],
              [
                  'type' => 'text',
                  'text' => 'NotFound ID:'. $rqMsgId
              ]
          ]
              ]);

              
      }else{
        //HITする場合分析に回す
        $res=$this->client->analys($requestId->request_id);

        $rs= json_decode($res,true);

        $ov= $rs['overview'];
        $ms=$rs['messages'];
      //  $m=$ms['seq'];
      //  $cl=$rs['clicks'];

        date_default_timezone_set('Asia/Tokyo');
        $ovTime= date('Y/m/d H:i:s', $ov['timestamp']);



        $this->client->replyMessage([
          'replyToken' => $this->event['replyToken'],
          'messages' => [
              [
                  'type' => 'text',
                  'text' => "　OK!\n "
                //   'emojis' =>[[
                //     'index' => 0,
                //     'productId' => '5ac21b4f031a6752fb806d59',
                //     'emojiId' =>'114',
                //     ]

                //   ]
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
                'text' => '吹き出し単位ごとの統計' .       
                         ",\n 　吹き出しが表示された回数：　". $ms['seq'] // .
                // ",\n 　吹き出し内の動画または音声を再生開始し、75%再生した人数：　". $m['uniqueMediaPlayed75Percent'] .
                // ",\n 　メッセージ内のいずれかのURLをタップした人数：　". $m['mediaPlayed50Percent'] .
                // ",\n 　吹き出し内の動画または音声が再生開始された回数：　". $m['mediaPlayed'] 

             ],
             [
               'type' => 'text',
               'text' => 'タップしたURLに関する情報'// .
                // ",\n 　吹き出しが表示された回数：　". $m['impression'] .
                // ",\n 　吹き出し内の動画または音声を再生開始し、75%再生した人数：　". $m['uniqueMediaPlayed75Percent'] .
                // ",\n 　メッセージ内のいずれかのURLをタップした人数：　". $m['mediaPlayed50Percent'] .
                // ",\n 　吹き出し内の動画または音声が再生開始された回数：　". $m['mediaPlayed'] 



              ]
              
          ]
      ]);

      }
      
 

    }

}