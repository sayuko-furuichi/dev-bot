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
'type' => 'text',
'text' => 'ないよ！' . $storeId
            ],
            [
'type' => 'text',
'text' =>  '作業中...'
            ],
         
            // ここからしたが合ってない
            [
                'type' => 'template',
                'altText' =>  'message',
                'template'=> [
                    'type'=> 'image_carousel',
                    'columns'=> [
                      [
                        "imageUrl"=>'https://dev-bot0722.herokuapp.com/storage/app/public/img/cake1.jpg',
                        'action'=> [
                            [
                              'type'=> 'message',
                              'label'=> 'いいね！',
                              'text'=> '美味しそう！'
                            ]
                           

                            ],
         
    ]]

                ]]

                           ] ]);
    
}
}

