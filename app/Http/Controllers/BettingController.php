<?php

namespace App\Http\Controllers;

use App\Models\BSGameCategory;
use App\Models\User;
use App\Models\MiniGameCategory;
use App\Models\SiteSetting;
use App\Models\MiniBetCtl;
use App\Models\MiniBetAdmTmp;
use App\Models\MiniBetListTmp;
use App\Models\MiniGameRate;
use App\Models\MiniBetListDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function casinoBetHistory(Request $request)
    {

        $sort_type = $request->type ?? 'main_sum2';
        $sort_arrow = $request->sort ?? 'asc';
        $gameType = $request->gameType ?? 'all';
        $gameItem = $request->gameItem ?? '';
        $searchUser = $request->searchUser ?? '';
        $startDate = $request->startDate ?? date('Y-m-d');
        $data = array(
            'type' => $sort_type,
            'sort' => $sort_arrow,
            'gameType' => $gameType,
            'gameItem' => $gameItem,
            'searchUser' => $searchUser,
            'startDate' => $startDate,
        );

        $searchUserId = null;
        if ($searchUser != '') {
            $temp = User::where('userid', $searchUser)
                ->where('rolecode', '0003')
                ->first();
            if (!empty($temp)) {
                $searchUserId = $temp->id;
            } else {
                $data['sumInfo'] = array();
                $data['userRecord'] = array();
                $data['gameRecord'] = array();
                return view('casino.bethistory', $data);
            }
        }

        $liveRecord = BSGameCategory::where('is_live', 1)
            ->orderByDesc('order_no')
            ->get();
        $slotRecord = BSGameCategory::where('is_live', 0)
            ->orderByDesc('order_no')
            ->get();

        $sort_arrow = $sort_arrow == 'asc' ? 'desc' : 'asc';
        switch ($gameType) {
            case 'all':
                $data['gameRecord'] = array();
                $collect = collect(DB::select(sprintf("EXEC %s.liveGameUserMonth2 @Start='%s 00:00:00', @End='%s 23:59:59'",
                    config('global.prefix'), $startDate, $startDate)));
                if ($searchUser != '') {
                    $collect = $collect->where('id', $searchUserId);
                }
                break;
            case 'slot':
                $code = implode(',', array_column($slotRecord->toArray(), 'thirdPartyCode'));
                $data['gameRecord'] = $slotRecord;
                if ($gameItem != '')
                    $code = $gameItem;
                $collect = collect(DB::select(sprintf("EXEC %s.liveBSUserMonth @Start='%s 00:00:00', @End='%s 23:59:59', @Game='%s', @Live=%d",
                    config('global.prefix'), $startDate, $startDate, $code, 0)));
                if ($searchUser != '') {
                    $collect = $collect->where('id', $searchUserId);
                }
                break;
            case 'live':
                $code = implode(',', array_column($liveRecord->toArray(), 'thirdPartyCode'));
                $data['gameRecord'] = $liveRecord;
                if ($gameItem != '')
                    $code = $gameItem;
                $collect = collect(DB::select(sprintf("EXEC %s.liveBSUserMonth @Start='%s 00:00:00', @End='%s 23:59:59', @Game='%s', @Live=%d",
                    config('global.prefix'), $startDate, $startDate, $code, 1)));
                if ($searchUser != '') {
                    $collect = $collect->where('id', $searchUserId);
                }
                break;
            default:
                break;
        }
        if ($sort_arrow == 'desc')
            $collect = $collect->sortByDesc($sort_type);
        else
            $collect = $collect->sortBy($sort_type);
        $tempRecord = $collect->toArray();
        $userRecord = $collect->paginate(15);

        $data['sumInfo'] = array(
            'sumBet' => array_sum(array_column($tempRecord, 'bet_sum')),
            'sumBetCnt' => array_sum(array_column($tempRecord, 'bet_num')),
            'sumWin' => array_sum(array_column($tempRecord, 'win_sum')),
            'sumWinCnt' => array_sum(array_column($tempRecord, 'win_num')),
            'sumLoss' => array_sum(array_column($tempRecord, 'loss_sum2')),
            'sumFee' => array_sum(array_column($tempRecord, 'fee_sum')),
            'sumLoss2' => array_sum(array_column($tempRecord, 'main_sum2')),
        );
        $data['userRecord'] = $userRecord ?? array();
        return view('casino.bethistory', $data);
    }

    public function betInfo(Request $request)
    {
        $userid = $request->userid;
        $gameType = $request->gameType ?? 'pwb';
        $gameItem = $request->gameItem ?? '';
        $startDate = $request->startDate ?? date('Y-m-d 00:00:00');
        $endDate = $request->endDate ?? date('Y-m-d 23:59:59');
        if (strlen($startDate) < 14)
            $startDate = $startDate . ' 00:00:00';
        if (strlen($endDate) < 14)
            $endDate = $endDate . ' 23:59:59';
        $itemLength = $request->itemLength ?? 15;
        if ($gameType == 'pwb') {
            $gameRecord = MiniGameCategory::orderBy('show_index', 'asc')
                ->get();
            if ($gameItem != '') {
                $index = array_search($gameItem, array_column($gameRecord->toArray(), 'game_code'));
                $pickRecord = MiniGamePick::where('gameid', $gameRecord[$index]->gameid)
                    ->get();
                $betInfo = MiniBetListAll::where('userid', $userid)
                    ->where('created_at', '>=', $startDate)
                    ->where('created_at', '<=', $endDate)
                    ->where('game_code', $gameItem)
                    ->get();
                $betInfo = json_decode(json_encode($betInfo), true);
                $betInfoArr = array_reduce($betInfo, function ($bets, $item) {
                    if (!isset($bets['sumBet'])) {
                        $bets['sumBet'] = 0;
                        $bets['sumWin'] = 0;
                    }
                    $bets['sumBet'] += empty($item['is_deleted']) ? $item['bet_money'] : 0;
                    $bets['sumWin'] += $item['is_win'] == 1 ? ($item['bet_money'] * $item['bet_rate']) : 0;
                    return $bets;
                });
                $feeInfo = MiniBetFeeAll::where('fee_userid', $userid)
                    ->where('created_at', '>=', $startDate)
                    ->where('created_at', '<=', $endDate)
                    ->where('game_code', $gameItem)
                    ->where('status', 1)
                    ->selectRaw('sum(fee_amount) as sumFee')
                    ->first();
                $sumInfo = array(
                    'sumBet' => $betInfoArr['sumBet'] ?? 0,
                    'sumWin' => $betInfoArr['sumWin'] ?? 0,
                    'sumFee' => $feeInfo->sumFee ?? 0,
                );
                $temp = MiniBetAdmDay::where('created_at', '>=', $startDate)
                    ->where('created_at', '<=', $endDate)
                    ->where('game_code', $gameItem)
                    ->where('userid', $userid)
                    ->orderByDesc('created_at')
                    ->first();
                $betRecord = collect(DB::select(sprintf("EXEC sphinx.betRecord @start='%s', @end='%s', @temp='%s', @code='%s', @user=%d",
                    $startDate, $endDate, (!empty($temp) ? $temp->created_at : ($startDate)), $gameItem, $userid)))
                    ->paginate($itemLength);
                /*$betRecord = MiniBetAdmAll::where('created_at', '>=', $startDate . ' 00:00:00')
                    ->where('created_at', '<=', $endDate . ' 23:59:59')
                    ->where('game_code', $gameItem)
                    ->where('userid', $userid)
                    ->orderByDesc('created_at')
                    ->paginate($itemLength);*/
            }
        } else {
            $tempSQL = BSBetListAll::where('userno', $userid)
                ->where('transTime', '>=', $startDate)
                ->where('transTime', '<=', $endDate);
            if ($gameType != 'all')
                $tempSQL->where('is_live', ($gameType == 'live' ? 1 : 0));
            if ($gameItem != '')
                $tempSQL->where('thirdParty', $gameItem);
            $tempSQL2 = clone $tempSQL;

            $betRecord = $tempSQL->orderByDesc('transTime')
                ->paginate($itemLength);
            $sumInfo = $tempSQL2->select(DB::raw('sum(amount) as sumBet'), DB::raw('sum(amount2) as sumWin'))
                ->first()->toArray();

            $tempSQL = BSBetFeeAll::where('proc_userid', $userid)
                ->where('transTime', '>=', $startDate)
                ->where('transTime', '<=', $endDate)
                ->where('status', 1);
            if ($gameType != 'all')
                $tempSQL->where('is_live', ($gameType == 'live' ? 1 : 0));
            if ($gameItem != '')
                $tempSQL->where('thirdParty', $gameItem);
            $feeInfo = $tempSQL->selectRaw('sum(fee_amount) as sumFee')
                ->first();
            $sumInfo['sumFee'] = $feeInfo->sumFee ?? 0;
            if ($gameType == 'live') {
                $gameRecord = BSGameCategory::where('is_live', 1)
                    ->orderByDesc('order_no')
                    ->get();
            } else if ($gameType == 'slot') {
                $gameRecord = BSGameCategory::where('is_live', 0)
                    ->orderByDesc('order_no')
                    ->get();
            }
        }
        $data = array(
            'userid' => $request->userid,
            'gameType' => $gameType,
            'gameItem' => $gameItem,
            'betRecord' => $betRecord ?? array(),
            'pickRecord' => $pickRecord ?? array(),
            'gameRecord' => $gameRecord ?? array(),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'itemLength' => $itemLength,
            'sumInfo' => $sumInfo ?? null,
            'userinfo' => User::where('id', $request->userid)->first(),
        );
        return view('casino.betInfo', $data);
    }

    public function list(Request $request)
    {
        $nPushRate = 0;
        $data['gameList'] = array("PWB", "UP3", "UP5", "CP3", "CS3", "CP5", "CS5", "BBP", "BBS", "HSP3", "HSP5", "HSS3", "HSS5", "EOS3", "EOS5", "KENO", "KLAYP5", "KLAYS5", "KLAYP2", "KLAYS2", "KSA", "MTP3", "MTS3", "MTP5", "MTS5", "XRP3", "XRP5", "XRS3", "XRS5", "PWPLUS", "PSPLUS", 'DHP');
        $data['gameRecord'] = MiniGameCategory::orderBy('show_index', 'asc')->get();
        $operateRecord = MiniGameCategory::where('is_operate', 1)->whereIn('game_code', $data['gameList'])->get();
        $data['gameGroup'] = array_column($operateRecord->toArray(), 'game_type');
        $data['start_date'] = $request->start_date ?? date('Y-m-d');
        $data['gameType'] = $request->gameType ?? '';
        $data['gameItem'] = $request->gameItem ?? '';
        $game_code = '';
        $gameId = 0;
        if ($data['gameType'] != '')
            $gameId = $data['gameType'];
        if ($data['gameItem'] != '')
            $gameId = $data['gameItem'];
        $data['game_id'] = $gameId;

        if ($gameId != 0) {
            $index = array_search($gameId, array_column($data['gameRecord']->toArray(), 'gameid'));
            $game_code = $data['gameRecord'][$index]->game_code;
            $nPushRate = $data['gameRecord'][$index]->push_fee;
        }

        $siteinfo = SiteSetting::take(1)->first();
        $betCnt = $siteinfo['betcnt'];
        if ($betCnt <= 0) $betCnt = 300;

        $end = $data['start_date'];

//        $tempQuery = MiniBetCtl::where(function ($query) {
//                $query->where('bet_count', '>', 0);
//                $query->orWhere('step_reset', 2);
//            })
//            ->where('created_at', '>=', $end.' 00:00:00')
//            ->where('created_at', '<=', $end.' 23:59:59');
        $tempQuery = MiniBetCtl::where('created_at', '>=', $end . ' 00:00:00')
            ->where('created_at', '<=', $end . ' 23:59:59');
        if ($game_code != '') {
            if ($data['gameItem'] == '')
                $tempQuery = $tempQuery->where('game_code', 'like', $game_code . '%');
            else
                $tempQuery = $tempQuery->where('game_code', $game_code);
        }
        $tempQuery2 = clone $tempQuery;
        $betRecord = $tempQuery->orderBy('created_at', 'desc')->paginate($betCnt);
        //$betRecord = $tempQuery->get();

        $tempRecord = $tempQuery2->where('step_reset', '!=', 2)
            ->where('game_result', '!=', '')
            ->selectRaw('SUM(bet_user) as nSumBet, SUM(push_user) as nSumPush, SUM(push_send) as nSumPushSend, SUM(push_win) as nSumPushWin, SUM(bet_win) as nSumBetWin, SUM(bet_fee) as nSumBetFee, SUM(push_fee) as nSumPushFee')
            ->first();
        $data['nSumBet'] = $tempRecord->nSumBet;
        $data['nSumPush'] = $tempRecord->nSumPush;
        $data['nSumPushSend'] = $tempRecord->nSumPushSend;
        $data['nSumPushDiff'] = $data['nSumPush'] - $data['nSumPushSend'];
        $data['nSumPushWin'] = $tempRecord->nSumPushWin;
        $data['nSumBetWin'] = $tempRecord->nSumBetWin;
        $data['nSumLoss'] = $data['nSumBet'] - $data['nSumPushSend'] + $data['nSumPushWin'] - $data['nSumBetWin'];
        $data['nSumBetFee'] = $tempRecord->nSumBetFee;
        $data['nSumPushFee'] = $tempRecord->nSumPushFee;
        $data['nSumMainLoss'] = $data['nSumLoss'] - $data['nSumBetFee'] + $data['nSumPushFee'];

        $betAdmRecord = MiniBetAdmTmp::groupBy('game_code', 'bet_round')
            ->selectRaw('count(*) as bet_count, game_code, bet_round')
            ->get();
        $betListRecord = MiniBetListTmp
            ::groupBy('game_code', 'bet_round')
            ->selectRaw('SUM(bet_money) as sum_bet_site, SUM(bet_money * bet_send / 100) as sum_push_user, game_code, bet_round')
            ->get();

        foreach ($betRecord as $record) {
            if ($record->game_result == "") {
                $arrAdmInfo = array_filter($betAdmRecord->toArray(), function ($bet) use ($record) {
                    if ($bet['game_code'] == $record->game_code && $bet['bet_round'] == $record->game_round) return true;
                    return false;
                });
                $arrBetInfo = array_filter($betListRecord->toArray(), function ($bet) use ($record) {
                    if ($bet['game_code'] == $record->game_code && $bet['bet_round'] == $record->game_round) return true;
                    return false;
                });

                if (!empty($arrAdmInfo) && !empty($arrBetInfo)) {
                    $gameInfo = MiniGameCategory::where('game_code', $record->game_code)
                        ->first();
                    $pushInfo = MiniPushList::where('gameid', $gameInfo->gameid)
                        ->where('is_enable', 1)
                        ->selectRaw('sum(send_rate) as sumRate')
                        ->first();

                    $betinfo = array_values($arrBetInfo)[0];
                    $adminfo = array_values($arrAdmInfo)[0];

                    $sumPush = ($betinfo['sum_bet_site'] - $betinfo['sum_push_user']) * ($pushInfo->sumRate ?? 0) / 100;
                    $nSumBetRaw = $betinfo['sum_bet_site'] - $sumPush;

                    $record->bet_user = $betinfo['sum_bet_site'];  // 유저배팅
                    $record->bet_count = $adminfo['bet_count'];  // 배팅횟수
                    $record->bet_site2 = $nSumBetRaw;  // 회원승부
                    $record->push_user2 = $sumPush;  // 보험대상
                }
            }
        }

        $data['betRecord'] = $betRecord;
        $rateinfo = MiniGameRate::where('gameid', $gameId ?? 0)
            ->first();
        $nUserWinRate = 0;
        if (!empty($rateinfo))
            $nUserWinRate = $rateinfo->rate;
        $data['user_win_rate'] = $nUserWinRate; // 유저승부수수료
        $data['pushinfo'] = $nPushRate;

        // 미배당 찾는다
        $numTemp1 = MiniBetListTmp::where('is_win', -1)
            ->where('is_reset', '!=', 2)
            ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($end . ' -7 days')))
            ->where('created_at', '<=', date('Y-m-d H:i:s', strtotime('-6 minutes')))
            ->where('is_deleted', 0)
            ->groupBy('game_code')
            ->groupBy('bet_round')
            ->count();
        $numTemp2 = MiniBetListDay::where('is_win', -1)
            ->where('is_reset', '!=', 2)
            ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($end . ' -7 days')))
            ->where('created_at', '<=', date('Y-m-d  23:59:59'))
            ->where('is_deleted', 0)
            ->groupBy('game_code')
            ->groupBy('bet_round')
            ->count();
        $data['notCount'] = $numTemp1 + $numTemp2;

        return view('game.mini.list', $data);
    }
}
