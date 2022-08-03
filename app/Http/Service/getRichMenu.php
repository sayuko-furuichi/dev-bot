<?php

namespace App\Http\Service;

use LINE\LINEBot;
use App\Http\Controllers\LINEBotTiny;
use Illuminate\Http\Request;
use LINE\LINEBot\Constant\ActionType;
use LINE\LINEBot\RichMenuBuilder;
use LINE\LINEBot\RichMenuBuilder\RichMenuSizeBuilder;
use LINE\LINEBot\RichMenuBuilder\RichMenuAreaBuilder;
use LINE\LINEBot\RichMenuBuilder\RichMenuAreaBoundsBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\HTTPClient;
use PHPUnit\Framework\TestCase;
use App\Models\UserProf;

class getRichMenu
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

    public function creater()
    {
        $res= $this->createRm();
       $res= json_decode($res,true);
       $rmId=$res['richMenuId'];
        //画像UP
        $res= $this->client->upRmImg($rmId);
        $res= $this->client->defaultRm($rmId);
        $res= $this->createAliasRm($rmId);


        return $res;


        // $response=$this->client->validateRm([
        //色んなサイズの物があったらいいかも。
    }
    
    public function createRm()
    {
        //作成


        $res=$this->client->rtRichMenu([

    'size'=>[
        'width'=>2500,
        'height'=>1686
    ],
    'selected'=> true,
    'name'=> 'demo_3',
    'chatBarText'=> 'alias menu',
    //ここでarray()を使用しないと配列になってくれない。JSONで[]なってるところ。
    'areas'=> [[

        'bounds'=> [
            'x'=> 0,
            'y'=> 501,
            'width'=> 1250,
            'height'=> 200
        ],
        'action'=> [
            'type'=> 'uri',
            'uri'=> 'https://developers.line.biz/en/news/'
        ],
    ],
    [
        'bounds'=> [
            'x'=>0,
            'y'=> 702,
            'width'=> 833,
            'height'=> 800
        ],
        'action'=> [
            'type'=> 'uri',
            'uri'=> 'https://developers.line.biz/en/news/'
            ]
        ],
        [
       //   E 切り替えアクション
          'bounds'=> [
            'x'=>0,
            'y'=> 0,
            'width'=> 2500,
            'height'=>500
        ],
        'action'=> [
            'type'=> 'richmenuswitch',
           // 切り替え[先]設定
            'richMenuAliasId'=> 'demo_2_b',
            'data'=> 'richmenu-changed-to-b'
        ]
        ]


        ],
        ]);

        return $res;
    }


    public function createAliasRm($rmId)
    {


        //エイリアス作成

        $res= $this->client->createAlias([
    'richMenuAliasId'=> 'demo_2_b',
   'richMenuId'=>$rmId,
  ]);

        return $res;
    }
}
