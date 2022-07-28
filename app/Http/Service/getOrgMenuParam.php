<?php 

namespace App\Http\Service;
class getOrgMenuParam{


public function getParam($storeId, $client,$event){

  //  if($storeId == '1'){

    //}

    $client->replyMessage([
        'replyToken' => $event['replyToken'],
        'messages' => [
       
            [
'type' => 'text',
'text' => 'OK!'
            ],
            [
                'type' => 'templete',
                'altText' =>  'message',
                'template'=> [
                    'type'=> 'image_carousel',
                    'columns'=> [
                      [
                        "imageUrl"=> "img/cake1.jpg",
                        'action'=> [
                            [
                              'type'=> 'message',
                              'label'=> 'いいね！',
                              'text'=> '美味しそう！'
                            ]

                            ]
          
    ]]
]
]]]
);

    
}
}

