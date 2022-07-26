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
        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient('/4Ejv8i8d4NB1+KSUMMXZA7zEGoCpcBQgIbEng9HYYgcOL1xPcgolcwDSXKbOlRxHvUUhmocgJDvFQrqH7IfpMkxYBt1O2NcU0wSU8bPIIDI9Rpe2VQCHa7ngQp57ptBA7oEAkNxdkZTweVAR0RF1QdB04t89/1O/w1cDnyilFU=');
        $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => '0b0aadd7b81ec25d7d861c28846e4048']);

    
        $dData =json_decode($_POST, true);

        var_dump($dData);

        $reply_token = $dData["replyToken"];
    
        // ここに自動応答処理を書く
        
        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('hello');
        
        $response=$bot->replyText($reply_token, $textMessageBuilder);
    
        // JSONでステータスコード=200のレスポンスを返す

        echo $response->getHTTPStatus() . ' ' . $response->getRawBody();
     
        //  return $response;
    }
}
