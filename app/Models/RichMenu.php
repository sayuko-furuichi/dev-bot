<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RichMenu extends Model
{
    use HasFactory;

    protected $fillable  = ['id','richmenu_id','name','chat_bar','img','richMenuAliasId','is_default'];
}
