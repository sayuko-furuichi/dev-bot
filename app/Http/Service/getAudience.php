<?php

namespace App\Http\Service;

use LINE\LINEBot;
use App\Http\Controllers\LINEBotTiny;
use Illuminate\Http\Request;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\HTTPClient;
use App\Models\UserProf;
use App\Models\Audience;
use Illuminate\Support\Facades\DB;

class getAudience
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

    public function createAud($storeId)
    {
        $us = UserProf::where('id', 4)->first(['line_user_id']);
        $aud=UserProf::all(['line_user_id']);
        $res= $this->client->crtAud([
             'description'=>'liff_users',
        
               'audiences'=>[
                [
                'id'=> $us->line_user_id,
               ],  
               ]
               
         ]);
        return $res;
        if ($res!='request failed') {
            $ress = json_decode($res, true);
            $newaud = new Audience();
            $newaud ->group_id=$ress['audienceGroupId'];
            $newaud ->create_route=$ress['createRoute'];
            $newaud ->description=$ress['description'];
            if (isset($ress['expireTimestamp'])) {
                //UNIXtimeを変換して格納
                $newaud ->expire=date("Y/m/d H:i:s", $ress['expireTimestamp']);
            } else {
                $newaud ->expire='';
            }

            $newaud->store_id=$storeId;
            $newaud->save();
            $resp='ok!!';
        }
        return $resp;
        //
    }
}
