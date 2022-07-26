<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot;

class Callback extends Controller
{
    //
    public function index(Request $request)
    {
        $channelSecret = '0b0aadd7b81ec25d7d861c28846e4048'; // Channel secret string
        $httpRequestBody = ['destination'=> 'xxxxxxxxxx','events'=>[]]; // Request body string
        $hash = hash_hmac('sha256', $httpRequestBody, $channelSecret, true);
        $signature = base64_encode($hash);

        // Compare x-line-signature request header string and the signature


        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient('/4Ejv8i8d4NB1+KSUMMXZA7zEGoCpcBQgIbEng9HYYgcOL1xPcgolcwDSXKbOlRxHvUUhmocgJDvFQrqH7IfpMkxYBt1O2NcU0wSU8bPIIDI9Rpe2VQCHa7ngQp57ptBA7oEAkNxdkZTweVAR0RF1QdB04t89/1O/w1cDnyilFU=');
        $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => '0b0aadd7b81ec25d7d861c28846e4048']);
        $at= '/4Ejv8i8d4NB1+KSUMMXZA7zEGoCpcBQgIbEng9HYYgcOL1xPcgolcwDSXKbOlRxHvUUhmocgJDvFQrqH7IfpMkxYBt1O2NcU0wSU8bPIIDI9Rpe2VQCHa7ngQp57ptBA7oEAkNxdkZTweVAR0RF1QdB04t89/1O/w1cDnyilFU=';

    
        // ここに自動応答処理を書く
        
        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('hello');
        
        $response=$bot->replyText($reply_token, $textMessageBuilder);
        if ($response-> isSucceeded()) {
            echo  '成功しました！' ;
            return;
        }
    
        // JSONでステータスコード=200のレスポンスを返す

        echo $response->getHTTPStatus() . ' ' . $response->getRawBody();
    }

 
    /**
     * callback from LINE Message API(webhook)
     * @param Request $request
     * @throws \LINE\LINEBot\Exception\InvalidSignatureException
     */
    public function callback(Request $request)
    {

        /** @var LINEBot $bot */
        $bot = app('line-bot');

        $signature = $_SERVER['HTTP_'.LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];
        if (!LINEBot\SignatureValidator::validateSignature($request->getContent(), env('LINE_CHANNEL_SECRET'), $signature)) {
            abort(400);
        }

        $events = $bot->parseEventRequest($request->getContent(), $signature);
        foreach ($events as $event) {
            $reply_token = $event->getReplyToken();
            $reply_message = 'その操作はサポートしてません。.[' . get_class($event) . '][' . $event->getType() . ']';

            switch (true) {
                //友達登録＆ブロック解除
                case $event instanceof LINEBot\Event\FollowEvent:
                    $service = new FollowService($bot);
                    $reply_message = $service->execute($event)
                        ? '友達登録されたからLINE ID引っこ抜いたわー'
                        : '友達登録されたけど、登録処理に失敗したから、何もしないよ';

                    break;
                    //メッセージの受信
                case $event instanceof LINEBot\Event\MessageEvent\TextMessage:
                    $service = new RecieveTextService($bot);
                    $reply_message = $service->execute($event);
                    break;

                    //位置情報の受信
                case $event instanceof LINEBot\Event\MessageEvent\LocationMessage:
                    $service = new RecieveLocationService($bot);
                    $reply_message = $service->execute($event);
                    break;

                    //選択肢とか選んだ時に受信するイベント
                case $event instanceof LINEBot\Event\PostbackEvent:
                    break;
                    //ブロック
                case $event instanceof LINEBot\Event\UnfollowEvent:
                    break;
                default:
                    $body = $event->getEventBody();
                    logger()->warning('Unknown event. ['. get_class($event) . ']', compact('body'));
            }

            $bot->replyText($reply_token, $reply_message);
        }
   
        $bot = app('line-bot');
        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('送信');
        $response = $bot->pushMessage('Uffd4dd52c580e1d2bb7b0a66e0ef1951', $textMessageBuilder);
   
   
   
    }
}
