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
      
      //  $m=$ms[0];

        $cl=$rs['clicks'];
      //  $c = $cl[0];

        date_default_timezone_set('Asia/Tokyo');
        $ovTime= date('Y/m/d H:i:s', $ov['timestamp']);


        $this->client->replyMessage([
          'replyToken' => $this->event['replyToken'],
          'messages' => [
              [
                  'type' => 'text',
                  'text' => "　OK!\n主に以下のような値が取得できます\n ！！イベントを発生させた実人数が20人未満だった場合は取得できる値が限られます！！"
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
                  ",\n 　メッセージ内のいずれかの動画または音声の再生を開始した人数：　". $ov['uniqueMediaPlayed'] .
                  ",\n 　メッセージ内のいずれかの動画または音声を最後まで視聴した人数：　". $ov['uniqueMediaPlayed100Percent'] 
                          
              ],
              [

                'type' => 'text',
                'text' => '吹き出し単位ごとの統計' .       
                         ",\n 　吹き出しが表示された回数：　"   . 
                 ",\n 　吹き出し内の動画または音声を再生開始し、75%再生した人数：　".// $m['uniqueMediaPlayed75Percent'] .
                 ",\n 　メッセージ内のいずれかのURLをタップした人数：　".// $m['mediaPlayed50Percent'] .
                 ",\n 　吹き出し内の動画または音声が再生開始された回数：　"// . $m['mediaPlayed'] 

             ],
             [
               'type' => 'text',
               'text' => 'タップしたURLに関する情報' .
                 ",\n 　記載された　URL：　".// $m['impression'] .
                 ",\n 　吹き出し内のURLをタップした回数：　". // $m['uniqueMediaPlayed75Percent'] .
                 ",\n 　吹き出し内のURLをタップした人数：　".// $m['mediaPlayed50Percent'] .
                 ",\n 　吹き出し内の動画または音声が再生開始された回数：　"// . $m['mediaPlayed'] 



              ],
              [
                'type' => 'text',
                'text' => "その他の取得可能な値は、以下のURLから確認出来ます\nhttps://developers.line.biz/ja/reference/messaging-api/#get-insight-message-event-response" 
              ]
              
          ]
      ]);

      }
      
 

    }

}