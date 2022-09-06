<?php

namespace App\Http\Service;

use LINE\LINEBot;
use App\Http\Controllers\LINEBotTiny;
use Illuminate\Http\Request;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\HTTPClient;
use App\Models\Member;
use Illuminate\Support\Facades\DB;

class getMember
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

    public function index($uid,$storeId){
        //会員は店舗関係無くしている(あかんか)
        $mem = Member::where('line_user_id',$uid)->where('attribute',1)->first();
        
        if (isset($mem)) {
            $resp=$mem->name . ' 　さんは会員';
            $rm='richmenu-c9cb25b501e7efc84acda2ef9e96d183';
        }else{
            $resp='非会員';
         //   $rm='richmenu-abb034aefaca6179f59627b52a6e0f43';
        $rm= 'richmenu-f4de7ea6cafa216a65e54fe73a66a427';


        }
        $res= $this->client->linkUser($uid,$rm);

        return $resp;
    }

//非会員　richmenu-abb034aefaca6179f59627b52a6e0f43
//会員　richmenu-17e16582cd159c844fa3d85d6f71967a


    function addMember($uid,$event){
//今はランクは設定してない
        $mem = Member::where('line_user_id',$uid)->where('attribute',1)->first();
        
        if (isset($mem)){ 
            $this->client->replyMessage([
                'replyToken' => $event['replyToken'],
                'messages' => [
                    [
        'type' => 'text',
        'text' => '　OK!'
                    ],
        
                    [
        'type' => 'text',
        'text' => ' あなたは'. 'すでに会員です'   
                    ]
                ]
            ]);
       
        }else{

           $res= $this->client->userProf($uid);
            $resp=json_decode($res,true);

            $this->client->replyMessage([
                'replyToken' => $event['replyToken'],
                'messages' =>  [[
                    'type'=> 'template',
                    'altText'=> 'this is a confirm template',
                    'template'=> [
                      'type'=> 'confirm',
                      'text'=> 'name:'. $resp['displayName']."\n以上で登録します",
                      'actions'=> [
                        [
                          'type'=> 'postback',
                          'label'=> 'yes',
                          'data'=> 'name='.$resp['displayName'],
                          'displayText'=>'会員登録する'
                        ],
                        [
                          'type'=> 'postback',
                          'label'=> 'No',
                          'data'=> 'no',
                           'displayText'=>'しない'
                        ]
                      ]
                      ]]]]);
                            


         //   $member = new Member;
          //  $member->line_user_id=$uid;

        }

    }

     function createMember($event,$pt){

        $name=  str_replace('name=','',$pt['data']);
        $ev=$event['source'];
        $mem = new Member;

        $mem->line_user_id=$ev['userId'];
        $mem->name = $name;
        $mem->attribute = 1;
        $mem->save();

        $rm='richmenu-c9cb25b501e7efc84acda2ef9e96d183';
        $this->client->linkUser($mem->line_user_id,$rm);

        $this->client->replyMessage([
            'replyToken' => $event['replyToken'],
            'messages' => [
                [
    'type' => 'text',
    'text' => '登録されました！' 
                ],
                [
    'type' => 'text',
    'text' =>  'Thanks！'
                ]

            ]
        ]);

     }

     function removeMember($uid,$event){
        $mem = Member::where('line_user_id',$uid)->where('attribute',1)->first();

        if(isset($mem)){
            
            $this->client->replyMessage([
                'replyToken' => $event['replyToken'],
                'messages' =>  [[
                    'type'=> 'template',
                    'altText'=> 'this is a confirm template',
                    'template'=> [
                      'type'=> 'confirm',
                      'text'=> '会員番号:'.$mem->id. "\n".
                      'name:'. $mem->name . "さん\n退会しますか？",
                      'actions'=> [
                        [
                          'type'=> 'postback',
                          'label'=> 'yes',
                          'data'=> 'removeMember&id='.$mem->id,
                          'displayText'=>'退会する'
                        ],
                        [
                          'type'=> 'postback',
                          'label'=> 'No',
                          'data'=> 'no',
                           'displayText'=>'しない'
                        ]
                      ]
                      ]]]]);
    
        }else{
            $this->client->replyMessage([
                'replyToken' => $event['replyToken'],
                'messages' => [
                    [
        'type' => 'text',
        'text' => 'Sorry,あなたは会員ではありません' 
                    ],
                    [
        'type' => 'text',
        'text' =>  '(\'・ω・`)'
                    ]
    
                ]
            ]);

        }


     }
     function remove($event,$pt){
        $id=str_replace('removeMember&id=','',$pt['data']);
        $mem=Member::where('id',$id)->first();
        $mem->attribute=0;
        $mem->save();

//リッチメニュー変更
$rm='richmenu-f4de7ea6cafa216a65e54fe73a66a427';
       $this->client->linkUser($mem->line_user_id,$rm);

        $this->client->replyMessage([
            'replyToken' => $event['replyToken'],
            'messages' => [
                [
    'type' => 'text',
    'text' => '退会が完了しました' 
                ],
                [
    'type' => 'text',
    'text' =>  'thanks'
                ]

            ]
        ]);
     }


}

