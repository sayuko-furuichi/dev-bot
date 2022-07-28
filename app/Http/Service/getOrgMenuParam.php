<?php 

namespace App\Http\Service;
use Illuminate\Support\Facades\Storage;
class getOrgMenuParam{


public function getParam($storeId, $client,$event){

  //  if($storeId == '1'){

    //}
    $storeId==null;

    $client->replyMessage([
        'replyToken' => $event['replyToken'],
        'messages' => [
//             [
// 'type' => 'text',
// 'text' => 'ないよ！' . $storeId
//             ],
//             [
// 'type' => 'text',
// 'text' =>  '作業中...'
//             ],
         
//             // ここからしたが合ってない
//             [
//                 'type' => 'template',
//                 'altText' =>  'message',
//                 'template'=> [
//                     'type'=> 'image_carousel',
//                     'columns'=> [
//                       [
//                         "imageUrl"=>'https://dev-bot0722.herokuapp.com/storage/app/public/img/cake1.jpg',
//                         'action'=> [
//                             [
//                               'type'=> 'message',
//                               'label'=> 'いいね！',
//                               'text'=> '美味しそう！'
//                             ]
                           

//                             ],
         
//     ]]

//                 ]]

//                            ] ]);
    
// }
// }


[
    'type'=> 'template',
    'altText'=> 'this is a carousel template',
    'template'=> [
      'type'=> 'carousel',
      'columns'=> [
        [
          'thumbnailImageUrl'=> 'https=>//example.com/bot/images/item1.jpg',
          'imageBackgroundColor'=> '#FFFFFF',
          'title'=> 'this is menu',
          'text'=> 'description',
          'defaultAction'=> [
            'type'=> 'uri',
            'label'=> 'View detail',
            'uri'=> 'http://example.com/page/123'
          ],
          'actions'=> [
            [
              'type'=> 'postback',
              'label'=> 'Buy',
              'data'=> 'action=buy&itemid=111'
            ],
            [
              'type'=> 'postback',
              'label'=> 'Add to cart',
              'data'=> 'action=add&itemid=111'
            ],
            [
              'type'=> 'uri',
              'label'=> 'View detail',
              'uri'=> 'http://example.com/page/111'
            ]
          ]
        ],
        [
          'thumbnailImageUrl'=> 'https://example.com/bot/images/item2.jpg',
          'imageBackgroundColor'=> '#000000',
          'title'=> 'this is menu',
          'text'=> 'description',
          'defaultAction'=> [
            'type'=> 'uri',
            'label'=> 'View detail',
            'uri'=> 'http://example.com/page/222'
          ],
          'actions'=> [
            [
              'type'=> 'postback',
              'label'=> 'Buy',
              'data'=> 'action=buy&itemid=222'
            ],
            [
              'type'=> 'postback',
              'label'=> 'Add to cart',
              'data'=> 'action=add&itemid=222'
            ],
            [
              'type'=> 'uri',
              'label'=> 'View detail',
              'uri'=> 'http://example.com/page/222'
            ]
          ]
        ]
      ],
      'imageAspectRatio'=> 'rectangle',
      'imageSize'=> 'cover'
    ]
  ]]]);

}

}

