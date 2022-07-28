<?php 

namespace App\Http\Service;
use Illuminate\Support\Facades\Storage;
class getOrgMenuParam{

  // private String $imgUrl;
  // private String $imgUrl2;
  // private String $title;
  // private String $text;

public function getParam($sId, $client,$event){



  if($sId == '1'){
    $imgUrl ='https://dev-bot0722.herokuapp.com/storage/app/public/img/cake1.jpg';
    $imgUrl2 ='https://dev-bot0722.herokuapp.com/storage/app/public/img/cafe1.jpg';
    $title='Demo本店限定！ティラミス';
    $text='コーヒーに合うよ';

    }elseif($sId == '2'){
      $imgUrl ='https://dev-bot0722.herokuapp.com/storage/app/public/img/cake1.jpg';
      $imgUrl2 ='https://dev-bot0722.herokuapp.com/storage/app/public/img/cafe1.jpg';
      $title='Demo2号店限定！ティラミス';
      $text='おいしいよ';

    }elseif($sId == '3'){
      $imgUrl ='https://dev-bot0722.herokuapp.com/storage/app/public/img/cake1.jpg';
      $imgUrl2 ='https://dev-bot0722.herokuapp.com/storage/app/public/img/cafe1.jpg';
      $title='Demo3号店店限定！ティラミス';
      $text='にゃー－－';
  
    }

    $client->replyMessage([
        'replyToken' => $event['replyToken'],
        'messages' => [

[
    'type'=> 'template',
    'altText'=> 'this is a carousel template',
    'template'=> [
      'type'=> 'carousel',
      'columns'=> [
        [
          'thumbnailImageUrl'=> $imgUrl,
          'imageBackgroundColor'=> '#FFFFFF',
          'title'=>  $title,
          'text'=>  $text,
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
          'thumbnailImageUrl'=>$imgUrl2,
          'imageBackgroundColor'=> '#000000',
          'title'=> $title,
          'text'=>  $text,
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

