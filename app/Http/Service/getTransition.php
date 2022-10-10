<?php

namespace App\Http\Service;

use LINE\LINEBot;
use App\Http\Controllers\LINEBotTiny;
use Illuminate\Http\Request;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\HTTPClient;


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

    }
    
       function search($userId){



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
