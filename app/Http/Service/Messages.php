<?php

namespace App\Http\Service;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\LineStoreStatus;
use App\Models\Client;
use App\Models\RichMenu;

class Messages
{
    private $lineBot;

    private $replyToken;


    public function __construct($lineBot, $replyToken)
    {
        // $this->userId= $userId;
        // $this->channelAccessToken= $channelAccessToken;
        // $this->channelSecret= $channelSecret;
        $this->lineBot=$lineBot;
        $this->replyToken=$replyToken;
    }

    //とりあえずブロードキャストで送信
    //送信後、ヘッダーからrequestIDを貰う
    public function sendMessage()
    {
        $this->lineBot->replyMessage(
            [
'replyToken' => $this->replyToken,
'messages' => [
[
'type'=> 'template',
'altText'=> '分岐テンプレート',
'template'=> [
'type'=> 'confirm',
'text'=> 'どちらを購入しますか？',
'actions'=> [
        [
          'type'=> 'message',
          'label'=> 'イベント',
          'text'=>'イベントページへ遷移',
        ],
        [
          'type'=> 'message',
          'label'=> '勉強会',
          'text'=> '勉強会ページへ遷移',

        ]
        ]]]]]
        );
    }

    public function reserveConf($storeId, $lineStore)
    {

        $this->lineBot->replyMessage(
            [
'replyToken' => $this->replyToken,
'messages' => [
[
'type' => 'text',
'text' => "予約店舗：***\n予約日時：***\n予約商品：**コース\n人数：**\nお支払い:**\n"
],

[
'type'=> 'template',
'altText'=> '予約修正テンプレート',
'template'=> [
'type'=> 'confirm',
'text'=> 'ご予約を変更しますか？',
'actions'=> [
    [
      'type'=> 'uri',
      'label'=> 'yes',
      'uri'=> $lineStore->liff_url .'/reserve?store='. $storeId,
    ],
    [
      'type'=> 'postback',
      'label'=> 'No',
      'data'=> 'no',
       'displayText'=>'しない'
    ]
    ]]]]]
        );




    }

    public function result($res)
    {
        $this->lineBot->replyMessage([
          'replyToken' => $this->replyToken,
          'messages' => [
              [
              'type' => 'text',
    'text' => $res
    ]

    ]
    ]);
    }
       public function search($userId, $storeId)
       {
        //1が退会
        $is_client = Client::where('line_user_id', $userId)->where('store_id', $storeId)->where('member_attribute_id','!=',1)->first('id');
           return $is_client;
       }

     public function sendFirstMessage($userId, $storeId)
     {
         $is_client = $this->search($userId, $storeId);
         if (!isset($is_client->id)) {
             $flag='非会員';
             $this->lineBot->deleteLinkUser($userId);
         } else {
             $flag='会員';
             $storerm=LineStoreStatus::where('store_id', $storeId)->first('member_richmenu_id');
             $rm=RichMenu::where('id', $storerm->member_richmenu_id)->first('richmenu_id');
             $this->lineBot->linkUser($userId, $rm->richmenu_id);
         }

         // $imgUrl = secure_asset('img/Commands_logo.png');
         $this->lineBot->replyMessage(
             [
'replyToken' => $this->replyToken,
'messages' => [
[
'type' => 'text',
'text' => "友達登録ありがとうございます！\nあなたは".$flag."メニューをご利用いただけます"
],

                     ]]
         );
     }

    public function sendPushMessage($userId, $webMsg)
    {
        $res = $this->lineBot->sendPush([
          'to' => $userId,

    'messages' => [
              [
'type' => 'text',
'text' =>$webMsg
              ],

          ]
            ]);
    }
}
