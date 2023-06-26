<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BSPushCtl extends Model
{
    use HasFactory;

    protected $table = 'sphinx.bs_ps_ctl';

    public function gameInfo()
    {
        return $this->belongsTo(BSGameCategory::class, 'thirdParty', 'thirdPartyCode');
    }

    public function userInfo()
    {
        return $this->belongsTo(User::class, 'userid');
    }

}
