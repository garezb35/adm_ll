<?php

namespace App\Http\Controllers;

use App\Models\UserListProc;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\PayExchargeLog;
use App\Models\PayExcharge;
use App\Models\User;
use Illuminate\Http\Request;

class ExchargeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getInfo(Request $request)
    {
        $objTotalDate = $request->start_date ?? date('Y-m-d') . ' to ' . date('Y-m-d');
        $objData = getDateFromRangeDate($objTotalDate);
        $data = array(
            'start_date' => $objTotalDate,
            'state' => $request->state ?? '',
            'req_type' => $request->req_type ?? '',
            'username' => $request->username ?? '',
            'type' => $request->type ?? 'req',
            'sort' => $request->sort ?? 'asc'
        );

        if ($request->ptype != '') {
            if ($request->ptype == 'pending') {
                $nVerified = 1;
                $data['page_type'] = '대기';
            } else if ($request->ptype == 'ready') {
                $nVerified = 0;
                $data['page_type'] = '요청';
            }
            $tempQuery = PayExcharge::where('verified', $nVerified);
        } else {
            $tempQuery = PayExcharge::where('created_at', '>=', $objData[0] . ' 00:00:00')
                ->where('created_at', '<=', $objData[1] . ' 23:59:59');
            if ($data['state'] != '')
                $tempQuery->where('verified', $data['state']);
            if ($data['req_type'] == 4)
                $tempQuery->whereIn('group_type', [0])
                    ->where('store', 1);
            else if ($data['req_type'] != '') {
                $tempQuery->where('group_type', $data['req_type']);
                // if ($data['req_type'] == 0 || $data['req_type'] == 2)
                if ($data['req_type'] == 0)
                    $tempQuery->where('store', '!=', 1);
            }
            if ($data['username'] != '') {
                $searchUserInfo = User::where('userid', $data['username'])
                    ->first();
                $tempQuery->where('userid', $searchUserInfo->id ?? 0);
            }
        }

        $snzSortType = "";
        $snzSortArrow = $data['sort'] == 'asc' ? 'desc' : 'asc';
        switch ($data['type']) {
            case 'money':
                $snzSortType = 'money';
                break;
            case 'res':
                $snzSortType = 'user_money';
                break;
            case 'req':
                $snzSortType = 'created_at';
                break;
            case 'proc':
                $snzSortType = 'updated_at';
                break;
        }
        $tempQuery->orderBy($snzSortType, $snzSortArrow);
        $tempRecord = $tempQuery->get();
        $exchargeRecord = $tempRecord->paginate(25);
        $nSumMoney = 0;
        foreach ($tempRecord as $record) {
            if ($record->verified == 2)
                $nSumMoney += $record->money;
        }
        $data['chargeRecord'] = $exchargeRecord;
        $data['nSumMoney'] = $nSumMoney;
        return $data;
    }

    public function list(Request $request)
    {
        return view('excharge.list', $this->getInfo($request));
    }

    public function searchList(Request $request)
    {
        $data = $this->getInfo($request);
        $data['ptype'] = $request->ptype;
        return view('excharge.ready', $data);
    }

    public function saveMemo(Request $request)
    {
        PayExcharge::where('id', $request->id)
            ->update(['memo' => $request->memo, 'updated_at' => DB::raw('updated_at')]);

        return json_encode(array('stat' => 'ok'));
    }

    public function setReadyProc(Request $request)
    {
        $chargeinfo = PayExcharge::where('id', $request->id)
            ->where('verified', 0)
            ->first();
        if (!empty($chargeinfo)) {
            if (PayExcharge::where('id', $request->id)
                    ->where('verified', 0)
                    ->update(['verified' => 1, 'updated_at' => date("Y-m-d H:i:s")]) > 0) {
                PayExchargeLog::insert(['userid' => $chargeinfo->userid,
                    'adminid' => auth()->user()->id,
                    'chargeid' => $request->id,
                    'ip_addr' => Session::get('ip_addr'),
                    'money' => $chargeinfo->money,
                    'type' => '대기'
                ]);
            }
        }
        return Redirect::back();
    }

    public function setDoneProc(Request $request)
    {
        $reqinfo = PayExcharge::where('id', $request->id)
            ->where('verified', '<', 2)
            ->first();
        $user = auth()->user();
        if (!empty($reqinfo)) {
            $target = User::where('id', $reqinfo->userid)
                ->first();
            if (!empty($target)) {
                if (config('app.no_mini') == 1) {
                    $nResMoney = $target->procinfo->bpoint + $target->procinfo->money;
                    $bs = new BSGameController;
                    $nAmount = $reqinfo->money;
                    Log::info(sprintf('%d:회원 %d:요청 %d:보유금액', $reqinfo->userid, $nAmount, $target->procinfo->money));
                    // 환전요청하면 게임머니가 money로 전환된다
                    /*if ($nAmount > $target->procinfo->money) {
                        $balance = $nAmount - $target->procinfo->money;
                        Log::info($balance);
                        $bs->procExcharge($target, $balance);
                    }*/

                    if (UserListProc::where('userno', $target->id)
                            ->where('ex_money', '>=', $nAmount)
                            ->update(['ex_money' => DB::raw('ex_money - ' . $nAmount)]) > 0) {
                        PayExcharge::where('id', $request->id)
                            ->where('verified', '<', 2)
                            ->update(['verified' => 2,
                                'updated_at' => date("Y-m-d H:i:s"),
                                'user_money' => $nResMoney,
                                'updateBy' => $user->id,
                            ]);
                        PayExchargeLog::insert(['userid' => $target->id,
                            'adminid' => $user->id,
                            'chargeid' => $request->id,
                            'ip_addr' => Session::get('ip_addr'),
                            'money' => $reqinfo->money,
                            'type' => '처리'
                        ]);
                    }
                    /*else {
                        PayExcharge::where('id', $request->id)
                            ->where('verified', '<', 2)
                            ->update(['verified' => 4,
                                'updated_at' => date("Y-m-d H:i:s"),
                                'user_money' => $nResMoney,
                                'updateBy' => $user->id,
                            ]);
                        PayExchargeLog::insert(['userid' => $target->id,
                            'adminid' => $user->id,
                            'chargeid' => $request->id,
                            'ip_addr' => Session::get('ip_addr'),
                            'money' => $reqinfo->money,
                            'type' => '취소'
                        ]);
                    }*/
                } else {
                    $nResMoney = $target->procinfo->money;
                    if (PayExcharge::where('id', $request->id)
                            ->where('verified', '<', 2)
                            ->update(['verified' => 2,
                                'updated_at' => date("Y-m-d H:i:s"),
                                'user_money' => $nResMoney,
                                'updateBy' => $user->id,
                            ]) > 0) {
                        PayExchargeLog::insert(['userid' => $target->id,
                            'adminid' => $user->id,
                            'chargeid' => $request->id,
                            'ip_addr' => Session::get('ip_addr'),
                            'money' => $reqinfo->money,
                            'type' => '처리'
                        ]);
                    }
                }
            }
        }
        return Redirect::back();
    }

    /**
     * 취소요청
     * @param Request $request
     * @return mixed
     */
    public function setDeleteProc(Request $request)
    {
        $chargeinfo = PayExcharge::where('id', $request->id)->first();
        if (!empty($chargeinfo)) {
            if (PayExcharge::where('id', $request->id)
                    ->where('verified', '!=', 2)
                    ->where('verified', '!=', 4)
                    ->update(['verified' => 4,
                        'updated_at' => date("Y-m-d H:i:s")
                    ]) > 0) {
                if (config('app.no_mini') == 0) {
                    UserListProc::where('userno', $chargeinfo->userid)
                        ->update(['money' => DB::raw('money + ' . $chargeinfo->money)]);
                } else {
                    UserListProc::where('userno', $chargeinfo->userid)
                        ->where('ex_money', '>=', $chargeinfo->money)
                        ->update([
                            'ex_money' => DB::raw("ex_money - $chargeinfo->money"),
                            'money' => DB::raw('money + ' . $chargeinfo->money),
                        ]);
                }
                PayExchargeLog::insert(['userid' => $chargeinfo->userid,
                    'adminid' => auth()->user()->id,
                    'chargeid' => $request->id,
                    'ip_addr' => Session::get('ip_addr'),
                    'money' => $chargeinfo->money,
                    'type' => '취소'
                ]);
            }
        }
        return Redirect::back();
    }

    /**
     * 삭제요청
     * @param Request $request
     * @return mixed
     */
    public function setHideProc(Request $request)
    {
        $chargeinfo = PayExcharge::where('id', $request->id)
            ->where('verified', 4)
            ->first();
        if (!empty($chargeinfo)) {
            PayExcharge::where('id', $request->id)
                ->where('verified', 4)
                ->delete();

            PayExchargeLog::insert(['userid' => $chargeinfo->userid,
                'adminid' => auth()->user()->id,
                'chargeid' => $request->id,
                'ip_addr' => Session::get('ip_addr'),
                'money' => $chargeinfo->money,
                'type' => '삭제'
            ]);
        }

        return Redirect::back();
    }
}
