<?php

namespace App\Http\Service;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\LINEBotTiny;
use App\Models\SentMessage;
use Illuminate\Support\Facades\DB;

class sendNarrow
{
    //chanell_access_token
    private $channelAccessToken;
    //chanell_secret
    private $channelSecret;

    //LINEBotTiny client
    private $client;


    public function __construct(String $channelAccessToken, String $channelSecret, $client)
    {
        // $this->userId= $userId;
        $this->channelAccessToken= $channelAccessToken;
        $this->channelSecret= $channelSecret;
        $this->client=$client;
    }

    //とりあえずブロードキャストで送信
    //送信後、ヘッダーからrequestIDを貰う
    public function sendMessage()
    {

        $imgUrl ='https://dev-bot0722.herokuapp.com/storage/app/public/img/roll3.jpg';
        $imgUrl2 ='https://dev-bot0722.herokuapp.com/storage/app/public/img/al3.jpg';
        $title='※テスト配信です※自家製ロールケーキ';
        $text='※テスト配信です※1番人気の商品です';
        $title2='※テスト配信です※カクテル';
        $text2='※テスト配信です※まったりしませんか(*´ω｀)';

        //$resに、requestidが入る
        $res = $this->client->sendBroad([

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

              $msg = new SentMessage;
              $msg->request_id=$res;
              $msg->save();
            
              return $msg;

            //   $msgId = SentMessages::where('request_id',$res)->first();

            //  return $msgId;

//               $res2 = $this->client->sendBroad([
                
       
//                 'messages' => [

//                     [
//                         'type'=> 'template',
//                         'altText'=> 'this is a carousel template',
//                         'template'=> [
//                           'type'=> 'carousel',
//                           'columns'=> [
//                             [
//                               'thumbnailImageUrl'=> $imgUrl,
//                               'imageBackgroundColor'=> '#FFFFFF',
//                               'title'=>  $title,
//                               'text'=>  $text,
//                               'defaultAction'=> [
//                                 'type'=> 'uri',
//                                 'label'=> 'View detail',
//                                 'uri'=> 'https://dev-ext-app.herokuapp.com/public/login'
//                               ],
//                               'actions'=> [
//                                 [
//                                   'type'=> 'message',
//                                   'text'=> $res
//                                 ],
//                                 // [
//                                 // 'type'=> 'postback',
//                                 //  'label'=> 'Add to cart',
//                                 //   'data'=> 'action=add&itemid=111'
//                                 // ],
//                                 [
//                                   'type'=> 'uri',
//                                   'label'=> '外部webアプリで注文',
//                                   'uri'=> 'https://dev-ext-app.herokuapp.com/public/login'
//                                 ]
//                               ]
//                             ]

//        ]]]]] );
            
// return $res2;

    }
}
