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
           
            $rm='richmenu-abb034aefaca6179f59627b52a6e0f43';
           
            
        }else{
            $resp='非会員';
            $rm='richmenu-17e16582cd159c844fa3d85d6f71967a';
        }
        $res= $this->client->linkUser($uid,$rm);

        return $res. $resp;
    }

//非会員　richmenu-abb034aefaca6179f59627b52a6e0f43
//会員　richmenu-17e16582cd159c844fa3d85d6f71967a
}