<?php

namespace App\Http\Service;

use Illuminate\Http\Request;
use App\Models\RichMenu;
use App\Models\Store;
use App\Models\LineStoreStatus;

class CreateRichmenu
{
    //LINEBotTiny lineBot
    private $lineBot;

    /**
     * Undocumented __construct
     *
     * @param SLINEBotTiny $lineBot
     */
    public function __construct($lineBot)
    {
        $this->lineBot=$lineBot;
    }

    function  is_set($storeId){
        $old = RichMenu::where('store_id',$storeId,)->where('is_default',1)->first();
        return $old;
    }


    public function creater($storeId)
    {
        $rmA= new RichMenu();
        $rmB= new RichMenu();
        $non_MemberRm = new RichMenu;
        //  $rmC= new RichMenu;

        //  $strAl= date('Y-m-d-H-i-s');
        // $strs=date('Y-m-d-s');
        $strs=uniqid('');
        $rmA ->richmenu_alias_id =  $strs . '_a';
        $rmB ->richmenu_alias_id=  $strs . '_b';

        $non_MemberRm ->richmenu_alias_id='';

        // $this->rmAlIdA='Al_'. $strAl . '_a';
        // $this->rmAlIdB='Al_'. $strAl . '_b';
        //   $str=date('Y-m-d-s');
        // $str=uniqid('');
        $str='まる';
        $rmA->richmenu_name = $str . '_member';
        $rmB->richmenu_name=$str . '_member_non';

        $non_MemberRm->richmenu_name=$str . '_nonmember';


        $rmA->menu_bar_title='メニュー/ON/OFF';
        $rmB->menu_bar_title="メニュー/ON/OFF";

        $non_MemberRm->menu_bar_title="メニュー/ON/OFF";

        $lineStore =LineStoreStatus::where('store_id', $storeId)->first();


        //create rich menu A
        $res= $this->createRmA($rmA, $rmB, $lineStore, $storeId);
        $rs= json_decode($res, true);
        $rmA->richmenu_id=$rs['richMenuId'];

        //create rich menu B
        $res= $this->createRmB($rmA, $rmB, $lineStore, $storeId);
        $rs= json_decode($res, true);
        $rmB->richmenu_id=$rs['richMenuId'];

        $res= $this->createNonMemberRm($non_MemberRm, $lineStore, $storeId);
        $rs= json_decode($res, true);
        $non_MemberRm->richmenu_id=$rs['richMenuId'];

        //画像のURLを設定する
        $imgUrlA=secure_asset('img/richmenu/mr_member.png');
        $imgUrlB=secure_asset('img/richmenu/mr_nonmember.png');

        //画像UP
        $res= $this->lineBot->upRmImgA($rmA->richmenu_id, $imgUrlA);
        $rmA->image='img/richmenu/mr_member.png';

        $res= $this->lineBot->upRmImgA($rmB->richmenu_id, $imgUrlB);
        $rmB->image='img/richmenu/mr_nonmember.png';

        $res= $this->lineBot->upRmImgA($non_MemberRm->richmenu_id, $imgUrlB);
        $non_MemberRm->image='img/richmenu/mr_nonmember.png';

        $res= $this->lineBot->defaultRm($non_MemberRm->richmenu_id);


        //前のデフォルトをDBで更新
        $old = RichMenu::where('is_default', 1)->where('store_id', $storeId)->first();
        if (isset($old)) {
            $old->is_default=0;
            $old->save();
        }

        $rmA->is_default=0;
        $rmB->is_default=0;
        $non_MemberRm->is_default=1;

        $res= $this->createAliasRmA($rmA);

        $res= $this->createAliasRmA($rmB);


        //store_idを入れる

        $rmA->store_id= $storeId;
        $rmB->store_id= $storeId;
        $non_MemberRm->store_id= $storeId;

        // $rms= new RichMenu();
        // $rms->richmenu_id=$this->rmIdA;
    //    return $rmC;
        $rmA->save();

        $rmB->save();
        $non_MemberRm->save();

        $lineStore->member_richmenu_id=$rmA->id;
        $lineStore->save();

        return $res;



        // $response=$this->client->validateRm([
        //色んなサイズの物があったらいいかも。
    }


//2枚作成する

//会員
    public function createRmA($rmA, $rmB, $lineStore, $storeId)
    {
        //作成

        $res=$this->lineBot->rtRichMenu([

    'size'=>[
    'width'=>2500,
    'height'=>1686
    ],
    'selected'=> false,
    'name'=> $rmA->richmenu_name,
    'chatBarText'=> $rmA->menu_bar_title,
    //ここでarray()を使用しないと配列になってくれない。JSONで[]なってるところ。
    'areas'=> [[

    //A shop_card
    'bounds'=> [
        'x'=> 30,
        'y'=> 320,
        'width'=> 1180,
        'height'=> 600
    ],
    'action'=> [
        'type'=> 'uri',
        //ext_app
        'uri'=> $lineStore->liff_url .'/stamps?store='.$storeId
    ]
    ],
    // B LIFF マイページ
    [
        'bounds'=> [
            'x'=> 1290,
            'y'=> 320,
            'width'=> 1180,
            'height'=> 600
        ],
    'action'=> [
        'type'=> 'uri',
        //LIFF
        'uri'=> $lineStore->liff_url . '/Member?store='.$storeId
        ]
    ],

    [
       //  C 注文する
       'bounds'=> [
        'x'=> 30,
        'y'=> 990,
        'width'=> 1180,
        'height'=> 600
    ],
    'action'=> [
        'type'=> 'uri',
        'uri'=> $lineStore->liff_url .'/reserve?store='. $storeId,
    ]
    ],
    [
        //   D 予約確認
        'bounds'=> [
            'x'=> 1290,
            'y'=> 990,
            'width'=> 1180,
            'height'=> 600
        ],
        //postbackで店舗ID投げる
         'action'=> [
            'type'=> 'message',
            'text'=> '予約確認',
        ]
         ],

         [
            //   E 2へ切り替え
               'bounds'=> [
                 'x'=>10,
                 'y'=> 20,
                 'width'=> 1210,
                 'height'=>220
             ],
             'action'=> [
                'type'=> 'richmenuswitch',
               // 切り替え先設定
                'richMenuAliasId'=>$rmB ->richmenu_alias_id,
                'data'=> 'changed=member_menu'
            ]
             ],

    ],
    ]);

        return $res;
    }

    //非会員
    public function createRmB($rmA, $rmB, $lineStore, $storeId)
    {
        $res=$this->lineBot->rtRichMenu([

            'size'=>[
            'width'=>2500,
            'height'=>1686
            ],
            'selected'=> false,
            'name'=> $rmB->richmenu_name,
            'chatBarText'=> $rmB->menu_bar_title,
            //ここでarray()を使用しないと配列になってくれない。JSONで[]なってるところ。
            'areas'=> [[

    //
    //A shop_card
    'bounds'=> [
        'x'=> 30,
        'y'=> 320,
        'width'=> 1180,
        'height'=> 600
    ],
    'action'=> [
        'type'=> 'uri',
        //ext_app
        'uri'=> $lineStore->liff_url .'/stamps?store='.$storeId
    ]
    ],
    // B LIFF マイページ
    [
        'bounds'=> [
            'x'=> 1290,
            'y'=> 320,
            'width'=> 1180,
            'height'=> 600
        ],
    'action'=> [
        'type'=> 'uri',
        //LIFF
        'uri'=> $lineStore->liff_url . '/addMember?store='.$storeId
        ]
    ],

    [
       //  C 注文する
       'bounds'=> [
        'x'=> 30,
        'y'=> 990,
        'width'=> 1180,
        'height'=> 600
    ],
    'action'=> [
        'type'=> 'uri',
        'uri'=> $lineStore->liff_url .'/reserve?store='. $storeId,
    ]
    ],
    [
        //   D 予約確認
        'bounds'=> [
            'x'=> 1290,
            'y'=> 990,
            'width'=> 1180,
            'height'=> 600
        ],
        //postbackで店舗ID投げる
         'action'=> [
            'type'=> 'message',
            'text'=> '予約確認',
        ]
         ],

                 [
                    //   F 1へ切り替え
                       'bounds'=> [
                         'x'=>1275,
                         'y'=> 20,
                         'width'=> 1210,
                         'height'=>220
                     ],
                     'action'=> [
                        'type'=> 'richmenuswitch',
                       // 切り替え先設定
                        'richMenuAliasId'=>$rmA ->richmenu_alias_id,
                        'data'=> 'changed'
                    ]
                     ],



                    ],]);
        return $res;
    }

 //非会員
 public function createNonMemberRm($rmB, $lineStore, $storeId)
 {
     $res=$this->lineBot->rtRichMenu([

         'size'=>[
         'width'=>2500,
         'height'=>1686
         ],
         'selected'=> false,
         'name'=> $rmB->richmenu_name,
         'chatBarText'=> $rmB->menu_bar_title,
         //ここでarray()を使用しないと配列になってくれない。JSONで[]なってるところ。
         'areas'=> [[

 //
 //A shop_card
 'bounds'=> [
     'x'=> 30,
     'y'=> 320,
     'width'=> 1180,
     'height'=> 600
 ],
 'action'=> [
     'type'=> 'uri',
     //ext_app
     'uri'=> $lineStore->liff_url .'/stamps?store='.$storeId
 ]
 ],
 // B LIFF マイページ
 [
     'bounds'=> [
         'x'=> 1290,
         'y'=> 320,
         'width'=> 1180,
         'height'=> 600
     ],
 'action'=> [
     'type'=> 'uri',
     //LIFF
     'uri'=> $lineStore->liff_url . '/addMember?store='.$storeId
     ]
 ],

 [
    //  C 注文する
    'bounds'=> [
     'x'=> 30,
     'y'=> 990,
     'width'=> 1180,
     'height'=> 600
 ],
 'action'=> [
     'type'=> 'uri',
     'uri'=> $lineStore->liff_url .'/reserve?store='. $storeId,
 ]
 ],
 [
     //   D 予約確認
     'bounds'=> [
         'x'=> 1290,
         'y'=> 990,
         'width'=> 1180,
         'height'=> 600
     ],
     //postbackで店舗ID投げる
      'action'=> [
         'type'=> 'message',
         'text'=> '予約確認',
     ]
      ],

              [
                 //   F 1へ切り替え
                    'bounds'=> [
                      'x'=>1275,
                      'y'=> 20,
                      'width'=> 1210,
                      'height'=>220
                  ],
                  'action'=> [
                     'type'=> 'postback',
                    // 切り替え先設定
                     'data'=> 'richmenu-changed-to-a'
                 ]
                  ],



                 ],]);
     return $res;
 }


    public function createAliasRmA($rm)
    {
        //エイリアス作成

        $res= $this->lineBot->createAlias([
    'richMenuAliasId'=>$rm ->richmenu_alias_id,
   'richMenuId'=>$rm->richmenu_id,
  ]);

        return $res;
    }



    public function getList($storeId)
    {
        //DBから持ってきて、POSTする
        //TODO:Jsonで送る？
        $list=RichMenu::where('store_id', $storeId)->get();

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
