<?php 

namespace App\Http\Service;
class getOrgMenuParam{


public function getParam($storeId){

    $client->replyMessage([
        'replyToken' => $event['replyToken'],
        'messages' => [
            [
'type' => 'text',
'text' => 'OK!'
            ],
            [
'type' => 'text',
'text' =>  $mnParam
            ]

        ]
    ]);

    
}  





}