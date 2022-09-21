<?php

namespace App\Http\Service;

use LINE\LINEBot;
use App\Http\Controllers\LINEBotTiny;
use Illuminate\Http\Request;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\HTTPClient;
use App\Models\Member;
use App\Models\Store;
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
        //会員に店舗の概念追加
        $mem = Member::where('line_user_id',$uid)->where('attribute',1)->where('store_id',$storeId)->first();
        
        
        if (isset($mem)) {
            $resp=$mem->name . ' 　さんは会員';
            $store = Store::find($storeId)->first();
            $rm = $store->member_menu;
        }else{
            $store = Store::find($storeId)->first();
            $rm = $store->non_member_menu;
            $resp='非会員';
         //   $rm='richmenu-abb034aefaca6179f59627b52a6e0f43';
        }
        $res= $this->client->linkUser($uid,$rm);

        return $resp;
    }

//非会員　richmenu-abb034aefaca6179f59627b52a6e0f43
//会員　richmenu-17e16582cd159c844fa3d85d6f71967a


    function addMember($uid,$event,$storeId){
//今はランクは設定してない
        $mem = Member::where('line_user_id',$uid)->where('store_id',$storeId)->where('attribute',1)->first();
        
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

     function createMember($event,$pt,$storeId){

        $name=  str_replace('name=','',$pt['data']);
        $ev=$event['source'];
        $mem = new Member;

        $mem->line_user_id=$ev['userId'];
        $mem->name = $name;
        $mem->attribute = 1;
        $mem->store_id=$storeId;
        $mem->save();

        $store = Store::find($storeId)->first();
        $rm = $store->member_menu;
        // if($storeId==4){
        //     $rm='richmenu-e31236ca44856f8610743dd3ed50d3a4';        
        // }else if($storeId==14){
        //     $rm='richmenu-c9cb25b501e7efc84acda2ef9e96d183';
        // }
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

     function removeMember($uid,$event,$storeId){
        $mem = Member::where('line_user_id',$uid)->where('attribute',1)->where('store_id',$storeId)->first();

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
     function remove($event,$pt,$storeId){
        $id=str_replace('removeMember&id=','',$pt['data']);
        $mem=Member::where('id',$id)->first();
        $mem->attribute=0;
        $mem->save();

        $store = Store::find($storeId)->first();
        $rm = $store->non_member_menu;
//リッチメニュー変更
// if($storeId==4){
//     $rm='richmenu-1cf3b08b8e1ffec0e5448a4119fa2e6d';
// }else if($storeId==14){
//     $rm= 'richmenu-f4de7ea6cafa216a65e54fe73a66a427';

// }
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

