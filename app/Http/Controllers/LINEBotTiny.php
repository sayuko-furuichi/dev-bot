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

     //署名の検証・POSTされたデータから、events抽出
    public function parseEvents()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            error_log('Method not allowed');
            exit();
        }

        $entityBody = file_get_contents('php://input');

        if ($entityBody === false || strlen($entityBody) === 0) {
            http_response_code(400);
            error_log('Missing request body');
            exit();
        }

        if (!hash_equals($this->sign($entityBody), $_SERVER['HTTP_X_LINE_SIGNATURE'])) {
            http_response_code(400);
            error_log('Invalid signature value');
            exit();
        }

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

        //OK!!!
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
                'content' => json_encode($rmDetail,true)
            ],
        ]);

        $rmresponse = file_get_contents('https://api.line.me/v2/bot/richmenu', false, $rmcontext);
        if (strpos($http_response_header[0], '200') === false) {
            error_log('Request failed: ' . $rmresponse);
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


    //rmImg
    public function upRmImg()
    {
       // $richmenuId="richmenu-b56771c2cf5b359b8c182d7de6f9e2c8";

        $header = array(
            'Content-Type: image/png',
            'Authorization: Bearer ' . $this->channelAccessToken,
        );

        $imgurl='https://dev-bot0722.herokuapp.com/storage/app/public/img/richmenu/3.png';

        $context = stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'method' => 'POST',
                'header' => implode("\r\n", $header),
              //  'content' =>'https://dev-bot0722.herokuapp.com/storage/app/public/img/richmenu/3.png',
            ],
        ]);

        $response = file_get_contents('https://api-data.line.me/v2/bot/richmenu/richmenu-b56771c2cf5b359b8c182d7de6f9e2c8/content', false, $context,$imgurl);
        if (strpos($http_response_header[0], '200') === false) {
            error_log('Request failed: ' . $response);
        }

        return $response;
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
