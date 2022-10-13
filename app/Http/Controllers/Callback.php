<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Foundation\helpers;
use LINE\LINEBot\Event\Parser;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\MessageBuilder;


use App\Http\Controllers\CatchEvents;
use App\Http\Controllers\CommonsCatchEvents;
use App\Http\Service\getUserProf;
use App\Models\Store;
use App\Models\LineStoreStatus;

class Callback extends Controller
{
    public function callback(Request $request)
    {
    //     //本店
    $store =LineStoreStatus::where('store_id', $request->store_id)->first();
    if (isset($store)) {
        $storeId=$request->store_id;
        $channelAccessToken=$store->channel_access_token;
        $channelSecret =$store->channel_secret;
    }
    //DB共有できたら生き返らせる
    // $pr = UserProf::where('id',$request->store_id)->first();

    // $channelAccessToken= $pr->channel_access_token;
    // $channelSecret=$pr->channel_secret;

    if ($storeId===1) {
        $send = new CommonsCatchEvents($channelAccessToken, $channelSecret, $storeId);
        $send->send();
    } else {
        $sends = new CatchEvents();
        $sends->send($channelAccessToken, $channelSecret, $storeId);
    }
}

}
