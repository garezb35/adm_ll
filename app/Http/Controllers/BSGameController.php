<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Lib\BossController;
use App\Http\Controllers\Lib\KtenController;
use App\Http\Controllers\Lib\AgaController;
use App\Http\Controllers\Lib\PlusController;
use App\Http\Controllers\Lib\PPKController;
use App\Http\Controllers\Lib\SGController;
use App\Http\Controllers\Lib\XimaxController;
use App\Models\UserGameLog;
use App\Models\UserListProc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BSGameController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getBalance($live, $user)
    {
        $result = array();
        $sumKeep = 0;
        if ($live == 1) {
            $result['casino']['sum'] = $sumKeep;
        } else {
            $result['slot']['sum'] = $sumKeep;
        }
        return $result;
    }

    public function deposit($live, $user, $amount)
    {
        if ($live == 1) {
            if ($amount <= 0)
                return false;
            return UserListProc::where('userno', $user->id)
                ->where('money', '>=', $amount)
                ->update([
                    'money' => DB::raw("money - $amount"),
                    'cx_point' => DB::raw("cx_point + $amount")
                ]);
        }
    }

    public function withdrawal($live, $user, $amount)
    {
        $proc = false;
        if ($live == 1) {
            if (!$proc) {
                if ($amount <= 0)
                    return false;
                return UserListProc::where('userno', $user->id)
                    ->where('cx_point', '>=', $amount)
                    ->update([
                        'money' => DB::raw("money + $amount"),
                        'cx_point' => DB::raw("cx_point - $amount")
                    ]);
            }
        }

    }

    public function procExcharge($user, $amount)
    {
        if ($user->procinfo->cx_wallet == '')
            return true;
        return UserListProc::where('userno', $user->id)
            ->where('cx_point', '>=', $amount)
            ->update([
                'money' => DB::raw('money + ' . $amount),
                'cx_point' => DB::raw('cx_point - ' . $amount)
            ]);
        return $isProc;
    }

    public function procCharge($user, $amount)
    {
        if ($user->procinfo->cx_wallet == '') {
            UserListProc::where('userno', $user->id)
                ->update(['money' => DB::raw('money + ' . $amount)]);
            return true;
        }
        return UserListProc::where('userno', $user->id)
            ->update(['cx_point' => DB::raw('cx_point + ' . $amount)]);
    }
}
