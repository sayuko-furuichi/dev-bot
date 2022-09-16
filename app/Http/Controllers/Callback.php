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
use App\Models\Store;

class Callback extends Controller
{
    public function callback(Request $request)
    {
    //     //本店

       $store =Store::where('id',$request->store_id)->first();
       if(isset($store)){

       $storeId=$request->store_id;
       $channelAccessToken=$store->cat;
       $channelSecret =$store->cs;
    }
           //DB共有できたら生き返らせる
        // $pr = UserProf::where('id',$request->store_id)->first();
            
        // $channelAccessToken= $pr->channel_access_token;
        // $channelSecret=$pr->channel_secret;


        $send = new SendMessage();
        $send->send($channelAccessToken, $channelSecret, $storeId);
    }

}
