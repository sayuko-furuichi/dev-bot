<?php
namespace App\Http\Service;

use LINE\LINEBot;
use App\Http\Controllers\LINEBotTiny;
use Illuminate\Http\Request;
use LINE\LINEBot\Constant\ActionType;
use LINE\LINEBot\RichMenuBuilder;
use LINE\LINEBot\RichMenuBuilder\RichMenuSizeBuilder;
use LINE\LINEBot\RichMenuBuilder\RichMenuAreaBuilder;
use LINE\LINEBot\RichMenuBuilder\RichMenuAreaBoundsBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\HTTPClient;
use PHPUnit\Framework\TestCase;
use App\Models\UserProf;

class getRichMenu
{


    //chanell_access_token
    private $channelAccessToken;
    //chanell_secret
    private $channelSecret;

    //LINEBotTiny client
    private $client;
    
    /**
     * Undocumented __construct
     *
     * @param String $channelAccessToken
     * @param String $channelSecret
     * @param LINEBotTiny $client
     */
    public function __construct(String $channelAccessToken, String $channelSecret, $client)
    {
        // $this->userId= $userId;
        $this->channelAccessToken= $channelAccessToken;
        $this->channelSecret= $channelSecret;
        $this->client=$client;
    }

    public function create()
    {
       // $response=$this->client->validateRm([
        //色んなサイズの物があったらいいかも。
    
        //     $res= $this->client->upRmImg();

        // return $res;


        
        $res= $this->client->defaultRm();

        return $res;
       
        // $res=$this->client->rtRichMenu([
       
        //     'size'=>[
        //         'width'=>2400,
        //         'height'=>1600
        //     ],
        //     'selected'=> false,
        //     'name'=> 'DEV_LINE',
        //     'chatBarText'=> 'Tap to open',
        //     //ここでarray()を使用しないと配列になってくれない。JSONで[]なってるところ。
        //     'areas'=> array([
              
        //             'bounds'=> [
        //                 'x'=> 1250,
        //                 'y'=> 0,
        //                 'width'=> 1250,
        //                 'height'=> 200
        //             ],
        //             'action'=> [
        //                 'type'=> 'uri',
        //                 'uri'=> 'https://developers.line.biz/en/news/'
        //             ],
        //             'bounds'=> [
        //                 'x'=>0,
        //                 'y'=> 200,
        //                 'width'=> 833,
        //                 'height'=> 1486
        //             ],
        //             'action'=> [
        //                 'type'=> 'uri',
        //                 'uri'=> 'https://developers.line.biz/en/news/'
        //             ],
        //               //E 切り替えアクション
        //               'bounds'=> [
        //                 'x'=>0,
        //                 'y'=> 100,
        //                 'width'=> 200,
        //                 'height'=> 100
        //             ],
        //             'action'=> [
        //                 'type'=> 'richmenuswitch',
        //                 //切り替え先設定
        //                 'richMenuAliasId'=> 'alias_dev',
        //                 'data'=> 'richmenu-changed-to-b'
        //             ]

        //             ]),

        //         ]);

                        
        //    return $res;
   


    }
    


    //Richメニュー作成
    /**
     * Undocumented function
     *
     * @return $menuId
     */

  

    //デフォルトのrichメニューを設定
    public function testSetDefaultRichMenuId()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/user/all/richmenu/123', $url);
            $testRunner->assertEquals([], $data);
            return ['status' => 200];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->setDefaultRichMenuId(123);

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

    //Richmenuの画像をアップロード





    //エイリアス作成
    public function testCreateRichMenuAlias()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/richmenu/alias', $url);
            $testRunner->assertEquals([
                'richMenuAliasId' => 'richmenu-alias-a',
                'richMenuId' => 'richmenu-862e6ad6c267d2ddf3f42bc78554f6a4'
            ], $data);

            return [];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->createRichMenuAlias('richmenu-alias-a', 'richmenu-862e6ad6c267d2ddf3f42bc78554f6a4');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
    }
}
