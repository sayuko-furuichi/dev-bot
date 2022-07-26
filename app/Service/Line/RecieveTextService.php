<?php

use LINE\LINEBot\Event\MessageEvent;

//ADD
use App\Models\UserProf;

/**
 * 登録
 * @param FollowEvent $event
 * @return bool
 * @throws \Illuminate\Database\Eloquent\MassAssignmentException
 */

 //Controllerから回された仕事をこなす。
 //もらった情報から、ほしいのを抜き出してＤＢに登録する。(登録いるかな？)

 class RecieveTextService{
// FollowEvent $event　のおかげで、GETprofile　とが使えるらしい



public function execute(FollowEvent $event)
{
    try {
        DB::beginTransaction();

        $line_id = $event->getUserId();
        $rsp = $this->bot->getProfile($line_id);
        if (!$rsp->isSucceeded()) {
            logger()->info('failed to get profile. skip processing.');
            return false;
        }
        
        //持ってきた値をdecodeで連想配列にして、DBへ格納する
        $profile = $rsp->getJSONDecodedBody();
        $line_friend = new UserProf();
        // 空文字で代入するもの
        $input = [
            'line_user_id' => $line_id,
            'line_user_name' => $profile['displayName'],
            'prof_img_url'=>'',
            'prof_msg'=>'',
            'user_os'=>'',
            'user_trans'=>'',
        ];

        $line_friend->fill($input)->save();
        DB::commit();

        return true;

    } catch (Exception $e) {
        logger()->error($e);
        DB::rollBack();
        return false;
    }
}
}