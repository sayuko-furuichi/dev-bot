<?php

namespace App\Http\Service;

use LINE\LINEBot;
use App\Http\Controllers\LINEBotTiny;
use Illuminate\Http\Request;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\HTTPClient;
use App\Models\RichMenu;
use Illuminate\Support\Facades\DB;

class getRichMenu
{
    //chanell_access_token
    private $channelAccessToken;
    //chanell_secret
    private $channelSecret;

    //LINEBotTiny client
    private $client;

    //rich menu id
    // private $rmIdA;
    // private $rmIdB;
    // private $rmIdC;

    // private $rmNmA;
    // private $rmNmB;
    // private $rmNmC;

    // private $rmAlIdA;
    // private $rmAlIdB;
    // private $rmAlIdC;

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

        $rmA= new RichMenu;
        $rmB= new RichMenu;
        $rmC= new RichMenu;

      //  $strAl= date('Y-m-d-H-i-s');
     // $strs=date('Y-m-d-s');
     $strs=uniqid('');
       $rmA ->richmenu_alias_id =  $strs . '_a';
       $rmB ->richmenu_alias_id=  $strs . '_b';
       $rmC ->richmenu_alias_id=  $strs . '_c';

       
        // $this->rmAlIdA='Al_'. $strAl . '_a';
        // $this->rmAlIdB='Al_'. $strAl . '_b';
     //   $str=date('Y-m-d-s');
     $str=uniqid('');
        $rmA->name = $str . '_a';
        $rmB->name=$str . '_b';
        $rmC->name=$str . '_c';

        $rmA->chat_bar='リッチメニュー1';
        $rmB->chat_bar='リッチメニュー2';
        $rmC->chat_bar='リッチメニュー3';

        //create rich menu A
        $res= $this->createRmA($rmA,$rmB,$rmC);
        $rs= json_decode($res, true);
        $rmA->richmenu_id=$rs['richMenuId'];
       
     
        //create rich menu B
        $res= $this->createRmB($rmA,$rmB,$rmC);
        $rs= json_decode($res, true);
        $rmB->richmenu_id=$rs['richMenuId'];


         //OK
         $res= $this->createRmC($rmA,$rmB,$rmC);
         $rs= json_decode($res, true);
         $rmC->richmenu_id=$rs['richMenuId'];

        //画像UP
        $res= $this->client->upRmImgA($rmA->richmenu_id);
        $rmA->img='demo_a.png';

        $res= $this->client->upRmImgB($rmB->richmenu_id);
        $rmB->img='demo_b.png';
            //OK
        $res= $this->client->upRmImgC($rmC->richmenu_id);
        $rmC->img='demo_c.png';
       

        $res= $this->client->defaultRm($rmA->richmenu_id);
        $rmA->is_default=1;
        $rmB->is_default=0;
        $rmC->is_default=0;
        $res= $this->createAliasRmA($rmA);
         
        $res= $this->createAliasRmB($rmB);
       
        //OK
        $res= $this->createAliasRmC($rmC);

        // $rms= new RichMenu();
        // $rms->richmenu_id=$this->rmIdA;
       $res= $rmA->save();
        $rmB->save();
        $rmC->save();

        return $res;

     //   return $res;
     


        // $response=$this->client->validateRm([
        //色んなサイズの物があったらいいかも。
    }


//３枚作成する



    public function createRmA($rmA,$rmB,$rmC)
    {


        //作成

        $res=$this->client->rtRichMenu([

    'size'=>[
    'width'=>2500,
    'height'=>1686
    ],
    'selected'=> false,
    'name'=> $rmA->name,
    'chatBarText'=> $rmA->chat_bar,
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
                     'richMenuAliasId'=>$rmB ->richmenu_alias_id,
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
                         'richMenuAliasId'=>$rmC->richmenu_alias_id,
                         'data'=> 'richmenu-changed-to-c'
                     ]
                     ],

    ],
    ]);

        return $res;
    }

    public function createRmB($rmA,$rmB,$rmC)
    {
        $res=$this->client->rtRichMenu([

            'size'=>[
                'width'=>2500,
                'height'=>1686
            ],
            'selected'=> false,
            'name'=> $rmB->name,
            'chatBarText'=> $rmB->chat_bar,
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
                                     'richMenuAliasId'=> $rmA ->richmenu_alias_id,
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
                                         'richMenuAliasId'=>$rmC->richmenu_alias_id,
                                         'data'=> 'richmenu-changed-to-c'
                                     ]
                                     ],

                ],
                ]);
        return $res;
    }

    //C作成
    public function createRmC($rmA,$rmB,$rmC)
    {


        //作成

        $res=$this->client->rtRichMenu([

    'size'=>[
    'width'=>2500,
    'height'=>1686
    ],
    'selected'=> false,
    'name'=> $rmC->name,
    'chatBarText'=> $rmC->chat_bar,
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
                     'richMenuAliasId'=>$rmB ->richmenu_alias_id,
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
                         'richMenuAliasId'=> $rmA ->richmenu_alias_id,
                         'data'=> 'richmenu-changed-to-a'
                     ]
                     ], 
                 

    ],
    ]);

        return $res;
    }


    public function createAliasRmA($rmA)
    {


        //エイリアス作成

        $res= $this->client->createAlias([
    'richMenuAliasId'=>$rmA ->richmenu_alias_id,
   'richMenuId'=>$rmA->richmenu_id,
  ]);

        return $res;
    }

    public function createAliasRmB($rmB)
    {
        //エイリアス作成

        $res= $this->client->createAlias([
    'richMenuAliasId'=> $rmB ->richmenu_alias_id,
   'richMenuId'=>$rmB->richmenu_id,
  ]);

        return $res;
    }
    public function createAliasRmC($rmC)
    {
        //エイリアス作成

        $res= $this->client->createAlias([
    'richMenuAliasId'=> $rmC ->richmenu_alias_id,
   'richMenuId'=>$rmC->richmenu_id,
  ]);

        return $res;
    }
}
