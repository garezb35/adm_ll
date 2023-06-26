<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BSGameCategory extends Model
{
    use HasFactory;

    protected $table = 'sphinx.bs_gm_category';

    public $timestamps = false;

    public function gamelist()
    {
        return $this->hasMany(BSGameContent::Class, 'category_id');
    }

}
