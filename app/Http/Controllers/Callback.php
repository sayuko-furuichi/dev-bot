<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Foundation\helpers;
use LINE\LINEBot\Event\Parser;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\MessageBuilder;


use App\Http\Controllers\SendMessage;
use App\Http\Service\getUserProf;

class Callback extends Controller
{
    public function callback(Request $request)
    {
    //     //本店
    //     if ($request->store_id == '1') {
    //         $channelAccessToken = '/4Ejv8i8d4NB1+KSUMMXZA7zEGoCpcBQgIbEng9HYYgcOL1xPcgolcwDSXKbOlRxHvUUhmocgJDvFQrqH7IfpMkxYBt1O2NcU0wSU8bPIIDI9Rpe2VQCHa7ngQp57ptBA7oEAkNxdkZTweVAR0RF1QdB04t89/1O/w1cDnyilFU=';
    //         $channelSecret = '0b0aadd7b81ec25d7d861c28846e4048';
         


    //    //2号店
    //     } elseif ($request->store_id == '2') {
    //         $channelAccessToken ='8jCwB7uJHNCjdiXhcUmlpFheWXIAUB2mnScBaSBvcSPp209NiJ9c/dTdKv3EF0+ufDJFR7ZBZ3MhcRe7RfFO6iGo5DugZXsO+Hdw7hht2JfYP/m/HgOvWl5FDkrrAKKZUddiWqaBY5rSytRV7q/POQdB04t89/1O/w1cDnyilFU=';
    //         $channelSecret ='b1361c02edc2e4fe84b7bef6c3bddf9d';
    //         $storeId=$request->store_id;

    //     //3号店
    //     } elseif ($request->store_id == '3') {
    //         $channelAccessToken ='paq5m/NvcbF96Rk5UUJoQlbLAS7FD5LRYMqzEWM+Ov8q1mb87WZWSd53enK+feHdV+pHfh6o3in2KI/cY4tEWmCJ7VOCuBHBSNkveeGFULhHWdLswizYiafmhee9yRgATphoOA1ZUY5F53R6/S4VigdB04t89/1O/w1cDnyilFU=';
    //         $channelSecret ='0bedd5514772fb1d4160813f870386d4';
    //         $storeId=$request->store_id;
    //     }

        $storeId=$request->store_id;
        $pr = UserProf::where('id',$storeId)->first();
        
        $channelAccessToken= $pr->channel_access_token;
        $channelSecret=$pr->channel_secret;

        $send = new SendMessage();
        $send->send($channelAccessToken, $channelSecret, $storeId);
    }

}
