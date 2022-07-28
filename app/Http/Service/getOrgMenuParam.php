<?php 

namespace App\Http\Service;
class getOrgMenuParam{


public function getParam($storeId, $client,$event){

    $client->replyMessage([
        'replyToken' => $event['replyToken'],
        'messages' => [
            [
'type' => 'text',
'text' => 'OK!'
            ],
            [
'type' => 'text',
'text' =>  $storeId
            ]

        ]
    ]);

    
}  





}