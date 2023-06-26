<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BSBetListAll extends Model
{
    use HasFactory;

    protected $table = 'sphinx.getBSBetList';

    public function betinfo()
    {
        return $this->belongsTo(BSGameCategory::class, 'thirdParty', 'thirdPartyCode');
    }

    public function c_betinfo()
    {
        return $this->belongsTo(BSGameCategory::class, 'thirdParty', 'thirdPartyCode')->where('is_live', 1);
    }

    public function s_betinfo()
    {
        return $this->belongsTo(BSGameCategory::class, 'thirdParty', 'thirdPartyCode')->where('is_live', 0);
    }

    public function userInfo()
    {
        return $this->belongsTo(User::class, 'userno');
    }

    public function pushInfo()
    {
        return $this->belongsTo(BSGamePush::class, 'userno', 'userid');
    }
}
