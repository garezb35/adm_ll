<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BSGameContent extends Model
{
    use HasFactory;

    protected $table = 'sphinx.bs_gm_content';

    public $timestamps = false;

    public function cateinfo()
    {
        return $this->belongsTo(BSGameCategory::class, 'category_id');
    }
}
