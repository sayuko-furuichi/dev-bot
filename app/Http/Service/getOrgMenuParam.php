<?php 

namespace App\Http\Service;
use Illuminate\Support\Facades\Storage;
class getOrgMenuParam{


public function getParam($storeId, $client,$event){

  //  if($storeId == '1'){

    //}

    $client->replyMessage([
        'replyToken' => $event['replyToken'],
        'messages' => [
       
            [
                'type' => 'template',
                'altText' =>  'message',
                'template'=> [
                    'type'=> 'image_carousel',
                    'columns'=> [
                      [
                        "imageUrl"=> Storage::get('public\img\cake1.jpg'),
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

