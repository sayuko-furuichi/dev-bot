<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\CatchEvents;
use App\Models\LineStoreStatus;
use App\Http\Controllers\SLINEBotTiny;

class Callback extends Controller
{
    public function callback(Request $request)
    {
    $store =LineStoreStatus::where('store_id', $request->store_id)->first();
    if (isset($store)) {
        $storeId=$request->store_id;
        $channelAccessToken=$store->channel_access_token;
        $channelSecret =$store->channel_secret;
    }

    $lineBot = new SLINEBotTiny($channelAccessToken, $channelSecret);
        $catch = new CatchEvents($storeId,$lineBot);
      
        $catch->send();
    

}

}
