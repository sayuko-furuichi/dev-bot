<?php

namespace App\Http\Service;

use LINE\LINEBot;
use App\Http\Controllers\LINEBotTiny;
use Illuminate\Http\Request;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\HTTPClient;
use App\Models\RichMenu;

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
    private $rmIdC;

    private $rmNmA;
    private $rmNmB;
    private $rmNmC;

    private $rmAlIdA;
    private $rmAlIdB;
    private $rmAlIdC;

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

      //  $strAl= date('Y-m-d-H-i-s');
     // $strs=date('Y-m-d-s');
     $strs=uniqid('');
        $this->rmAlIdA=  $strs . '_a';
        $this->rmAlIdB=  $strs . '_b';
        $this->rmAlIdC=  $strs . '_c';

        // $this->rmAlIdA='Al_'. $strAl . '_a';
        // $this->rmAlIdB='Al_'. $strAl . '_b';
     //   $str=date('Y-m-d-s');
     $str=uniqid('');
        $this->rmNmA=$str . '_a';
        $this->rmNmB=$str . '_b';
        $this->rmNmC=$str . '_c';

        //create rich menu A
        $res= $this->createRmA();
        $rs= json_decode($res, true);
        $this->rmIdA=$rs['richMenuId'];
       
        //create rich menu B
        $res= $this->createRmB();
        $rs= json_decode($res, true);
        $this->rmIdB=$rs['richMenuId'];


         //OK
         $res= $this->createRmC();
         $rs= json_decode($res, true);
         $this->rmIdC=$rs['richMenuId'];

        //画像UP
        $res= $this->client->upRmImgA($this->rmIdA);
        $res= $this->client->upRmImgB($this->rmIdB);
            //OK
        $res= $this->client->upRmImgC($this->rmIdC);

       

        $res= $this->client->defaultRm($this->rmIdA);
        $res= $this->createAliasRmA($this->rmIdA);
        $res= $this->createAliasRmB($this->rmIdB);
        //OK
        $res= $this->createAliasRmC($this->rmIdC);

        // $rms= new RichMenu();
        // $rms->richmenu_id=$this->rmIdA;


        return $res;

     //   return $res;
     


        // $response=$this->client->validateRm([
        //色んなサイズの物があったらいいかも。
    }


//３枚作成する



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
    ]
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
       // 切り替え先設定
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
            'text'=> '普請中です',
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
                 'text'=> '普請中です',
             ]
             ],
             [
                //   Bへの 切り替えアクション
                   'bounds'=> [
                     'x'=>1507,
                     'y'=> 0,
                     'width'=> 937,
                     'height'=>152
                 ],
                 'action'=> [
                     'type'=> 'richmenuswitch',
                    // 切り替え先設定
                     'richMenuAliasId'=>$this->rmAlIdB,
                     'data'=> 'richmenu-changed-to-b'
                 ]
                 ],
                 [
                    //  Cへの 切り替えアクション
                       'bounds'=> [
                         'x'=>1022,
                         'y'=> 34,
                         'width'=> 433,
                         'height'=>152
                     ],
                     'action'=> [
                         'type'=> 'richmenuswitch',
                        // 切り替え先設定
                         'richMenuAliasId'=>$this->rmAlIdC,
                         'data'=> 'richmenu-changed-to-c'
                     ]
                     ],

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
                ]
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
                        'text'=> '普請中です',
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
                             'text'=> '普請中です',
                         ]
                         ],

                             [
                                //   Aへの 切り替えアクション
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
                                     'data'=> 'richmenu-changed-to-a'
                                 ]
                                 ], 
                                 [
                                    //  Cの 切り替えアクション
                                       'bounds'=> [
                                         'x'=>1022,
                                         'y'=> 34,
                                         'width'=> 433,
                                         'height'=>152
                                     ],
                                     'action'=> [
                                         'type'=> 'richmenuswitch',
                                        // 切り替え先設定
                                         'richMenuAliasId'=>$this->rmAlIdC,
                                         'data'=> 'richmenu-changed-to-c'
                                     ]
                                     ],

                ],
                ]);
        return $res;
    }

    //C作成
    public function createRmC()
    {


        //作成

        $res=$this->client->rtRichMenu([

    'size'=>[
    'width'=>2500,
    'height'=>1686
    ],
    'selected'=> false,
    'name'=> $this->rmNmA,
    'chatBarText'=> 'リッチメニュー3',
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
        'uri'=> 'https://dev-liff.herokuapp.com/public/send'
    ]
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
        'type'=> 'message',
        //LIFF
        'text'=> 'ID'
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
       // 切り替え先設定
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
            'text'=> '普請中です',
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
                 'text'=> '普請中です',
             ]
             ],
             [
                //   Bへの 切り替えアクション
                   'bounds'=> [
                     'x'=>1507,
                     'y'=> 0,
                     'width'=> 937,
                     'height'=>152
                 ],
                 'action'=> [
                     'type'=> 'richmenuswitch',
                    // 切り替え先設定
                     'richMenuAliasId'=>$this->rmAlIdB,
                     'data'=> 'richmenu-changed-to-b'
                 ]
                 ],  
                 [
                    //   Aへの 切り替えアクション
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
                         'data'=> 'richmenu-changed-to-a'
                     ]
                     ], 
                 

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
    public function createAliasRmC()
    {
        //エイリアス作成

        $res= $this->client->createAlias([
    'richMenuAliasId'=> $this->rmAlIdC,
   'richMenuId'=>$this->rmIdC,
  ]);

        return $res;
    }
}
