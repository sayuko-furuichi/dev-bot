<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

/*
 * This polyfill of hash_equals() is a modified edition of https://github.com/indigophp/hash-compat/tree/43a19f42093a0cd2d11874dff9d891027fc42214
 *
 * Copyright (c) 2015 Indigo Development Team
 * Released under the MIT license
 * https://github.com/indigophp/hash-compat/blob/43a19f42093a0cd2d11874dff9d891027fc42214/LICENSE
 */
if (!function_exists('hash_equals')) {
    defined('USE_MB_STRING') or define('USE_MB_STRING', function_exists('mb_strlen'));

    /**
     * @param string $knownString
     * @param string $userString
     * @return bool
     */

    //ハッシュの検証
    function hash_equals($knownString, $userString)
    {
        $strlen = function ($string) {
            if (USE_MB_STRING) {
                return mb_strlen($string, '8bit');
            }

            return strlen($string);
        };

        // Compare string lengths
        if (($length = $strlen($knownString)) !== $strlen($userString)) {
            return false;
        }

        $diff = 0;

        // Calculate differences
        for ($i = 0; $i < $length; $i++) {
            $diff |= ord($knownString[$i]) ^ ord($userString[$i]);
        }
        return $diff === 0;
    }
}
class LINEBotTiny
{
    /** @var string */
    private $channelAccessToken;
    /** @var string */
    private $channelSecret;

    /**
     * @param string $channelAccessToken
     * @param string $channelSecret
     */
    public function __construct($channelAccessToken, $channelSecret)
    {
        $this->channelAccessToken = $channelAccessToken;
        $this->channelSecret = $channelSecret;
    }

    /**
     * @return mixed
     */

    //  署名の検証・POSTされたデータから、events抽出
    public function parseEvents()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            error_log('Method not allowed');
            exit();
        }

        //requestBodyを格納
        $entityBody = file_get_contents('php://input');

        //requestBodyの中身が入っているか
        if ($entityBody === false || strlen($entityBody) === 0) {
            http_response_code(400);
            error_log('Missing request body');
            exit();
        }
        if (isset($_SERVER['HTTP_X_LINE_SIGNATURE']) && !hash_equals($this->sign($entityBody), $_SERVER['HTTP_X_LINE_SIGNATURE'])) {
            //ハッシュ値が署名と一致するかどうか

            http_response_code(400);
            error_log('Invalid signature value');
            exit();
        //lineの署名が無い場合、独自の署名が一致するか
        } elseif (isset($_SERVER['x_demo_signature']) && !hash_equals($this->sign($entityBody), $_SERVER['x_demo_signature'])) {
            http_response_code(400);
            error_log('Invalid signature value');

            exit();
        }
        //他サイトから操作するための処理

        //requestBodyに、Eventオブジェクトが含まれているかどうか
        $data = json_decode($entityBody, true);
        if (!isset($data['events'])) {
            http_response_code(400);
            error_log('Invalid request body: missing events property');
            exit();
        }
        return $data['events'];
    }


    /**
     * @param array<string, mixed> $message
     * @return void
     */

     //メッセージ送信処理
    public function replyMessage($message)
    {
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->channelAccessToken,
        );

        $context = stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'method' => 'POST',
                'header' => implode("\r\n", $header),
                'content' => json_encode($message),
            ],
        ]);

        $response = file_get_contents('https://api.line.me/v2/bot/message/reply', false, $context);
        if (strpos($http_response_header[0], '200') === false) {
            error_log('Request failed: ' . $response);
        }
    }

        //create　richmenu
    public function rtRichMenu($rmDetail)
    {
        $rmheader = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->channelAccessToken,
        );

        $rmcontext = stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'method' => 'POST',
                'header' => implode("\r\n", $rmheader),
                'content' => json_encode($rmDetail, true)
            ],
        ]);

        $rmresponse = file_get_contents('https://api.line.me/v2/bot/richmenu', false, $rmcontext);
        if (strpos($http_response_header[0], '200') === false) {
            //  $rmresponse = 'false';
        }
        return $rmresponse;
    }


    public function validateRm($rmDetail)
    {
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->channelAccessToken,
        );

        $context = stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'method' => 'POST',
                'header' => implode("\r\n", $header),
                'content' => json_encode($rmDetail),
            ],
        ]);

        $response = file_get_contents('https://api.line.me/v2/bot/richmenu/validate', false, $context);
        if (strpos($http_response_header[0], '200') === false) {
            error_log('Request failed: ' . $response);
        }
        return $response;
    }


    //TODO:ファイル名変数にしたら１つで行けそう
    //リッチメニューに画像添付
    public function upRmImgA($rmId, $imgUrl)
    {
        // $richmenuId="richmenu-b56771c2cf5b359b8c182d7de6f9e2c8";

        //画像URL
        //TODO:会員メニューに変更すること
        $img = file_get_contents($imgUrl);
        $imgheader = array(
            'Content-Type: image/png',
            'Authorization: Bearer ' . $this->channelAccessToken,
        //    "Content-Length: ".strlen($img),
        );


        $imgcontext = stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'method' => 'POST',
                'header' => implode("\r\n", $imgheader),
               'content' => $img
            ],
        ]);

        $imgresponse = file_get_contents('https://api-data.line.me/v2/bot/richmenu/'.$rmId.'/content', false, $imgcontext);
        if (strpos($http_response_header[0], '200') === false) {
            $imgresponse= 'Request failed: ';
        } else {
            $imgresponse= 'OK';
        }

        return $imgresponse;
    }



//非会員メニューをデフォルトで表示
    public function defaultRm($rmId)
    {
        //デフォルト解除しておく
        // $this->dltDefaultRm();

        $api_url ='https://api.line.me/v2/bot/user/all/richmenu/'. $rmId;

        //エンコードされたURLでPOST通信する
        $headers = [ 'Authorization: Bearer ' . $this->channelAccessToken,];

        $curl_handle = curl_init();

        curl_setopt($curl_handle, CURLOPT_POST, true);
        curl_setopt($curl_handle, CURLOPT_URL, $api_url);
        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $headers);
        // curl_exec()の結果を文字列にする
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        //実行
        $res = curl_exec($curl_handle);

        //close
        curl_close($curl_handle);

        //デコード
        //  $res = json_decode($json_response, true);
        return $res;
    }

        //デフォルト解除

        public function dltDefaultRm()
        {
            $dfheader = array(
                'Authorization: Bearer ' . $this->channelAccessToken,
            );
            $dfcontext = stream_context_create([
                'http' => [
                    'ignore_errors' => true,
                    'method' => 'DELETE',
                    'header' => $dfheader,
                  // 'content' => $imgurl,
                ],
            ]);

            file_get_contents('https://api.line.me/v2/bot/user/all/richmenu', false, $dfcontext);

            $dheader = array(
                'Authorization: Bearer ' . $this->channelAccessToken,
            );
            $dcontext = stream_context_create([
                'http' => [
                    'ignore_errors' => true,
                    'method' => 'DELETE',
                    'header' => $dheader,
                  // 'content' => $imgurl,
                ],
            ]);

            file_get_contents('https://api.line.me/v2/bot/user/U8e837a180eae5c7896bc0e5b1a3aa55f/richmenu', false, $dcontext);


            // if (strpos($http_response_header[0], '200') === false) {
            //     $dfresponse= 'Request failed';
            //   }else{
            //     $dfresponse= 'OK';
            //    }
        }



        //エイリアスを作成
    public function createAlias($param)
    {
        //

        $alheader = array(
            'Authorization: Bearer ' . $this->channelAccessToken,
            'Content-Type: application/json',
        );

        $alcontext = stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'method' => 'POST',
                'header' => implode("\r\n", $alheader),
               'content' => json_encode($param)
            ],
        ]);

        $alresponse = file_get_contents('https://api.line.me/v2/bot/richmenu/alias', false, $alcontext);
        if (strpos($http_response_header[0], '200') === false) {
            $alresponse= 'Request failed';
        }

        return $alresponse;
    }

public function linkUser($uid, $rm)
{
    $api_url ='https://api.line.me/v2/bot/user/'. $uid . '/richmenu/' . $rm;

    //エンコードされたURLでPOST通信する
    $headers = [ 'Authorization: Bearer ' . $this->channelAccessToken,];

    $curl_handle = curl_init();

    curl_setopt($curl_handle, CURLOPT_POST, true);
    curl_setopt($curl_handle, CURLOPT_URL, $api_url);
    curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $headers);
    // curl_exec()の結果を文字列にする
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
    //実行
    $res = curl_exec($curl_handle);

    //close
    curl_close($curl_handle);

    return $res;
}

//リッチメニューとユーザのリンクを切る（非会員メニューに切り替える）
public function deleteLinkUser($userId)
{
    $header = array(
        'Authorization: Bearer ' . $this->channelAccessToken,
);

    $context = stream_context_create([
        'http' => [
            'ignore_errors' => true,
            'method' => 'DELETE',
           'header' => implode("\r\n", $header),
        ],
    ]);

    $response = file_get_contents('https://api.line.me/v2/bot/user/'. $userId . '/richmenu/', false, $context);
    if (strpos($http_response_header[0], '200') === false) {
        $response='request failed';
    }

    return $response;
}




public function sendPush($param)
{
    $header = array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $this->channelAccessToken,
    );
    $context = stream_context_create([
        'http' => [
            'ignore_errors' => true,
            'method' => 'POST',
            'header' => $header,
            // JSON_UNESCAPED_UNICODE？
           'content' => json_encode($param),
        ],
    ]);

    $res=file_get_contents('https://api.line.me/v2/bot/message/push', false, $context);
    if (strpos($http_response_header[0], '200') === false) {
           $res='request failed';
    }

    return $res;
}



        //署名をハッシュ化
    /**
     * @param string $body
     * @return string
     */
    private function sign($body)
    {
        $hash = hash_hmac('sha256', $body, $this->channelSecret, true);
        $signature = base64_encode($hash);
        return $signature;
    }
}
