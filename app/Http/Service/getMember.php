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
        $mem = Member::where('line_user_id',$uid)->first();
        
        if (isset($mem)) {
            $resp='会員'. $storeId;
            $rm='richmenu-17e16582cd159c844fa3d85d6f71967a';
        }else{
            $resp='非会員';
         //   $rm='richmenu-abb034aefaca6179f59627b52a6e0f43';
        $rm= 'richmenu-b63a368440e5008ca8e3293ea6f7c795';


        }
        $res= $this->client->linkUser($uid,$rm);

        return $res. $resp;
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
            $res=json_decode($res);

            $this->client->replyMessage([
                'replyToken' => $event['replyToken'],
                'messages' => [
                    [
        'type' => 'text',
        'text' => '　OK!'
                    ],
        
                    [
        'type' => 'text',
        'text' => ' あなたは'. '非会員です'   
                    ],
                    [
                        'type' => 'text',
                        'text' => ' 登録します'.$res   
                                    ]
                ]
            ]);



         //   $member = new Member;
          //  $member->line_user_id=$uid;

        }

    }
}