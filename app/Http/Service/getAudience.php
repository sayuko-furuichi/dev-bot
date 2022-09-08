<?php

namespace App\Http\Service;

use LINE\LINEBot;
use App\Http\Controllers\LINEBotTiny;
use Illuminate\Http\Request;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\HTTPClient;
use App\Models\UserProf;
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

    public function createAud($storeId){

        $us = UserProf::where('id',4)->first(['line_user_id']);
        $aud=UserProf::all(['line_user_id']);
       $res= $this->client->crtAud([
            'description'=>'liff_user',
            'audiences'=> $aud
        ]);

         if($res!='request failed'){
             $res='ok!';
          $newaud = new Audience;
          $newaud ->group_id=$res->audienceGroupId;
          $newaud ->createRoute=$res->createRoute;
          $newaud ->description=$res->description;
          $newaud->store_id=$storeId;
          $newaud->save();
         
         }
        return $res;
        //

    }



}



