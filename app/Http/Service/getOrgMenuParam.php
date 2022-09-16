<?php 

namespace App\Http\Service;
use Illuminate\Support\Facades\Storage;
class getOrgMenuParam{

// $thisでダメで、self::だったら行けるかもしれない。

  // private String $imgUrl;
  // private String $imgUrl2;
  // private String $title;
  // private String $text;

public function getParam($sId, $client,$event){

  //店舗によって送信するメッセージを変更する。

  if($sId == '4'){
    $imgUrl ='https://dev-bot0722.herokuapp.com/storage/app/public/img/cake1.jpg';
    $imgUrl2 ='https://dev-bot0722.herokuapp.com/storage/app/public/img/cafe1.jpg';
    $title='Demo本店限定！ティラミス';
    $title2='カプチーノ';
    $text='コーヒーに合うよ';
    $text2='こだわりのコーヒー';


    }elseif($sId == '14'){
      $imgUrl ='https://dev-bot0722.herokuapp.com/storage/app/public/img/tea2.jpg';
      $imgUrl2 ='https://dev-bot0722.herokuapp.com/storage/app/public/img/sand2.jpg';
      $title='本格派 抹茶ラテ';
      $text='人気商品です';
      $title2='サンドイッチ';
      $text2='テイクアウトできます！';

    }elseif($sId == '24'){
      $imgUrl ='https://dev-bot0722.herokuapp.com/storage/app/public/img/roll3.jpg';
      $imgUrl2 ='https://dev-bot0722.herokuapp.com/storage/app/public/img/al3.jpg';
      $title='自家製ロールケーキ';
      $text='1番人気の商品です';
      $title2='カクテル';
      $text2='まったりしませんか(*´ω｀)';
  
    }

    //テンプレートを、変数でレンダリングする方式
    //2clumnのカルーセルテンプレートメッセージ

    $client->replyMessage([
        'replyToken' => $event['replyToken'],
        'messages' => [

[
    'type'=> 'template',
    'altText'=> 'this is a carousel template',
    'template'=> [
      'type'=> 'carousel',
      'columns'=> [
        [
          'thumbnailImageUrl'=> $imgUrl,
          'imageBackgroundColor'=> '#FFFFFF',
          'title'=>  $title,
          'text'=>  $text,
          'defaultAction'=> [
            'type'=> 'uri',
            'label'=> 'View detail',
            'uri'=> 'https://dev-ext-app.herokuapp.com/public/login'
          ],
          'actions'=> [
            [
              'type'=> 'postback',
              'label'=> 'Buy',
              'data'=> 'action=buy&itemid=111'
            ],
            // [
            // 'type'=> 'postback',
            //  'label'=> 'Add to cart',
            //   'data'=> 'action=add&itemid=111'
            // ],
            [
              'type'=> 'uri',
              'label'=> '外部webアプリで注文',
              'uri'=> 'https://dev-ext-app.herokuapp.com/public/login'
            ]
          ]
        ],
        [
          'thumbnailImageUrl'=>$imgUrl2,
          'imageBackgroundColor'=> '#000000',
          'title'=> $title2,
          'text'=>  $text2,
          'defaultAction'=> [
            'type'=> 'uri',
            'label'=> 'View detail',
            'uri'=> 'https://liff.line.me/1657181787-2vrnwwlj'
          ],
          'actions'=> [
            [
              'type'=> 'postback',
              'label'=> 'Buy',
              'data'=> 'action=buy&itemid=222'
            ],
            // [
            //  'type'=> 'postback',
            //  'label'=> 'Add to cart',
            //   'data'=> 'action=add&itemid=222'
            //  ],
             [
              'type'=> 'uri',
              'label'=> 'LIFFアプリで注文',
              'uri'=> 'https://liff.line.me/1657181787-2vrnwwlj'
            ]
          ]
        ]
      ],
      'imageAspectRatio'=> 'rectangle',
      'imageSize'=> 'cover'
    ]
  ]]]);

}

}

