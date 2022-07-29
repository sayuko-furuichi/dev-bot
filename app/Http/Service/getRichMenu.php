<?php
namespace App\Http\Service;

use LINE\LINEBot;
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

class getRichMenu{

    private $userId;
    //chanell_access_token
    private $cat;
    //chanell_secret
    private $sc;
    
    public function __construct($userId,$cat,$cs){
        $this->userId= $userId;
        $this->cat= $cat;
        $this->sc= $sc;

    }

    public function create(){

//build()で、作れそう。

    }
    

    // //richメニュー取得
    // public function testGetRichMenu()
    // {
    //     $mock = function ($testRunner, $httpMethod, $url, $data) {
    //         /** @var \PHPUnit\Framework\TestCase $testRunner */
    //         $testRunner->assertEquals('GET', $httpMethod);
    //         $testRunner->assertEquals('https://api.line.me/v2/bot/richmenu/123', $url);
    //         $testRunner->assertEquals([], $data);
    //         return ['status' => 200];
    //     };
    //     $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
    //     $res = $bot->getRichMenu(123);

    //     $this->assertEquals(200, $res->getHTTPStatus());
    //     $this->assertTrue($res->isSucceeded());
    //     $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    // }


    //Richメニュー作成
    public function createRichMenu()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api.line.me/v2/bot/richmenu', $url);

            $testRunner->assertEquals(2500, $data['size']['width']);
            $testRunner->assertEquals(1686, $data['size']['height']);
            $testRunner->assertEquals(true, $data['selected']);
            $testRunner->assertEquals('Nice richmenu', $data['name']);
            $testRunner->assertEquals('Tap to open', $data['chatBarText']);

            $areas = $data['areas'];
            $testRunner->assertEquals(2, count($areas));

            $testRunner->assertEquals(0, $areas[0]['bounds']['x']);
            $testRunner->assertEquals(10, $areas[0]['bounds']['y']);
            $testRunner->assertEquals(1250, $areas[0]['bounds']['width']);
            $testRunner->assertEquals(1676, $areas[0]['bounds']['height']);
            $testRunner->assertEquals(ActionType::MESSAGE, $areas[0]['action']['type']);
            $testRunner->assertEquals('test message', $areas[0]['action']['text']);

            $testRunner->assertEquals(1250, $areas[1]['bounds']['x']);
            $testRunner->assertEquals(0, $areas[1]['bounds']['y']);
            $testRunner->assertEquals(1240, $areas[1]['bounds']['width']);
            $testRunner->assertEquals(1686, $areas[1]['bounds']['height']);
            $testRunner->assertEquals(ActionType::MESSAGE, $areas[1]['action']['type']);
            $testRunner->assertEquals('test message 2', $areas[1]['action']['text']);

            return ['status' => 200];
        };
        $bot = new LINEBot(new HttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->createRichMenu(
            new RichMenuBuilder(
                RichMenuSizeBuilder::getFull(),
                true,
                'Nice richmenu',
                'Tap to open',
                [
                    new RichMenuAreaBuilder(
                        new RichMenuAreaBoundsBuilder(0, 10, 1250, 1676),
                        new MessageTemplateActionBuilder('message label', 'test message')
                    ),
                    new RichMenuAreaBuilder(
                        new RichMenuAreaBoundsBuilder(1250, 0, 1240, 1686),
                        new MessageTemplateActionBuilder('message label 2', 'test message 2')
                    )
                ]
            )
        );

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

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

    public function testUploadRichMenuImage()
    {
        $mock = function ($testRunner, $httpMethod, $url, $data, $headers) {
            /** @var \PHPUnit\Framework\TestCase $testRunner */
            $testRunner->assertEquals('POST', $httpMethod);
            $testRunner->assertEquals('https://api-data.line.me/v2/bot/richmenu/123/content', $url);

            $testRunner->assertEquals(1, count($headers));
            $testRunner->assertEquals('Content-Type: image/png', $headers[0]);

            $testRunner->assertEquals('/path/to/image.png', $data['__file']);
            $testRunner->assertEquals('image/png', $data['__type']);
            return ['status' => 200];
        };
        $bot = new LINEBot(new DummyHttpClient($this, $mock), ['channelSecret' => 'CHANNEL-SECRET']);
        $res = $bot->uploadRichMenuImage(123, '/path/to/image.png', 'image/png');

        $this->assertEquals(200, $res->getHTTPStatus());
        $this->assertTrue($res->isSucceeded());
        $this->assertEquals(200, $res->getJSONDecodedBody()['status']);
    }

}