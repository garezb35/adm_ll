<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BSGameCategoryLog extends Model
{
    use HasFactory;

    protected $table = 'sphinx.bs_gm_category_log';

    public function userinfo()
    {
        return $this->belongsTo(User::class, 'adminid');
    }

    public function gameinfo()
    {
        return $this->belongsTo(BSGameContent::class, 'content_id', 'playid');
    }

//    public function cateinfo()
//    {
//        return $this->belongsTo(BSGameCategory::class, 'category_id');
//    }
}
