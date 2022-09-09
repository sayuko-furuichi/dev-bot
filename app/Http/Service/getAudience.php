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
        
     //   $targets = array_combine(['id'],$us->line_user_id);

        //全件取得
        $aud=UserProf::all(['line_user_id']);
       
        foreach($aud as $au){
if (!isset($targets)) {
    $targets=[];
}
            $ar = array('id'=>$au->line_user_id);
            $targets =array_merge($targets,$ar);
        }
       // $auds =array('audiences'=>$targets);

        //カラムを指定してやらないともってこれない
        $res= $this->client->crtAud([
             'description'=>'liff_users',
               'audiences'=>[
                    $targets
         ]]);
        
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
            $resp= $this->getdetail($ress['audienceGroupId']);
            $newaud->count=$resp['audienceCount'];
            $newaud->store_id=$storeId;
            $newaud->save();
            $resp='ok!!';
          
        }
        return $resp;


    }

    public function getdetail($gId){
      $res=  $this->client->detAud($gId);
      return $res;
      $res=json_decode($res,true);
     $gp= $res['audienceGroup'];
     
        return $gp;
            
    }
}
