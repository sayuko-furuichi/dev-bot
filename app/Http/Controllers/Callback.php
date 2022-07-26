<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot;

class Callback extends Controller
{
    //
    public function index(Request $request){

        $bot = app('line-bot');
        // LINE シグネチャのチェック
        $signature = $_SERVER['HTTP_' . LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];
        if (!LINEBot\SignatureValidator::validateSignature($request->getContent(), config('line-bot.channel_secret'), $signature)) {
            abort(400);
        }
        $reply_token = $event->getReplyToken();
    
        // ここに自動応答処理を書く
        $bot->replyText($reply_token, 'Hello');
    
        // JSONでステータスコード=200のレスポンスを返す
        response()->json(['return-data' => 'data'], 200);
     //   return $bot;
        return $bot;
    }




}
