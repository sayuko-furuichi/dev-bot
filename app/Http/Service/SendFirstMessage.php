<?php

namespace App\Http\Service;

use Illuminate\Http\Request;

use App\Models\LineStoreStatus;
use App\Models\Client;
use App\Models\RichMenu;

class SendFirstMessage
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


       public function search($userId,$storeId)
       {
           $is_client = Client::where('line_user_id', $userId)->where('store_id',$storeId)->first();
           return $is_client;
       }

     public function send($replyToken, $userId, $storeId)
     {
         $is_client = $this->search($userId,$storeId);
         if (!isset($is_client->id)) {
             $flag='非会員';
             $this->client->deleteLinkUser($userId);

         } else {
             $flag='会員';
             $storerm=LineStoreStatus::where('store_id',$storeId)->first('member_richmenu_id');
             $rm=RichMenu::where('id',$storerm->member_richmenu_id)->first('richmenu_id');
             $this->client->linkUser($userId,$rm->richmenu_id);
         }

         // $imgUrl = secure_asset('img/Commands_logo.png');
         $this->client->replyMessage(
             [
'replyToken' => $replyToken,
'messages' => [
[
'type' => 'text',
'text' => "友達登録ありがとうございます！\nあなたは".$flag."メニューをご利用いただけます"
],

                     ]]
         );
     }

}
