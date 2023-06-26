<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'sphinx.user_list';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'api_token',
    ];

    public $sortable = ['created_at'];

    public function joininfo()
    {
        return $this->hasOne(User::class, 'id', 'joinid');
    }

    public function limitinfo()
    {
        return $this->hasOne(MiniBetLimit::class, 'userid',  'id');
    }

    public function headinfo()
    {
        return $this->hasOne(User::class, 'id', 'masterid');
    }

    public function procinfo()
    {
        return $this->hasOne(UserListProc::class, 'userno')
            ->selectRaw('*, (fail_point + cx_point) AS bpoint,
                (plus_point) AS spoint');
    }

    public function parent()
    {
        return $this->hasOne(User::class, 'id', 'joinid');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'joinid', 'id');
    }

    public function all_children()
    {
        return $this->children();
    }

    public function adminip()
    {
        return $this->hasMany(SiteAdminIplist::class, 'userid');
    }

    public function rolling()
    {
        return $this->hasOne(RollingMini::class, 'userid');
    }

    public function mnPush()
    {
        return $this->hasOne(MiniGamePush::class, 'userid');
    }

    public function rollingBS()
    {
        return $this->hasOne(RollingBS::class, 'userid');
    }

    public static function rateAll($userid)
    {
        return DB::select(sprintf('EXEC sphinx.rateAll @User=%d', $userid));
    }
}
