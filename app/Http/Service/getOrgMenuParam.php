<?php 

namespace App\Http\Service;
use Illuminate\Support\Facades\Storage;
class getOrgMenuParam{

  private $imgUrl;
  private $imgUrl2;
  private $title;
  private $text;

public function getParam($storeId, $client,$event){

   if($storeId == '1'){
    $this->imgUrl ='https://dev-bot0722.herokuapp.com/storage/app/public/img/cake1.jpg';
    $this->imgUrl2 ='https://dev-bot0722.herokuapp.com/storage/app/public/img/cafe1.jpg';
    $this->title='Demo本店限定！ティラミス';
    $this->text='コーヒーに合うよ';

    }elseif($storeId == '2'){
      $this->imgUrl ='https://dev-bot0722.herokuapp.com/storage/app/public/img/cake1.jpg';
      $this->imgUrl2 ='https://dev-bot0722.herokuapp.com/storage/app/public/img/cafe1.jpg';
      $this->title='Demo2号店限定！ティラミス';
      $this->text='';

    }elseif($storeId == '3'){
      $this->imgUrl ='https://dev-bot0722.herokuapp.com/storage/app/public/img/cake1.jpg';
      $this->imgUrl2 ='https://dev-bot0722.herokuapp.com/storage/app/public/img/cafe1.jpg';
      $this->title='Demo3号店店限定！ティラミス';
      $this->text='';
  
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
          'thumbnailImageUrl'=> $imgUrl ,
          'imageBackgroundColor'=> '#FFFFFF',
          'title'=> $title,
          'text'=> $text,
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

