<?php

use App\Models\UserProf;



class getUserProf{

public function getProf($use){

    //Userのアクセストークンの取得
    $data =UserProf::where('line_user_id',$use) ->first();
    
    return $data;


}


}