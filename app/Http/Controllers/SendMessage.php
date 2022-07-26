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
use App\Http\Service\getEnisRm;
use App\Http\Service\sendNarrow;
use App\Http\Service\getAnalysisData;
use  App\Http\Service\SendPushMessage;
use  App\Http\Service\getMember;
use  App\Http\Service\getAudience;
use App\Http\Service\getCommonsRm;
use App\Http\Service\getTransition;
use App\Http\Service\Messages;



//あとで消す
use App\Models\RichMenu;
use App\Models\Store;

class SendMessage extends Controller
{
    //postbackアクションについて
    //ユーザが、メッセージを送信せずにデータのみ送信できる機能。
    //botは、if($pt['data']＝action=***)　などで判定したらよい。

    public function send($channelAccessToken, $channelSecret, $storeId, $request)
    {
        $client = new LINEBotTiny($channelAccessToken, $channelSecret);
        foreach ($client->parseEvents() as $event) {
            $us = $event['source'];


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

                } elseif (preg_match('/changed=/', $pt['data'])) {
                    $mm = new getMember($channelAccessToken, $channelSecret, $client);
                    $uid=$us['userId'];
                    $res=$mm->changeMenu($uid, $storeId);
                    if ($res !=null || $res !='') {
                        $client->replyMessage([
                            'replyToken' => $event['replyToken'],
                            'messages' => [
                                [
        'type' => 'text',
        'text' => "会員登録後にご利用頂けます"
                                ],
                            ]
                        ]);
                    }

                //経路の入力を受け付ける
                } elseif (preg_match('/transition=/', $pt['data'])) {
                    $tra = new getTransition($channelAccessToken, $channelSecret, $client);

                    $tra->insertData($us['userId'], $pt['data'], $event);
                }
            }
            //eventtypeがmessageで、messagetypeがtextの時起動

            //友達登録画面
            if ($event['type'] == 'follow') {
                //すでに入力していた場合は受け付けない
                if ($storeId ==54) {
                    $tra = new getTransition($channelAccessToken, $channelSecret, $client);
                    $tra->sendTemplate($event, $us['userId'], $storeId);
                }
            }
            //ブロック時
            if ($event['type'] == 'unfollow') {
                //TODO:ブロック時の記録
            }

            if ($event['type'] == 'message') {
                $message = $event['message'];
                //"ID"と入力されたら、ユーザIDを返す

                // if ($message['text'] == 'ID') {
                //     //ユーザID取得のために、event配列からsoureを代入
                //     //　$us['userId']　でユーザIDを持ってこれる。

                //     $use=$us['userId'];

                //     $client->replyMessage([
                //             'replyToken' => $event['replyToken'],
                //             'messages' => [
                //                 [
                //     'type' => 'text',
                //     'text' => 'This is ' . $storeId . '号店'
                //                 ],
                //                 [
                //     'type' => 'text',
                //     'text' =>  'あなたのユーザID：'.$us['userId']
                //                 ]

                //             ]
                //         ]);
                if ($message['text'] == '申し込み' && $storeId==54) {
                    //ユーザID取得のために、event配列からsoureを代入
                    //　$us['userId']　でユーザIDを持ってこれる。
                    // header("Location:https://dev-ext-app.herokuapp.com/public/addMember?user=".$us['userId']);


                    $client->replyMessage([
                            'replyToken' => $event['replyToken'],
                            'messages' => [
                                [
                    'type' => 'text',
                    'text' => 'こちらからどうぞ'
                                ],
                                [
                    'type' => 'text',
                    'text' =>  'https://dev-ext-app.herokuapp.com/public/addMember?user='.$us['userId']
                                ]

                            ]
                        ]);
                } elseif ($message['text'] == '完了' && $storeId==54 && $us['type']=='web') {
                    // $us['useId'];
                    $member = new getMember($channelAccessToken, $channelSecret, $client);
                    $member->createCMember($us['userId'], $storeId);
                    $store= Store::where('id', 54)->first();
                    $client->linkUser($message['text2'], $store->member_menu);
                    $msg = new SendPushMessage($channelAccessToken, $channelSecret, $client, '登録', 'ありがとうございます！', $message['text2']);
                    $msg->sendPushMessage();
                } elseif ($message['text'] == '予約確認') {
                    $store = Store::where('id', $storeId)->first();
                    $msg = new Messages($channelAccessToken, $channelSecret, $client, $event['replyToken']);
                    $msg->reserveConf($store);

                 
                } elseif ($message['text'] == 'オフラインを購入') {
                    if ($storeId ==54) {
                        $msg = new Messages($channelAccessToken, $channelSecret, $client, $event['replyToken']);
                        $msg->sendMessage();
                    }



                // メニュー　と言われたら、返す　OK！
                } elseif ($message['text'] == 'create Rich Menu') {
                    //__construct　は、newした時に実行されるので、これが正解？

                    if ($storeId ==54) {
                        $rmDetail = new getCommonsRm($channelAccessToken, $channelSecret, $client);
                        $res = $rmDetail->creater($storeId);
                    } else {
                        $rmDetail = new getRichMenu($channelAccessToken, $channelSecret, $client);
                        $res = $rmDetail->creater($storeId);
                    }



                    // $imres=json_decode($res, true);

                    // if ($res==false || $res== null ||$res== 'undefine' || isset($res['message'])) {
                    //     $flag='false';
                    // } elseif (!isset($imres['message'])) {
                    //     $flag='true';
                    //     //  $imres['message']='true';
                    // }

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
'text' =>  ' is richmenuID'   . $res
]
]
]);
                } elseif ($message['text'] == 'create Enis Menu') {
                    $rmDetail = new getEnisRm($channelAccessToken, $channelSecret, $client);
                    $res = $rmDetail->creater($storeId);

                    //   $imres=json_decode($res, true);

                    // if ($res==false || $res== null ||$res== 'undefine' || isset($res['message'])) {
                    //     $flag='false';
                    // } elseif (!isset($imres['message'])) {
                    //     $flag='true';
                    //     //  $imres['message']='true';
                    // }

                    $client->replyMessage([
'replyToken' => $event['replyToken'],
'messages' => [
[
'type' => 'text',
'text' =>$storeId . '　OK!'
],

[
'type' => 'text',
'text' =>  ' is richmenuID'   . $res
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
                    $mm->addMember($uid, $event, $storeId);
                } elseif ($message['text'] == '退会する') {
                    $mm = new getMember($channelAccessToken, $channelSecret, $client);
                    $uid=$us['userId'];
                    $mm->removeMember($uid, $event, $storeId);


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
                    if (isset($message['text2'])) {
                        $webMsg2= $message['text2'];
                    } else {
                        $webMsg2='プッシュメッセージ';
                    }

                    $uid=$us['userId'];
                    $msg = new SendPushMessage($channelAccessToken, $channelSecret, $client, $webMsg, $webMsg2, $uid);
                    $msg->sendPushMessage();
                } elseif ($message['text'] == 'audience') {
                    $us = new getAudience($channelAccessToken, $channelSecret, $client);
                    $res= $us->createAud($storeId);

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
                // } elseif ($message['text'] == '限定メニュー') {
                //     $param =new getOrgMenuParam();
                //     $sId =$storeId;
                //     $param ->getParam($sId, $client, $event);


                //ブロードキャスト送信する。
                //送信したMsgのRequestIdをDBに格納し、IDを返却する
                } elseif ($message['text'] == 'ブロキャス') {
                    $param = new sendNarrow($channelAccessToken, $channelSecret, $client);
                    $msgId = $param->sendMessage();
                //     $params = new getAnalysisData($client,$event);
                // $params->getData($requestId);

                //    $rs=json_decode($res,true);
                } elseif ($message['text'] == '利用状況') {
                    $resq= $client->getQuota();
                    $resq=json_decode($resq, true);

                    $ress= $client->getSent();
                    $ress=json_decode($ress, true);


                    $client->replyMessage([
                        'replyToken' => $event['replyToken'],
                        'messages' => [
                            [
                                'type' => 'text',
                                'text' => "　当月の送信数：".$ress['totalUsage']
                            ],

                            [
                                'type' => 'text',
                                'text' => '当月の送信上限目安：'.$resq['value']
                            ],
                            [
                                'type' => 'text',
                                'text' => '当月の送信可能数： 約'. $resq['value'] - $ress['totalUsage']
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
                                            'text' => "メッセージありがとうございます\n大変申し訳ありませんが、こちらのアカウントでは個別に返信を行うことができません。"
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
