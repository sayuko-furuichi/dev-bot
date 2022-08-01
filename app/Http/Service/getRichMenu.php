<?php
namespace App\Http\Service;

use LINE\LINEBot;
use App\Http\Controllers\LINEBotTiny;

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
    private LINEBotTiny $client;
    
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
        //debug
      // $res=$this->client->rtRichMenu([
        $res=$this->client->validateRm([
            'size'=>[
                'width'=>2500,
                'height'=>1686
            ],
            'selected'=> true,
            'name'=> 'LINE Developers Info',
            'chatBarText'=> 'Tap to open',
            'areas'=> [
                [
                    'bounds'=> [
                        'x'=> 34,
                        'y'=> 24,
                        'width'=> 169,
                        'height'=> 193
                    ],
                    'action'=> [
                        'type'=> 'uri',
                        'uri'=> 'https://developers.line.biz/en/news/'
                    ]
                ],
                [
                    'bounds'=> [
                        'x'=> 229,
                        'y'=> 24,
                        'width'=> 207,
                        'height'=> 193
                    ],
                    'action'=> [
                        'type'=> 'uri',
                        'uri'=> 'https://www.line-community.me/ja/'
                    ]
                ],
                [
                    'bounds'=> [
                        'x'=> 461,
                        'y'=> 24,
                        'width'=> 173,
                        'height'=> 193
                    ],
                    'action'=> [
                        'type'=> 'uri',
                        'uri'=> 'https://engineering.linecorp.com/en/blog/'
                    ]
                ]
            ]
                    ]);

        $resDcd=json_decode($res);
        return $resDcd;
    }
    
    //assertEquals()　とは、PHPUnitのアサーションメソッドで、期待した値と等しいか判定する
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
    /**
     * Undocumented function
     *
     * @return $menuId
     */

    //!! 出来ないお(；ω；)　連想配列で渡して、encodeする書き方にしようかな。
    public function createRichMenu():String
    {
        //testから引用
        //     $bot = new LINEBot(new CurlHttpClient($this->channelAccessToken), ['channelSecret' => $this->channelSecret]);
        //     $res = $bot->createRichMenu(
        //         new RichMenuBuilder(
        //             RichMenuSizeBuilder::getFull(),
        //             true,
        //             'Nice richmenu',
        //             'Tap to open',
        //             [
        //                 new RichMenuAreaBuilder(
        //                     new RichMenuAreaBoundsBuilder(0, 10, 1250, 1676),
        //                     new MessageTemplateActionBuilder('message label', 'test message')
        //                 ),
        //                 new RichMenuAreaBuilder(
        //                     new RichMenuAreaBoundsBuilder(1250, 0, 1240, 1686),
        //                     new MessageTemplateActionBuilder('message label 2', 'test message 2')
        //                 )
        //             ]
        //         )
        //     );
        $httpClient = new CurlHTTPClient($this->channelAccessToken);
        $bot = new LINEBot($httpClient, ['channelSecret' => $this->channelSecret]);

        //RichMenuBuilder() params
        $sizeBuilder= RichMenuSizeBuilder::getFull();
        $selected= true;
        $name='Nice richmenu';
        $chatBarText= 'Tap to open';

        //RichMenuAreaBoundsBuilder() params
        $x=0;
        $y=10;
        $width=1250;
        $height=1676;

        // MessageTemplateActionBuilder params
        $label='message label';
        $text='test message';
        $areaBuilders= [new RichMenuAreaBuilder(

            //RichMenuAreaBoundsBuilder() params =  $x, $y, $width, $height (boundsオブジェクト==タップ領域)
            new RichMenuAreaBoundsBuilder($x, $y, $width, $height),
            // MessageTemplateActionBuilder params = $label, $text (actionオブジェクト==タップされたときの挙動)
            new MessageTemplateActionBuilder($label, $text)
        )];
        $richMenuBuilder = new RichMenuBuilder($sizeBuilder, $selected, $name, $chatBarText, $areaBuilders);
        //LINEBot classの、createRichMenu($richMenuBuilder)　を呼ぶ。渡した後、ちゃんとbild()してるから個々ではbildしなくていい。
        $res = $bot->createRichMenu($richMenuBuilder);

        $dcdRes =jsondecode($res);

        $menuId = $dcdRes['richMenuId'];

        //use debug
        return $menuId;
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
