<?php

namespace App\Http\Controllers;

use App\Models\PayCharge;
use App\Models\PayChargeLog;
use App\Models\User;
use App\Models\UserListProc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;


class ChargeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function getInfo(Request $request)
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

        $tempQuery = null;
        if ($request->ptype != '') {
            if ($request->ptype == 'pending') {
                $nVerified = 1;
                $data['page_type'] = '대기';
            } else if ($request->ptype == 'ready') {
                $nVerified = 0;
                $data['page_type'] = '요청';
            }
            $tempQuery = PayCharge::where('verified', $nVerified);
        } else {
            $tempQuery = PayCharge::where('created_at', '>=', $objData[0] . ' 00:00:00')
                ->where('created_at', '<=', $objData[1] . ' 23:59:59');
            if ($data['state'] != '')
                $tempQuery->where('verified', $data['state']);
            if ($data['req_type'] == 6) {
                $tempQuery->whereIn('group_type', array(0))
                    ->where('store', 1);
            } else if ($data['req_type'] != '') {
                $tempQuery->where('group_type', $data['req_type']);
//                if ($data['req_type'] == 0 || $data['req_type'] == 2)
                if ($data['req_type'] == 0)
                    $tempQuery->where('store', '!=', 1);
            }
            if ($data['username'] != '') {
                $searchUserInfo = User::where('userid', $data['username'])
                    ->first();
                if (!empty($searchUserInfo))
                    $tempQuery->where('userid', $searchUserInfo->id);
                else
                    $tempQuery->where('userid', 0);
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
        $chargeRecord = $tempRecord->paginate(25);
        $nSumCharge = 0;
        foreach ($tempRecord as $record) {
            if ($record->verified == 2)
                $nSumCharge += $record->money;
        }
        $data['nSumCharge'] = $nSumCharge;
        $data['chargeRecord'] = $chargeRecord;
        return $data;
    }

    public function list(Request $request)
    {
        $data = $this->getInfo($request);
        return view('charge.list', $data);
    }

    public function searchList(Request $request)
    {
        $data = $this->getInfo($request);
        $data['ptype'] = $request->ptype;
        return view('charge.ready', $data);
    }

    public function saveMemo(Request $request)
    {
        PayCharge::where('id', $request->id)
            ->update(['memo' => $request->memo, 'updated_at' => DB::raw('updated_at')]);

        return json_encode(array('stat' => 'ok'));
    }

    public function setReadyProc(Request $request)
    {
        $chargeinfo = PayCharge::where('id', $request->id)
            ->where('verified', 0)
            ->first();
        if (!empty($chargeinfo)) {
            if (PayCharge::where('id', $request->id)
                    ->where('verified', 0)
                    ->update(['verified' => 1, 'updated_at' => date("Y-m-d H:i:s")]) > 0) {
                PayChargeLog::insert(['userid' => $chargeinfo->userid,
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
        $user = auth()->user();
        $reqinfo = PayCharge::where('id', $request->id)
            ->where('verified', '<', 2)
            ->first();
        if (!empty($reqinfo)) {
            $userinfo = UserListProc::where('userno', $reqinfo->userid)
                ->first();
            if (!empty($userinfo)) {
                if (config('app.no_mini') == 1) {
                    $target = User::where('id', $reqinfo->userid)
                        ->first();
                    $nResMoney = $target->procinfo->bpoint + $target->procinfo->money;
                    $bs = new BSGameController;
                    if (!$bs->procCharge($target, $reqinfo->money)) {
                        // 충전실패되면 보유에 적립시킨다
                        UserListProc::where('userno', $reqinfo->userid)
                            ->update(['money' => DB::raw("money + $reqinfo->money")]);
                    }
                    PayCharge::where('id', $request->id)
                        ->where('verified', '<', 2)
                        ->update(['verified' => 2,
                            'updated_at' => date("Y-m-d H:i:s"),
                            'user_money' => $nResMoney + $reqinfo->money,
                            'updateBy' => $user->id,
                        ]);
                    PayChargeLog::insert(['userid' => $request->id,
                        'adminid' => $user->id,
                        'chargeid' => $request->id,
                        'ip_addr' => Session::get('ip_addr'),
                        'money' => $reqinfo->money,
                        'type' => '처리'
                    ]);
                } else {
                    DB::beginTransaction();
                    try {
                        $nResMoney = $userinfo->money + $reqinfo->money;
                        if (PayCharge::where('id', $request->id)
                                ->where('verified', '<', 2)
                                ->update(['verified' => 2,
                                    'updated_at' => date("Y-m-d H:i:s"),
                                    'user_money' => $nResMoney,
                                    'updateBy' => $user->id,
                                ]) > 0) {
                            UserListProc::where('userno', $userinfo->userno)
                                ->update(['money' => DB::raw('money + ' . $reqinfo->money)]);
                            PayChargeLog::insert(['userid' => $userinfo->userno,
                                'adminid' => $user->id,
                                'chargeid' => $request->id,
                                'ip_addr' => Session::get('ip_addr'),
                                'money' => $reqinfo->money,
                                'type' => '처리'
                            ]);
                        }
                        DB::commit();
                    } catch (\Exception $ex) {
                        DB::rollback();
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
        $chargeinfo = PayCharge::where('id', $request->id)->first();
        if (!empty($chargeinfo)) {
            if (PayCharge::where('id', $request->id)
                    ->where('verified', '!=', 2)
                    ->update(['verified' => 4,
                        'updated_at' => date("Y-m-d H:i:s")
                    ]) > 0) {
                PayChargeLog::insert(['userid' => $chargeinfo->userid,
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
        $chargeinfo = PayCharge::where('id', $request->id)->first();
        if (!empty($chargeinfo)) {
            PayCharge::where('id', $request->id)
                ->where('verified', '!=', 2)
                ->delete();

            PayChargeLog::insert(['userid' => $chargeinfo->userid,
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
