<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProf extends Model
{
    use HasFactory;
    protected $table = 'user_profs';
    protected $fillable =['line_user_id','line_user_name','prof_img_url','prof_msg','user_os','user_trans'];

}
