<?php

namespace App\Http\Service;

use Illuminate\Http\Request;
use App\Models\RichMenu;
use App\Models\LineStoreStatus;
use App\Service\Messages;

class GetCommonsRm
{


    //LINEBotTiny client
    private $client;

    /**
     * Undocumented __construct
     *
     * @param LINEBotTiny $client
     */
    public function __construct($client)
    {
        // $this->userId= $userId;

        $this->client=$client;
    }

    function  is_set($storeId){
        $old = RichMenu::where('store_id',$storeId,)->where('is_default',1)->first();
        return $old;
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
       

       $str='Commons';
        $rmA->richmenu_name = $str . '_member';
         $rmB->richmenu_name=$str . '_non_member';


        $rmA->menu_bar_title='メニュー/ON/OFF';
         $rmB->menu_bar_title="メニュー/ON/OFF";
    
       $lineStore =LineStoreStatus::where('store_id',$storeId)->first();

      
        //create rich menu A
        $res= $this->createRmA($rmA);
        $rs= json_decode($res, true);
        $rmA->richmenu_id=$rs['richMenuId'];
        //storeテーブルにも同時に設定する
        // $lineStore->member_menu=$rs['richMenuId'];
   
        //create rich menu B
         $res= $this->createRmB($rmB);
         $rs= json_decode($res, true);
         $rmB->richmenu_id=$rs['richMenuId'];
      
         //storeテーブルにも同時に設定する
        //  $lineStore->non_member_menu=$rs['richMenuId'];
    
        $imgUrlA=secure_asset('img/richmenu/cm_rm_y.png');
        $imgUrlB=secure_asset('img/richmenu/cm_rm_n.png');
        //画像UP
        $res= $this->client->upRmImgA($rmA->richmenu_id,$imgUrlA);
        $rmA->image='img/richmenu/cm_rm_y.png';

         $res= $this->client->upRmImgA($rmB->richmenu_id,$imgUrlB);
         $rmB->image='img/richmenu/cm_rm_n.png';

         //非会員メニューをデフォルトに設定
         $res= $this->client->defaultRm($rmB->richmenu_id);

         
        //前のデフォルトをDBで更新
        //TODO:もっと効率よく参照したい
        $old = RichMenu::where('is_default',1)->where('store_id',$storeId)->first();
        if(isset($old)){
            $old->is_default=0;
            $old->save();
        }
      
         $rmA->is_default=0;
         $rmB->is_default=1;

          $res= $this->createAliasRmA($rmA);

         $res= $this->createAliasRmA($rmB);

    
        //store_idを入れる
       
        $rmA->store_id= $storeId;
         $rmB->store_id= $storeId;

        // $rms= new RichMenu();
        // $rms->richmenu_id=$this->rmIdA;
    //    return $rmC;
        $rmA->save();

         $rmB->save();
      
         //リッチメニューの登録が完了してから、IDを登録する
         $lineStore->member_richmenu_id=$rmA->id;
         $lineStore->save();
        return $res;

    }

  

//2枚一気に作成する


    public function createRmA($rmA)
    {
        //作成

        $res=$this->client->rtRichMenu([

    'size'=>[
    'width'=>2500,
    'height'=>1686
    ],
    'selected'=> false,
    'name'=> $rmA->richmenu_name,
    'chatBarText'=> $rmA->menu_bar_title,
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

    public function createRmB($rmB)
    {
        $res=$this->client->rtRichMenu([

            'size'=>[
            'width'=>2500,
            'height'=>843
            ],
            'selected'=> false,
            'name'=> $rmB->richmenu_name,
            'chatBarText'=> $rmB->menu_bar_title,
            //ここでarray()を使用しないと配列になってくれない。JSONで[]なってるところ。
            'areas'=> [[

    //        A 入会申し込み
            'bounds'=> [
                'x'=> 50,
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




    public function createAliasRmA($rm)
    {
        //エイリアス作成

        $res= $this->client->createAlias([
    'richMenuAliasId'=>$rm ->richmenu_alias_id,
   'richMenuId'=>$rm->richmenu_id,
  ]);

        return $res;
    }

//     public function createAliasRmB($rmB)
//     {
//         //エイリアス作成

//         $res= $this->client->createAlias([
//     'richMenuAliasId'=> $rmB ->richmenu_alias_id,
//    'richMenuId'=>$rmB->richmenu_id,
//   ]);

//         return $res;
//     }



    }






    