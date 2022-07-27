<?php

use App\Models\UserProf;



class getUserProf{

public function getProf($userId){

    //Userのアクセストークンの取得
    $data =UserProf::where('line_user_id',$userId) ->first();
    return $data;


}


}