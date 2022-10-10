<?php

namespace App\Http\Service;

use LINE\LINEBot;
use App\Http\Controllers\LINEBotTiny;
use Illuminate\Http\Request;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\HTTPClient;
use  App\Http\Service\getMember;


use App\Models\Transition;

class getTransition
{
    //chanell_access_token
    private $channelAccessToken;
    //chanell_secret
    private $channelSecret;

    //LINEBotTiny client
    private $client;

    /**
     * Undocumented __construct
     *
     * @param String $channelAccessToken
     * @param String $channelSecret
     * @param LINEBotTiny $client
     */
    public function __construct(String $channelAccessToken, String $channelSecret, $client)
    {
        // $this->userId= $userId;
        $this->channelAccessToken= $channelAccessToken;
        $this->channelSecret= $channelSecret;
        $this->client=$client;
    }

     function insertData($userId,$data,$event){
        $oldTrans=$this->search($userId);
        $re= json_encode($oldTrans,true);

        if(empty($oldTrans)){
 
            $trans =new Transition;
            $trans->lineuser_id=$userId;
            $trans->transition=str_replace('transition=','',$data);
            $trans->save();
    
            $this->client->replyMessage([
                'replyToken' => $event['replyToken'],
                'messages' => [
                    [
        'type' => 'text',
        'text' => "ありがとうございました！"
                    ],
                ]
            ]);
    
        }else{
            $this->client->replyMessage([
                'replyToken' => $event['replyToken'],
                'messages' => [
                    [
        'type' => 'text',
        'text' => '入力済みです'.$re
                    ],
                ]
            ]);
        }
        }
      
    
       function search($userId){
        $trans = Transition::where('lineuser_id',$userId);
        return $trans;

       }
        
     function sendTemplate($event,$userId,$storeId){

        $member = new getMember($this->channelAccessToken, $this->channelSecret, $this->client);
        $res=$member->index($userId,$storeId);

        // $imgUrl = secure_asset('img/Commands_logo.png');
        $this->client->replyMessage(
            [
'replyToken' => $event['replyToken'],
'messages' => [
[
'type' => 'text',
'text' => "友達登録ありがとうございます！\n".$res."メニューをご利用いただけます"
],
[
'type'=> 'template',
'altText'=> 'きっかけテンプレート',
'template'=> [
'type'=> 'buttons',
'text'=> '当アカウントを知ったきっかけを教えてください',
//   'thumbnailImageUrl'=> $imgUrl,
'actions'=> [
                    [
                      'type'=> 'postback',
                      'label'=> 'LP',
                      'data'=> 'transition=lp',
                      'displayText'=>'LP'
                    ],
                    [
                      'type'=> 'postback',
                      'label'=> 'チラシ',
                      'data'=> 'transition=paper',
                       'displayText'=>'チラシ'
                    ],
                    [
            'type'=> 'postback',
            'label'=> 'セミナー',
            'data'=> 'transition=paper',
             'displayText'=>'セミナー'
                      ],
                      [
            'type'=> 'postback',
            'label'=> '知人からの紹介',
            'data'=> 'transition=introduction',
             'displayText'=>'知人からの紹介'
                      ]
                    ]]],  [
                        'type'=> 'template',
                        'altText'=> 'きっかけテンプレート',
                        'template'=> [
                          'type'=> 'buttons',
                          'text'=> '　　　　　',
                          'actions'=> [
                                    [
                                      'type'=> 'postback',
                                      'label'=> '検索サイト',
                                      'data'=> 'transition=search',
                                      'displayText'=>'検索サイト'
                                    ],
                                    [
                                      'type'=> 'postback',
                                      'label'=> '公式ホームページ',
                                      'data'=> 'transition=HP',
                                       'displayText'=>'公式ホームページ'
                                    ],
                                    [
                            'type'=> 'postback',
                            'label'=> '本や雑誌・メディア',
                            'data'=> 'transition=media',
                             'displayText'=>'本や雑誌・メディア'
                                      ],
                                      [
                            'type'=> 'postback',
                            'label'=> 'その他',
                            'data'=> 'transition=other',
                             'displayText'=>'その他'
                                      ]
                                    ]]],
                    ]]
        );
       }
    

    public function getdetail($gId)
    {
        $res=  $this->client->detAud($gId);
        return $res;
        $res=json_decode($res, true);
        $gp= $res['audienceGroup'];

        return $gp;
    }

    



}
