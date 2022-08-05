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

    //rich menu id
    private $rmIdA;
    private $rmIdB;

    private $rmNmA;
    private $rmNmB;

    private $rmAlIdA;
    private $rmAlIdB;

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

        //TODO:各フィードバッグ後の、trueなら続行、falseなら中断の分岐(trycatchでもいいかも？)

        $strAl= date('Y-m-d-H-i-s');

         $this->rmAlIdA='Al_'. $strAl . '_a';
         $this->rmAlIdB='Al_'. $strAl . '_b';

        $str=date('Y-m-d');

        $this->rmNmA=$str . '_a';
        $this->rmNmB=$str . '_b';

        //create rich menu A
        $res= $this->createRmA();
        $rs= json_decode($res, true);
        $this->rmIdA=$rs['richMenuId'];

        //create rich menu B
        $res= $this->createRmB();
        $rs= json_decode($res, true);
        $this->rmIdB=$rs['richMenuId'];


        //画像UP
        $res= $this->client->upRmImgA($this->rmIdA);
        $res= $this->client->upRmImgB($this->rmIdB);

        $res= $this->client->defaultRm($this->rmIdA);
        $res= $this->createAliasRmA($this->rmIdA);
        $res= $this->createAliasRmB($this->rmIdB);


        return $res;


        // $response=$this->client->validateRm([
        //色んなサイズの物があったらいいかも。
    }

    public function createRmA()
    {


        //作成

        $res=$this->client->rtRichMenu([

    'size'=>[
    'width'=>2500,
    'height'=>1686
    ],
    'selected'=> false,
    'name'=> $this->rmNmA,
    'chatBarText'=> 'リッチメニュー1',
    //ここでarray()を使用しないと配列になってくれない。JSONで[]なってるところ。
    'areas'=> [[

    //A
    'bounds'=> [
        'x'=> 13,
        'y'=> 223,
        'width'=> 1227,
        'height'=> 478
    ],
    'action'=> [
        'type'=> 'uri',
        //ext_app
        'uri'=> 'https://dev-ext-app.herokuapp.com/public/login'
    ],
    ],
    // B
    [
    'bounds'=> [
        'x'=>1300,
        'y'=> 246,
        'width'=> 1158,
        'height'=> 437
    ],
    'action'=> [
        'type'=> 'uri',
        //LIFF
        'uri'=> 'https://liff.line.me/1657181787-2vrnwwlj'
        ]
    ],

    [
       //  C
      'bounds'=> [
        'x'=>32,
        'y'=> 756,
         'width'=> 1176,
        'height'=>441
    ],
    'action'=> [
        'type'=> 'message',
       // 切り替え[先]設定
       'text'=>'限定メニュー'
    ]
    ],
    [
        //   D
           'bounds'=> [
             'x'=>1300,
             'y'=> 756,
             'width'=> 1144,
             'height'=>892
         ],
         'action'=> [
            'type'=> 'message',
            'text'=> '普請中',
        ]
         ],

         [
            //   E
               'bounds'=> [
                 'x'=>82,
                 'y'=> 1276,
                 'width'=> 1108,
                 'height'=>386
             ],
             'action'=> [
                 'type'=> 'message',
                 'text'=> '普請中',
             ]
             ],
             [
                //   t-2 切り替えアクション
                   'bounds'=> [
                     'x'=>1507,
                     'y'=> 0,
                     'width'=> 937,
                     'height'=>152
                 ],
                 'action'=> [
                     'type'=> 'richmenuswitch',
                    // 切り替え[先]設定
                     'richMenuAliasId'=>$this->rmAlIdB,
                     'data'=> 'richmenu-changed-to-a'
                 ]
                 ]

    ],
    ]);

        return $res;
    }

    public function createRmB()
    {
        $res=$this->client->rtRichMenu([

            'size'=>[
                'width'=>2500,
                'height'=>1686
            ],
            'selected'=> false,
            'name'=> $this->rmNmB,
            'chatBarText'=> 'リッチメニュー2',
            //ここでarray()を使用しないと配列になってくれない。JSONで[]なってるところ。
            'areas'=> [[

                //A
                'bounds'=> [
                    'x'=> 13,
                    'y'=> 223,
                    'width'=> 1227,
                    'height'=> 478
                ],
                'action'=> [
                    'type'=> 'uri',
                    //ext_app
                    'uri'=> 'https://dev-ext-app.herokuapp.com/public/login'
                ],
            ],
            // B
            [
                'bounds'=> [
                    'x'=>1300,
                    'y'=> 246,
                    'width'=> 1158,
                    'height'=> 437
                ],
                'action'=> [
                    'type'=> 'uri',
                    //LIFF
                    'uri'=> 'https://liff.line.me/1657181787-2vrnwwlj'
                    ]
                ],

                [
               //  C
                  'bounds'=> [
                    'x'=>32,
                    'y'=> 756,
                     'width'=> 1176,
                    'height'=>441
                ],
                'action'=> [
                    'type'=> 'message',
                   // 切り替え[先]設定
                   'text'=>'限定メニュー'
                ]
                ],
                [
                    //   D
                       'bounds'=> [
                         'x'=>1300,
                         'y'=> 756,
                         'width'=> 1144,
                         'height'=>892
                     ],
                     'action'=> [
                        'type'=> 'message',
                        'text'=> '普請中',
                    ]
                     ],

                     [
                        //   E
                           'bounds'=> [
                             'x'=>82,
                             'y'=> 1276,
                             'width'=> 1108,
                             'height'=>386
                         ],
                         'action'=> [
                             'type'=> 'message',
                             'text'=> '普請中',
                         ]
                         ],

                             [
                                //   t-1 切り替えアクション
                                   'bounds'=> [
                                     'x'=>0,
                                     'y'=> 0,
                                     'width'=> 974,
                                     'height'=>170
                                 ],
                                 'action'=> [
                                     'type'=> 'richmenuswitch',
                                    // 切り替え[先]設定
                                     'richMenuAliasId'=> $this->rmAlIdA,
                                     'data'=> 'richmenu-changed-to-b'
                                 ]
                                 ]

                ],
                ]);
        return $res;
    }


    public function createAliasRmA()
    {


        //エイリアス作成

        $res= $this->client->createAlias([
    'richMenuAliasId'=>$this->rmAlIdA,
   'richMenuId'=>$this->rmIdA,
  ]);

        return $res;
    }

    public function createAliasRmB()
    {
        //エイリアス作成

        $res= $this->client->createAlias([
    'richMenuAliasId'=> $this->rmAlIdB,
   'richMenuId'=>$this->rmIdB,
  ]);

        return $res;
    }
}
