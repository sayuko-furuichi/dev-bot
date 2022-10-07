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

class getCommonsRm
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
       $str='Commons';
        $rmA->name = $str . '_big';
         $rmB->name=$str . '_small';
    

        $rmA->chat_bar='メニュー/ON/OFF';
         $rmB->chat_bar="メニュー/ON/OFF";
    
       $simg =Store::where('id',$storeId)->first();

      
        //create rich menu A
        $res= $this->createRmA($rmA,$simg);
        $res= json_decode($res, true);
        $rmA->richmenu_id=$rs['richMenuId'];
   
        //create rich menu B
         $res= $this->createRmB($rmB,$simg);
         $res= json_decode($res, true);
         $rmB->richmenu_id=$rs['richMenuId'];

    

        //画像UP
        $res= $this->client->upRmImgA($rmA->richmenu_id);
        $rmA->img='img/cm_rm_y.png';

         $res= $this->client->upRmImgB($rmB->richmenu_id);
         $rmB->img='img/cm_rm_n.png';

        // $res= $this->client->defaultRm($rmA->richmenu_id);

         
        //前のデフォルトをDBで更新
        //TODO:もっと効率よく参照したい
        $old = RichMenu::where('is_default',1)->where('store_id',$storeId)->first();
        if(isset($old)){
            $old->is_default=0;
            $old->save();
        }
      
         $rmA->is_default=0;
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

    }

  

//2枚一気に作成する


    public function createRmA($rmA, $simg)
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

    //A mypage
    'bounds'=> [
        'x'=> 30,
        'y'=> 210,
        'width'=> 750,
        'height'=> 600
    ],
    'action'=> [
        'type'=> 'message',
        'text'=> 'マイページ',
    ]
    ],
    // B 経営相談を投げる
    [
        'bounds'=> [
            'x'=> 870,
            'y'=> 210,
            'width'=> 750,
            'height'=> 600
        ],
        'action'=> [
            'type'=> 'message',
            'text'=> '経営相談を投げる',
        ]
    ],

    [
       //  C プロジェクトにjoin
       'bounds'=> [
        'x'=> 1720,
        'y'=> 210,
        'width'=> 750,
        'height'=> 600
    ],
    'action'=> [
        'type'=> 'message',
        'text'=> 'プロジェクトにjoin',
    ]
    ],
    [
        //   D 議論する場に移動
        'bounds'=> [
            'x'=> 30,
            'y'=> 910,
            'width'=> 750,
                    'height'=> 600
        ],
        //postbackで店舗ID投げる
         'action'=> [
            'type'=> 'message',
            'text'=> '議論する場に移動',
        ]
         ],
         [
            //   E オフラインを購入
            'bounds'=> [
                'x'=> 870,
                'y'=> 910,
                'width'=> 750,
                'height'=> 600
            ],
            //postbackで店舗ID投げる
             'action'=> [
                'type'=> 'message',
                'text'=> 'オフラインを購入',
            ]
             ],
             [
                //   F 閲覧する
                'bounds'=> [
                    'x'=> 1720,
                    'y'=> 910,
                    'width'=> 750,
                    'height'=> 600
                ],
                //postbackで店舗ID投げる
                 'action'=> [
                    'type'=> 'message',
                    'text'=> '閲覧する',
                ]
                 ],

    ],
    ]);

        return $res;
    }

    public function createRmB($rmB,$simg)
    {
        $res=$this->client->rtRichMenu([

            'size'=>[
            'width'=>2500,
            'height'=>843
            ],
            'selected'=> false,
            'name'=> $rmB->name,
            'chatBarText'=> $rmB->chat_bar,
            //ここでarray()を使用しないと配列になってくれない。JSONで[]なってるところ。
            'areas'=> [[

    //        A 入会申し込み
            'bounds'=> [
                'x'=> 735,
                'y'=> 120,
                'width'=> 740,
                'height'=> 600
            ],
            'action'=> [
                'type'=> 'message',
                'text'=> '申し込み',
            ]
           ],
       //     B LIFF 会員の方はこちら
            [
                'bounds'=> [
                    'x'=> 870,
                    'y'=> 120,
                    'width'=> 740,
                    'height'=> 600
                ],
                'action'=> [
                    'type'=> 'message',
                    'text'=> '会員の方はこちら',
                ]
            ],
        
            [
               //  C 注文する
               'bounds'=> [
                'x'=> 1710,
                'y'=> 120,
                'width'=> 740,
                'height'=> 600
            ],
            'action'=> [
                'type'=> 'message',
                'text'=> '問い合わせ',
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






    