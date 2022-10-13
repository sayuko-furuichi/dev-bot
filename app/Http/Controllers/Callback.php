<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Controllers\CommonsCatchEvents;
use App\Models\LineStoreStatus;

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
    //DB共有できたら生き返らせる
    // $pr = UserProf::where('id',$request->store_id)->first();

    // $channelAccessToken= $pr->channel_access_token;
    // $channelSecret=$pr->channel_secret;

    //厳密な比較でフラグと間違われないようにする
    if ($storeId===1) {
        $catch = new CommonsCatchEvents($channelAccessToken, $channelSecret, $storeId);
        $catch->send();
    // } else {
    //     $sends = new CatchEvents();
    //     $sends->send($channelAccessToken, $channelSecret, $storeId);
    }
}

}
