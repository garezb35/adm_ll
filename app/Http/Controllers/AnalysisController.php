<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserListProc;
use App\Models\PayRecord;
use Illuminate\Support\Facades\Log;

class AnalysisController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dailyList(Request $request)
    {
        $today = date('Y-m-d');
        $option = $request->option ?? 1;
        $sub = $request->sub ?? 2;
        $search = $request->search;
        $start = $request->start ?? $today;
        $end = $request->end ?? $today;

        $data = array(
            'option' => $option,
            'sub' => $sub,
            'search' => $search,
            'start' => $start,
            'end' => $end,
            'dateRecord' => array()
        );

        if ($search != '') {
            $column = $option == 1 ? 'userid' : 'nickname';
            $parent = User::where($column, $search)
                ->where('rolecode', '0003')
                ->first();
            if (empty($parent)) {
                return view('casino.daily', $data);
            }
            $arrSubUserId = array($parent->id);
            if ($sub == 2) {
                getSubUserId($parent, $arrSubUserId);
            }
        } else {
            $userRecord = User::where('rolecode', '0003')
                ->get();
            $arrSubUserId = array_column($userRecord->toArray(), 'id');
        }
        $dateRecord = DB::select(sprintf("EXEC %s.dateRange @Start='%s 00:00:00', @End='%s 23:59:59'",
            config('global.prefix'), $start, $end));
        if ($search != '') {
            $snzUserIds = implode(',', $arrSubUserId);
            $betConn = sprintf('AND userno IN (%s)', $snzUserIds);
            $feeConn = sprintf('AND proc_userid IN (%s)', $snzUserIds);
            $chgConn = sprintf('AND userid IN (%s)', $snzUserIds);
        }
        $groupType = '0,2';
        $chgRecord = DB::select(sprintf("SELECT CONVERT(date, created_at) as daily, SUM(money) AS charge
            FROM sphinx.pay_charge WHERE verified = 2 AND group_type IN (%s) AND created_at >= '%s 00:00:00' AND created_at <= '%s 23:59:59' %s GROUP BY CONVERT(date, created_at)",
            $groupType, $start, $end, $chgConn ?? ''));
        $exgRecord = DB::select(sprintf("SELECT CONVERT(date, created_at) as daily, SUM(money) AS excharge
            FROM sphinx.pay_exchange WHERE verified = 2 AND group_type IN (%s) AND created_at >= '%s 00:00:00' AND created_at <= '%s 23:59:59' %s GROUP BY CONVERT(date, created_at)",
            $groupType, $start, $end, $chgConn ?? ''));
        if (config('app.api_type') == 'ximax2') {
            $betRecord = DB::select(sprintf("SELECT CONVERT(date, transTime) as daily,
                SUM(CASE WHEN is_live = 1 AND thirdParty != 1000 THEN amount ELSE 0 END) AS sumBBet, SUM(CASE WHEN is_live = 1 AND thirdParty != 1000 THEN amount2 ELSE 0 END) AS sumBWin,
                SUM(CASE WHEN is_live = 1 AND thirdParty = 1000 THEN amount ELSE 0 END) AS sumBBet2, SUM(CASE WHEN is_live = 1 AND thirdParty = 1000 THEN amount2 ELSE 0 END) AS sumBWin2,
                SUM(CASE WHEN is_live = 0 THEN amount ELSE 0 END) AS sumSBet, SUM(CASE WHEN is_live = 0 THEN amount2 ELSE 0 END) AS sumSWin
                FROM sphinx.getBSBetList WHERE transTime >= '%s 00:00:00' AND transTime <= '%s 23:59:59' %s GROUP BY CONVERT(date, transTime)", $start, $end, $betConn ?? ''));
        } else {
            $betRecord = DB::select(sprintf("SELECT CONVERT(date, transTime) as daily,
                SUM(CASE WHEN is_live = 1 THEN amount ELSE 0 END) AS sumBBet, SUM(CASE WHEN is_live = 1 THEN amount2 ELSE 0 END) AS sumBWin,
                SUM(CASE WHEN is_live = 0 THEN amount ELSE 0 END) AS sumSBet, SUM(CASE WHEN is_live = 0 THEN amount2 ELSE 0 END) AS sumSWin
                FROM sphinx.getBSBetList WHERE transTime >= '%s 00:00:00' AND transTime <= '%s 23:59:59' %s GROUP BY CONVERT(date, transTime)", $start, $end, $betConn ?? ''));
        }
        $feeRecord = DB::select(sprintf("SELECT CONVERT(date, transTime) as daily,
            SUM(CASE WHEN is_live = 1 THEN fee_amount ELSE 0 END) AS sumBFee,
            SUM(CASE WHEN is_live = 0 THEN fee_amount ELSE 0 END) AS sumSFee
            FROM sphinx.getBSBetFee WHERE transTime >= '%s 00:00:00' AND transTime <= '%s 23:59:59' AND status = 1 %s GROUP BY CONVERT(date, transTime)", $start, $end, $feeConn ?? ''));

        $tempRecord = array();
        foreach ($dateRecord as $record) {
            $temp = array(
                'dt' => $record->dt,
                'charge' => 0,
                'excharge' => 0,
                'bb_money' => 0,
                'bb_money2' => 0,
                'sb_money' => 0,
                'bw_money' => 0,
                'bw_money2' => 0,
                'sw_money' => 0,
                'bf_amount' => 0,
                'sf_amount' => 0
            );

            $index = array_search($record->dt, array_column($chgRecord, 'daily'));
            if ($index !== false) {
                $temp['charge'] = $chgRecord[$index]->charge;
            }
            $index = array_search($record->dt, array_column($exgRecord, 'daily'));
            if ($index !== false) {
                $temp['excharge'] = $exgRecord[$index]->excharge;
            }
            $index = array_search($record->dt, array_column($betRecord, 'daily'));
            if ($index !== false) {
                $temp['bb_money'] = $betRecord[$index]->sumBBet;
                $temp['bb_money2'] = $betRecord[$index]->sumBBet2 ?? 0;
                $temp['sb_money'] = $betRecord[$index]->sumSBet;
                $temp['bw_money'] = $betRecord[$index]->sumBWin;
                $temp['bw_money2'] = $betRecord[$index]->sumBWin2 ?? 0;
                $temp['sw_money'] = $betRecord[$index]->sumSWin;
            }
            $index = array_search($record->dt, array_column($feeRecord, 'daily'));
            if ($index !== false) {
                $temp['bf_amount'] = $feeRecord[$index]->sumBFee;
                $temp['sf_amount'] = $feeRecord[$index]->sumSFee;
            }
            $tempRecord[] = (object)$temp;
        }
        $data['dateRecord'] = $tempRecord;
        return view('casino.daily', $data);
    }

    public function stageList(Request $request)
    {
        $userRecord = User::where('masterid', 0)
            ->where('rolecode', '0003')
            ->get();
        $start = $request->start ?? date('Y-m-1') . ' to ' . date('Y-m-d');
        $gameType = $request->gameType ?? 'all';
        $searchUser = $request->searchUser ?? '';
        $data = array(
            'userRecord' => $userRecord,
            'gameType' => $gameType,
            'start' => $start,
            'searchUser' => $searchUser,
            'is_sub' => $request->is_sub ?? 1,
        );
        return view('casino.stageList', $data);
    }

    public function partnerTree(Request $request)
    {
        $userRecord = User::where('masterid', 0)
            ->where('rolecode', '0003')
            ->get();
        return view('casino.levelTree', ['userRecord' => $userRecord]);
    }

    public function partnerTable(Request $request)
    {
        $start = $request->start ?? date('Y-m-01');
        $end = $request->end ?? date('Y-m-d');
        $gameType = $request->gameType ?? 'all';
        $searchUser = $request->userid ?? '';
        $is_sub = $request->is_sub ?? true;
        try {
            if ($searchUser != '') {
                $parent = User::where('userid', $searchUser)
                    ->where('rolecode', '0003')
                    ->first();
                $arrSubUserId = array($parent->id);
                if ($is_sub)
                    getSubUserId($parent, $arrSubUserId);
                $connBSBet = sprintf("AND userno IN (SELECT Item FROM dbo.SplitStringToInt('%s', ','))", implode(',', $arrSubUserId));
                $connFee = sprintf("AND proc_userid IN (SELECT Item FROM dbo.SplitStringToInt('%s', ','))", implode(',', $arrSubUserId));
                $connMNBet = sprintf("AND userid IN (SELECT Item FROM dbo.SplitStringToInt('%s', ','))", implode(',', $arrSubUserId));
                $connFee2 = sprintf("AND fee_userid IN (SELECT Item FROM dbo.SplitStringToInt('%s', ','))", implode(',', $arrSubUserId));
            } else {
                $userRecord = User::where('rolecode', '0003')
                    ->get();
                $arrSubUserId = array_column($userRecord->toArray(), 'id');
            }
            $snzSubUserId = implode(',', $arrSubUserId);
            if (config('app.omg_ver') == 1) {
                $dateRecord = DB::select(sprintf("EXEC %s.levelGameBetMonth2 @Start='%s 00:00:00', @End='%s 23:59:59', @User='%s'",
                    config('global.prefix'), $start, $end, $snzSubUserId));
            } else {
                $dateRecord = DB::select(sprintf("EXEC %s.levelGameBetMonth @Start='%s 00:00:00', @End='%s 23:59:59', @User='%s'",
                    config('global.prefix'), $start, $end, $snzSubUserId));
            }

            switch ($gameType) {
                case 'live':
                    $connBSLive = 'AND is_live = 1';
                    break;
                case 'slot':
                    $connBSLive = 'AND is_live = 0';
                    break;
                default:
                    break;
            }
            if ($request->gameItem != '') {
                if ($gameType == 'live' || $gameType == 'slot') {
                    $connGItem = 'AND thirdParty = ' . $request->gameItem;
                }
                if ($gameType == 'pwb') {
                    $connGItem = sprintf("AND game_code = '%s'", $request->gameItem);
                }
            }
            $betRecord = $feeRecord = $betRecord2 = $feeRecord2 = array();
            if ($gameType == 'all' || $gameType != 'pwb') {
                $betRecord = DB::select(sprintf("SELECT CONVERT(date, transTime) as daily, SUM(amount) AS sumBet, SUM(amount2) AS sumWin
                    FROM sphinx.getBSBetList WHERE transTime >= '%s 00:00:00' AND transTime <= '%s 23:59:59' %s %s %s GROUP BY CONVERT(date, transTime)",
                    $start, $end, $connBSLive ?? '', $connGItem ?? '', $connBSBet ?? ''));
                $feeRecord = DB::select(sprintf("SELECT CONVERT(date, transTime) as daily, SUM(fee_amount) AS sumFee
                    FROM sphinx.getBSBetFee WHERE status = 1 AND transTime >= '%s 00:00:00' AND transTime <= '%s 23:59:59' %s %s %s GROUP BY CONVERT(date, transTime)",
                    $start, $end, $connBSLive ?? '', $connGItem ?? '', $connFee ?? ''));
            }
            if ($gameType == 'all' || $gameType == 'pwb') {
                $betRecord2 = DB::select(sprintf("SELECT CONVERT(date, created_at) as daily, SUM(bet_money) AS sumBet, SUM(CASE WHEN is_win=1 THEN floor(bet_money*bet_rate) END) AS sumWin
                    FROM sphinx.getMiniBetList WHERE is_win != -1 AND created_at >= '%s 00:00:00' AND created_at <= '%s 23:59:59' %s %s GROUP BY CONVERT(date, created_at)",
                    $start, $end, ($connGItem ?? ''), ($connMNBet ?? '')));

                $feeRecord2 = DB::select(sprintf("SELECT CONVERT(date, created_at) as daily, SUM(fee_amount) AS sumFee
                    FROM sphinx.getMiniFeeList WHERE status = 1 AND created_at >= '%s 00:00:00' AND created_at <= '%s 23:59:59' %s %s GROUP BY CONVERT(date, created_at)",
                    $start, $end, ($connGItem ?? ''), ($connFee2 ?? '')));
            }
            Log::info(sprintf("SELECT CONVERT(date, transTime) as daily, SUM(amount) AS sumBet, SUM(amount2) AS sumWin
                    FROM sphinx.getBSBetList WHERE transTime >= '%s 00:00:00' AND transTime <= '%s 23:59:59' %s %s %s GROUP BY CONVERT(date, transTime)",
                $start, $end, $connBSLive ?? '', $connGItem ?? '', $connBSBet ?? ''));
            $today = date('Y-m-d');
            $sumInfo = UserListProc::whereRaw(sprintf("userno IN (SELECT Item FROM dbo.SplitStringToInt('%s', ','))", implode(',', $arrSubUserId)))
                ->selectRaw('sum(money) as uMoney,
                    sum(floor(point)) as uPoint,
                    sum(ximax_point) as xPoint,
                    sum(ban_point) as bPoint,
                    sum(plus_point) as pPoint,
                    sum(sg_point) as sgPoint,
                    sum(fail_point) as failPoint,
                    sum(ppk_point) as ppkPoint'
                )
                ->first();

            $tempRecord = array();
            foreach ($dateRecord as $record) {
                $money = $point = null;
                switch ($gameType) {
                    case 'live':
                        $point = 0;
                        break;
                    default:
                        if ($record->dt == $today) {
                            $money = $sumInfo->uMoney;
                            $point = $sumInfo->uPoint;
                        }
                        break;
                }

                $index = array_search($record->dt, array_column($betRecord, 'daily'));
                $sumBet = $sumWin = $sumFee = 0;
                if ($index !== false) {
                    $sumBet = $betRecord[$index]->sumBet;
                    $sumWin = $betRecord[$index]->sumWin;
                }
                $index = array_search($record->dt, array_column($feeRecord, 'daily'));
                if ($index !== false)
                    $sumFee = $feeRecord[$index]->sumFee;

                $index = array_search($record->dt, array_column($betRecord2, 'daily'));
                if ($index !== false) {
                    $sumBet += $betRecord2[$index]->sumBet;
                    $sumWin += $betRecord2[$index]->sumWin;
                }
                $index = array_search($record->dt, array_column($feeRecord2, 'daily'));
                if ($index !== false)
                    $sumFee += $feeRecord2[$index]->sumFee;

                $tempRecord[] = array(
                    'date' => $record->dt,
                    'charge' => ($record->charge ?? 0),
                    'excharge' => ($record->excharge ?? 0),
                    'loss1' => ($record->charge ?? 0) - ($record->excharge ?? 0),
                    'bet' => $sumBet,
                    'win' => $sumWin,
                    'fee' => $sumFee,
                    'loss2' => $sumBet - $sumWin - $sumFee,
                    'money' => $money ?? $record->uMoney,
                    'point' => $point ?? $record->uPoint
                );
            }
            $dateRecord = $tempRecord;

            return view('casino.levelTable', ['dateRecord' => $dateRecord]);
        } catch (\Exception $ex) {
            Log::info("/analysis/partner/list {$searchUser} " . $ex->getMessage());
            return view('casino.levelTable', ['dateRecord' => []]);
        }
    }

    public function chexdetails(Request $request)
    {
        $objDate = getDateFromRangeDate($request->start_date);
        $data = array(
            'start_date' => $objDate[0] ?? date('Y-m-d'),
            'end_date' => $objDate[1] ?? date('Y-m-d'),
            'username' => $request->username ?? '',
            'sort' => $request->sort ?? 'asc',
            'type' => $request->type ?? 'req'
        );
        $snzSortType = "";
        $snzSortArrow = $data['sort'] == 'asc' ? 'desc' : 'asc';
        $nSumCharge = 0;
        $nSumExcharge = 0;

        $tempQuery = PayRecord::where('created_at', '>=', $data['start_date'] . ' 00:00:00')
            ->where('created_at', '<=', $data['end_date'] . ' 23:59:59');
        if ($data['username'] != '') {
            $searchUserInfo = User::where('userid', $data['username'])
                ->first();
            if (!empty($searchUserInfo))
                $tempQuery->where('userid', $searchUserInfo->id);
            else
                $tempQuery->where('userid', 0);
        }

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
        $chargeExhRecord = $tempQuery->paginate(25);
        for ($index = 0; $index < count($chargeExhRecord); $index++) {
            $record = $chargeExhRecord[$index];
            $headinfo = User::with(['joininfo' => function ($query) {
                $query->select('id', 'userid', 'nickname', 'user_color');
            }, 'headinfo' => function ($query) {
                $query->select('id', 'userid', 'nickname', 'user_color');
            }])
                ->where('id', $record->userid)
                ->select('id', 'userid', 'nickname', 'user_color', 'bankmaster', 'joinid', 'masterid')
                ->first();

            if (!empty($headinfo))
                $chargeExhRecord[$index]['userinfo'] = $headinfo;
            if ($record->verified == 2)
                if ($record->type == 1)
                    $nSumCharge += $record->money;
                else
                    $nSumExcharge += $record->money;
        }
        $data['nSumCharge'] = $nSumCharge;
        $data['nSumExcharge'] = $nSumExcharge;
        $data['chargeExhRecord'] = $chargeExhRecord;
        $data['start_date'] = $request->start_date;
        return view('analysis.chargeExchargeList', $data);
    }

    public function liveBetDetailCX(Request $request)
    {
        $cxapi = new CxApiController;
        $url = $cxapi->getRoundDetail($request->roundid);
        if ($url != '')
            return redirect($url);
        return '준비중입니다.';
    }
}
