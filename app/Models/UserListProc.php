<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserListProc extends Model
{
    use HasFactory;

    public $table = 'sphinx.user_list_proc';

    public $timestamps = false;
}
