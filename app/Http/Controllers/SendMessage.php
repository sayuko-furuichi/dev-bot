<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\LINEBotTiny;

use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot;
use Illuminate\Support\Facades\Storage;

use App\Http\Service\getOrgMenuParam;
use App\Http\Service\getRichMenu;
use App\Http\Service\sendNarrow;
use App\Http\Service\getAnalysisData;
use  App\Http\Service\SendPushMessage;
use  App\Http\Service\getMember;
use  App\Http\Service\getAudience;

//あとで消す
use App\Models\RichMenu;

class SendMessage extends Controller
{
    //postbackアクションについて
    //ユーザが、メッセージを送信せずにデータのみ送信できる機能。
    //botは、if($pt['data']＝action=***)　などで判定したらよい。

    public function send($channelAccessToken, $channelSecret, $storeId)
    {
        $client = new LINEBotTiny($channelAccessToken, $channelSecret);
        foreach ($client->parseEvents() as $event) {
            if ($event['type'] == 'postback') {
                $pt=$event['postback'];
                $ptD = $pt['data'];
                
                //会員登録するユーザ
                if (preg_match('/name=/', $pt['data'])) {
                    $member = new getMember($channelAccessToken, $channelSecret, $client);
                    $member->createMember($event, $pt, $storeId);
                
                //退会するユーザ
                } elseif (preg_match('/removeMember&id=/', $pt['data'])) {
                    $member = new getMember($channelAccessToken, $channelSecret, $client);
                    $member->remove($event, $pt, $storeId);
                }
                // $pra = new getAnalysisData($client,$event);
                //    $param->getData($ptD);


                // if($pt['data'] ==''){


                // }
            }

            //eventtypeがmessageで、messagetypeがtextの時起動


            if ($event['type'] == 'message') {
                $us = $event['source'];

                $message = $event['message'];
                //"ID"と入力されたら、ユーザIDを返す

                if ($message['text'] == 'ID') {
                    //ユーザID取得のために、event配列からsoureを代入
                    //　$us['userId']　でユーザIDを持ってこれる。
                    //TODO:!!!!!
                    $client->dltDefaultRm();

                    $use=$us['userId'];


                    $client->replyMessage([
            'replyToken' => $event['replyToken'],
            'messages' => [
                [
    'type' => 'text',
    'text' => 'This is ' . $storeId . '号店'
                ],
                [
    'type' => 'text',
    'text' =>  'あなたのユーザID：'.$us['userId']
                ]

            ]
        ]);

                // メニュー　と言われたら、返す　OK！
                } elseif ($message['text'] == 'create Rich Menu') {
                    //__construct　は、newした時に実行されるので、これが正解？

                    $rmDetail = new getRichMenu($channelAccessToken, $channelSecret, $client);
                    $res = $rmDetail->creater($storeId);

                    $imres=json_decode($res, true);

                    if ($res==false || $res== null ||$res== 'undefine' || isset($res['message'])) {
                        $flag='false';
                    } elseif (!isset($imres['message'])) {
                        $flag='true';
                        //  $imres['message']='true';
                    }

                    //$ss = new getRichMenu($channelAccessToken, $channelSecret);


    //    $mId = $ss->createRichMenu();

    //      $imres['richMenuId']

                    $client->replyMessage([
'replyToken' => $event['replyToken'],
'messages' => [
[
'type' => 'text',
'text' =>$storeId . '　OK!'
],

[
'type' => 'text',
'text' => $flag . ' is richmenuID'   . $res
]
]
]);
                } elseif ($message['text'] == '会員ステータス確認') {
                    //TODO:確認
                    $mm = new getMember($channelAccessToken, $channelSecret, $client);
                    $uid=$us['userId'];
                    $res=$mm->index($uid, $storeId);

                    $client->replyMessage([
                        'replyToken' => $event['replyToken'],
                        'messages' => [
                            [
'type' => 'text',
'text' => '　OK!'
                            ],

                            [
'type' => 'text',
'text' =>  $res . '　です！'
                            ]
                        ]
                    ]);
                } elseif ($message['text'] == '会員登録する') {
                    $mm = new getMember($channelAccessToken, $channelSecret, $client);
                    $uid=$us['userId'];
                    $mm->addMember($uid, $event);
                } elseif ($message['text'] == '退会する') {
                    $mm = new getMember($channelAccessToken, $channelSecret, $client);
                    $uid=$us['userId'];
                    $mm->removeMember($uid, $event);


                //TODO:idで受け渡しする
                } elseif ($us['type']=='web' && $message['text']=='change_df_rich_menu' && isset($message['text2'])) {
                    //   $new = RichMenu::where('id',$message['text2'])->where('store_id',$storeId)->get();

                    $new = RichMenu::where('richmenu_id', $message['text2'])->where('store_id', $storeId)->first();
                    $res= $client->defaultRm($new->richmenu_id);
                    $old = RichMenu::where('is_default', 1)->where('store_id', $storeId)->first();
                    if (isset($old)) {
                        $old->is_default=0;
                        $old->save();
                    }

                    $new->is_default=1;
                    $new->save();

                    return $res;

                //TODO:クーポンの配信など調査
                } elseif ($us['type']=='web' || $message['text']=='push!') {
                    $webMsg= $message['text'];
                    $webMsg2= $message['text2'];
                    $uid=$us['userId'];
                    $msg = new SendPushMessage($channelAccessToken, $channelSecret, $client, $webMsg, $webMsg2, $uid);
                    $res = $msg->sendPushMessage();

                    $client->replyMessage([
                        'replyToken' => $event['replyToken'],
                        'messages' => [
                            [
'type' => 'text',
'text' => '　OK!'
                            ],

                            [
'type' => 'text',
'text' => ' is  '   . $res
                            ]
                        ]
                    ]);

                    
                } elseif ($message['text'] == 'audience') {
                    $us = new getAudience($channelAccessToken, $channelSecret, $client);
                   $res= $us->createAud();

 $client->replyMessage([
                        'replyToken' => $event['replyToken'],
                        'messages' => [
                            [
                                'type' => 'text',
                                'text' => "　OK!\n"
                            ],

                            [
                                'type' => 'text',
                                'text' => 'plz create !'. $res
                            ]

                        ]
                    ]);



                //限定メニューを要求されたとき
                } elseif ($message['text'] == '限定メニュー') {
                    $param =new getOrgMenuParam();
                    $sId =$storeId;
                    $param ->getParam($sId, $client, $event);


                //ブロードキャスト送信する。
                //送信したMsgのRequestIdをDBに格納し、IDを返却する
                } elseif ($message['text'] == 'ブロキャス') {
                    $param = new sendNarrow($channelAccessToken, $channelSecret, $client);
                    $msgId = $param->sendMessage();
                //     $params = new getAnalysisData($client,$event);
                    // $params->getData($requestId);

                //    $rs=json_decode($res,true);


                    $client->replyMessage([
                        'replyToken' => $event['replyToken'],
                        'messages' => [
                            [
                                'type' => 'text',
                                'text' => "　OK!\nThis　Message ID　is"
                            ],

                            [
                                'type' => 'text',
                                'text' => 'ID:' . $msgId->id
                            ]

                        ]
                    ]);


                //  $param =new getOrgMenuParam();
                // $sId =$storeId;
                // $param ->getParam($sId, $client, $event);
                } elseif ($message['text'] == '分析') {
                    $param = new getAnalysisData($client, $event);
                    $param->getData();

                //DB参照
                } elseif ($message['text'] == 'READ') {
                    $us = $event['source'];
                    $use=$us['userId'];

                    $uP= new getUserProf();
                    $uP->getProf($use, $client, $event);

                //richメニュー画像
                } elseif ($message['text'] == '画像') {
                    $param =new getOrgMenuParam();
                    $sId =$storeId;
                    $param ->getParam($sId, $client, $event);


                //送信したMsgのIDを、ID:　という形で入力してもらい、IDからRequestIdを持ってきて分析に回す
                } elseif (preg_match('/ID:/', $message['text'])) {
                    $rqMsgId = $message['text'];
                    $param = new getAnalysisData($client, $event);
                    $param->getData($rqMsgId);


                // $client->replyMessage([
                    //     'replyToken' => $event['replyToken'],
                    //     'messages' => [
                    //         [
                    //             'type' => 'text',
                    //             'text' => 'true'
                    //         ],
                    //         [
                    //             'type' => 'text',
                    //             'text' => $qr
                    //         ]
                    //     ]
                    //         ]);


                //ここから
                } elseif ($message['type']=='text') {
                    $client->replyMessage([
                                    'replyToken' => $event['replyToken'],
                                    'messages' => [
                                        [
                                            'type' => 'text',
                                            'text' => $message['text']
                                        ],
                                        [
                                            'type' => 'text',
                                            'text' => 'まねしないで！'
                                        ]
                                    ]
                                        ]);
                } else {
                    error_log('Unsupported message type: ' . $message['type']);
                    break;
                }
            } else {
                error_log('Unsupported event type: ' . $event['type']);
                break;
            }
        }
    }
}
