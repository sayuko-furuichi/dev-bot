<?php
namespace App\Http\Service;

use App\Models\UserProf;




class getUserProf{

public function getProf($use,$client, $event){

    //Userのアクセストークンの取得
    $data =UserProf::where('line_user_id', $use) ->first();
    $results= json_decode(json_encode($data));




    $client->replyMessage([
        'replyToken' => $event['replyToken'],
        'messages' => [
            [
'type' => 'text',
'text' => 'This is ' . $storeId . '号店'
            ],
            [
'type' => 'text',
'text' =>  $results
            ]

        ]
    ]);
}


}