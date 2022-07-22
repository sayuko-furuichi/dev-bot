<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LinkId extends Controller
{
    public function index()
    {
        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient('/4Ejv8i8d4NB1+KSUMMXZA7zEGoCpcBQgIbEng9HYYgcOL1xPcgolcwDSXKbOlRxHvUUhmocgJDvFQrqH7IfpMkxYBt1O2NcU0wSU8bPIIDI9Rpe2VQCHa7ngQp57ptBA7oEAkNxdkZTweVAR0RF1QdB04t89/1O/w1cDnyilFU=');
        $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => '0b0aadd7b81ec25d7d861c28846e4048']);

        $response = $bot->replyText('<reply token>', 'hello!');
        return $response ;
    }
}
