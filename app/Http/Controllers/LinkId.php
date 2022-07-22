<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LinkId extends Controller
{
    public function index(Request $request)
    {

         $obj=json_decode($request);
         
         $userId= $obj -> events->source->userId;
         $reply= $obj -> events->replyToken;

      /*  $channelSecret = '0b0aadd7b81ec25d7d861c28846e4048'; // Channel secret string
        $httpRequestBody = '...'; // Request body string
        $hash = hash_hmac('sha256', $httpRequestBody, $channelSecret, true);
        $signature = base64_encode($hash);
*/

        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient('/4Ejv8i8d4NB1+KSUMMXZA7zEGoCpcBQgIbEng9HYYgcOL1xPcgolcwDSXKbOlRxHvUUhmocgJDvFQrqH7IfpMkxYBt1O2NcU0wSU8bPIIDI9Rpe2VQCHa7ngQp57ptBA7oEAkNxdkZTweVAR0RF1QdB04t89/1O/w1cDnyilFU=');
        $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => '0b0aadd7b81ec25d7d861c28846e4048']);

        $response = $bot->replyText($reply, 'hello!');
        return $response ;


    }
}
