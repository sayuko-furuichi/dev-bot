<?php

namespace App\Http\Service;

use LINE\LINEBot;
use App\Http\Controllers\LINEBotTiny;
use Illuminate\Http\Request;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\HTTPClient;
use App\Models\RichMenu;
use Illuminate\Support\Facades\DB;
use App\Models\Store;

class getEnisRm
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

    public function creater($storeId)
    {
        //TODO:各フィードバッグ後の、trueなら続行、falseなら中断の分岐(trycatchでもいいかも？)

        $rmA= new RichMenu;
        $rmB= new RichMenu;
      //  $rmC= new RichMenu;

        //  $strAl= date('Y-m-d-H-i-s');
        // $strs=date('Y-m-d-s');
        $strs=uniqid('');
        $rmA ->richmenu_alias_id =  $strs . '_a';
        $rmB ->richmenu_alias_id=  $strs . '_b';
       

        // $this->rmAlIdA='Al_'. $strAl . '_a';
        // $this->rmAlIdB='Al_'. $strAl . '_b';
        //   $str=date('Y-m-d-s');
       // $str=uniqid('');
       $str='まる_会員';
        $rmA->name = $str . '_a';
        $rmB->name=$str . '_b';
    

        $rmA->chat_bar='メニュー/ON/OFF';
        $rmB->chat_bar="メニュー/ON/OFF";
    
       $simg =Store::where('id',$storeId)->first();

      
        //create rich menu A
        $res= $this->createRmA($rmA, $rmB,$simg);
        $rs= json_decode($res, true);
        $rmA->richmenu_id=$rs['richMenuId'];
   
        //create rich menu B
        $res= $this->createRmB($rmA,$rmB,$simg);
        $rs= json_decode($res, true);
        $rmB->richmenu_id=$rs['richMenuId'];

    

        //画像UP
        $res= $this->client->upRmImgA($rmA->richmenu_id);
        $rmA->img='img/1.png';

        $res= $this->client->upRmImgB($rmB->richmenu_id);
        $rmB->img='img/n2.png';

        $res= $this->client->defaultRm($rmA->richmenu_id);

         
        //前のデフォルトをDBで更新
        //TODO:もっと効率よく参照したい
        $old = RichMenu::where('is_default',1)->where('store_id',$storeId)->first();
        if(isset($old)){
            $old->is_default=0;
            $old->save();
        }
      
        $rmA->is_default=1;
        $rmB->is_default=0;

        $res= $this->createAliasRmA($rmA);

        $res= $this->createAliasRmB($rmB);

    
        //store_idを入れる
       
        $rmA->store_id= $storeId;
        $rmB->store_id= $storeId;

        // $rms= new RichMenu();
        // $rms->richmenu_id=$this->rmIdA;
    //    return $rmC;
        $rmA->save();

        $rmB->save();


        return $res;



        // $response=$this->client->validateRm([
        //色んなサイズの物があったらいいかも。
    }


//３枚作成する


    public function createRmA($rmA, $rmB,$simg)
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

    //A shop_card
    'bounds'=> [
        'x'=> 85,
        'y'=> 370,
        'width'=> 1140,
        'height'=> 530
    ],
    'action'=> [
        'type'=> 'uri',
        //ext_app
        'uri'=> $simg->card_url
    ]
    ],
    // B LIFF マイページ
    [
        'bounds'=> [
            'x'=> 1300,
            'y'=> 370,
            'width'=> 1140,
            'height'=> 530
        ],
    'action'=> [
        'type'=> 'uri',
        //LIFF
        'uri'=> $simg->liff_url
        ]
    ],

    [
       //  C 注文する
       'bounds'=> [
        'x'=> 85,
        'y'=> 1020,
        'width'=> 1140,
        'height'=> 530
    ],
    'action'=> [
        'type'=> 'message',
       // 切り替え先設定
       'text'=>'注文する'
    ]
    ],
    [
        //   D 予約確認
        'bounds'=> [
            'x'=> 1300,
            'y'=> 1020,
            'width'=> 1140,
            'height'=> 530
        ],
         'action'=> [
            'type'=> 'message',
            'text'=> '予約確認',
        ]
         ],

         [
            //   E 2へ切り替え
               'bounds'=> [
                 'x'=>1305,
                 'y'=> 80,
                 'width'=> 1175,
                 'height'=>175
             ],
             'action'=> [
                'type'=> 'richmenuswitch',
               // 切り替え先設定
                'richMenuAliasId'=>$rmB ->richmenu_alias_id,
                'data'=> 'changed=nonMember_b'
            ]
             ],

    ],
    ]);

        return $res;
    }

    public function createRmB($rmA,$rmB,$simg)
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
        
                'bounds'=> [
                    'x'=> 75,
                    'y'=> 1000,
                    'width'=> 2360,
                    'height'=> 540
                ],
                'action'=> [
                    'type'=> 'uri',
                    //ext_app
                    'uri'=> $simg->liff_url . '/addMember'
                ]
            //A shop_card
            // 'bounds'=> [
            //     'x'=> 85,
            //     'y'=> 370,
            //     'width'=> 1140,
            //     'height'=> 530
            // ],
            // 'action'=> [
            //     'type'=> 'uri',
            //     //ext_app
            //     'uri'=> $simg->card_url
            // ]
            ],
            // B LIFF マイページ
            // [
            //     'bounds'=> [
            //         'x'=> 1300,
            //         'y'=> 370,
            //         'width'=> 1140,
            //         'height'=> 530
            //     ],
            // 'action'=> [
            //     'type'=> 'uri',
            //     //LIFF
            //     'uri'=> $simg->liff_url
            //     ]
            // ],
        
            // [
            //    //  C 注文する
            //    'bounds'=> [
            //     'x'=> 85,
            //     'y'=> 1020,
            //     'width'=> 1140,
            //     'height'=> 530
            // ],
            // 'action'=> [
            //     'type'=> 'message',
            //    // 切り替え先設定
            //    'text'=>'注文する'
            // ]
            // ],
            // [
            //     //   D 予約確認
            //     'bounds'=> [
            //         'x'=> 1300,
            //         'y'=> 1020,
            //         'width'=> 1140,
            //         'height'=> 530
            //     ],
            //      'action'=> [
            //         'type'=> 'message',
            //         'text'=> '予約確認',
            //     ]
            //      ],
        
        
                 [
                    //   F 1へ切り替え
                       'bounds'=> [
                         'x'=>55,
                         'y'=> 80,
                         'width'=> 1175,
                         'height'=>175
                     ],
                     'action'=> [
                        'type'=> 'richmenuswitch',
                       // 切り替え先設定
                        'richMenuAliasId'=>$rmA ->richmenu_alias_id,
                        'data'=> 'richmenu-changed-to-a'
                    ]
                     ],
        
            
            
                    ],] );
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



    public function getList($storeId){

        //DBから持ってきて、POSTする
        //TODO:Jsonで送る？
        $list=RichMenu::where('store_id',$storeId)->get();

        $header = array(
            'Content-Type: application/json',
        );

       
         $context = stream_context_create([
             'http' => [
                 'ignore_errors' => true,
                 'method' => 'POST',
                 'header' => implode("\r\n", $header),
                 'content' =>json_encode($list)
             ],
         ]);
         //   var_dump($detail);
    
     return  file_get_contents('https://dev-ext-app.herokuapp.com/public/rich', false, $context);

         if (strpos($http_response_header[0], '200') === false) {
             $res = 'false';
         }

    }

    }






    