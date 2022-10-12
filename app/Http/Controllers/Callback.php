<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Foundation\helpers;
use LINE\LINEBot\Event\Parser;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\MessageBuilder;


use App\Http\Controllers\CatchEvents;
use App\Http\Service\getUserProf;
use App\Models\Store;
use App\Models\LineStoreStatus;

class Callback extends Controller
{
    public function callback(Request $request)
    {
    //     //本店
        return 'HTTP/1.1 200 OK';
       $store =LineStoreStatus::where('store_id',$request->store_id)->first();
       if(isset($store)){

       $storeId=$request->store_id;
       $channelAccessToken=$store->channel_access_token;
       $channelSecret =$store->channel_secret;
    }
           //DB共有できたら生き返らせる
        // $pr = UserProf::where('id',$request->store_id)->first();
            
        // $channelAccessToken= $pr->channel_access_token;
        // $channelSecret=$pr->channel_secret;


        $send = new CatchEvents();
        $send->send($channelAccessToken, $channelSecret, $storeId,$request);
    }

}
