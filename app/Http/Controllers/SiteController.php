<?php

namespace App\Http\Controllers;

use App\Models\BSBetListDay;
use App\Models\BSGameCategory;
use App\Models\BSGameCategoryLog;
use App\Models\BSGameContent;
use App\Models\BSPushList;
use App\Models\BSPushListLog;
use App\Models\BSPushListTemp;
use App\Models\MiniBetListDay;
use App\Models\MiniPushList;
use App\Models\MiniPushListLog;
use App\Models\MiniPushListTemp;
use App\Models\SiteBlackNum;
use App\Models\SiteSetting;
use App\Models\MiniGameCategory;
use App\Models\MiniGameCategoryLog;
use App\Models\MiniGamePick;
use App\Models\MiniGamePush;
use App\Models\MiniGamePushTemp;
use App\Models\MiniGamePushLog;
use App\Models\MiniGameBetLimit;
use App\Models\MiniPushTypes;
use App\Models\MiniResultPwb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App;
use Illuminate\Support\Facades\Log;

class SiteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function casino_edit_view(Request $request)
    {
        $data['game_id'] = $request->gameid ?? 0;
        if ($data['game_id'] == 0)
            return redirect()->route('gameslist');

        $data['game_type'] = 'casino';

        $data['gameinfo'] = BSGameCategory::where('thirdPartyCode', $request->gameid)
            ->where('is_live', 1)
            ->first();

        return view('site.game.casino.edit', $data);
    }

    public function casino_edit_proc(Request $request)
    {
        $snzContent = getOperationTxt($request->state);
        $updateParam = [
            'is_enable' => $request->state,
            'order_no' => $request->order,
        ];

        if ($request->gameid == 998) {
            $betLimit = array("end_sec" => 5);
            if (!empty($request->betCode)) {
                $updateParam['betCode'] = json_encode($request->betCode);
            }
            if (!empty($request->betEnd))
                $betLimit['end_sec'] = $request->betEnd;
            $updateParam['betLimit'] = json_encode($betLimit);
        } else
            if (!empty($request->betLimit))
                $updateParam['betLimit'] = $request->betLimit;
        BSGameCategory::where('thirdPartyCode', $request->gameid)
            ->where('is_live', 1)
            ->update($updateParam);
        // 카지노 모든 수수료와, 보험시간은 일치해야 한다
        BSGameCategory::where('is_live', 1)
            ->update([
                'fee' => $request->fee,
                'cycle' => $request->cycle,
            ]);
        BSGameCategoryLog::insert(['adminid' => auth()->user()->id,
            'category_id' => $request->gameid,
            'content' => sprintf('운영상태: %s, 수수료: %s, 주기: %s분', $snzContent, $request->fee, $request->cycle),
            'ip_addr' => Session::get('ip_addr'),
        ]);

        return back();
    }

    public function casino_push_view(Request $request)
    {
        $nGameId = $request->gameid ?? 0;
        if ($nGameId == 0)
            return redirect()->route('gameslist');
        $pushRecord = BSPushList::where('gameid', $nGameId)
            ->get();
        $data = array(
            'pushRecord' => $pushRecord,
            'gameId' => $nGameId,
            'game_type' => 'casino',
        );
        return view('site.game.casino.push.list', $data);
    }

    public function casinoPushAdd(Request $request)
    {
        $nGameId = $request->gameid ?? 0;
        if ($nGameId == 0)
            return redirect()->route('gameslist');

        $data = array(
            'gameId' => $nGameId,
            'game_type' => 'casino',
        );

        return view('site.game.casino.push.add', $data);
    }

    public function casinoPushAddProc(Request $request)
    {
        $tempid = BSPushList::insertGetId([
            'gameid' => $request->gameid,
            'api_url_protocol' => $request->api_url_protocol,
            'api_url' => $request->api_url,
            'api_url_page' => $request->api_url_page,
            'api_username' => $request->api_username,
            'api_key' => $request->api_key,
            'api_sitename' => $request->api_sitename,
            'send_rate' => $request->send_rate,
            'is_enable' => $request->is_enable,
        ]);

        BSPushListTemp::insert([
            'tempid' => $tempid,
            'gameid' => $request->gameid,
            'api_url_protocol' => $request->api_url_protocol,
            'api_url' => $request->api_url,
            'api_url_page' => $request->api_url_page,
            'api_username' => $request->api_username,
            'api_key' => $request->api_key,
            'api_sitename' => $request->api_sitename,
            'send_rate' => $request->send_rate,
            'is_enable' => $request->is_enable,
        ]);

        return redirect(route('gamesCasinoPush') . '?gameid=' . $request->gameid);
    }

    public function casinoPushEdit(Request $request)
    {
        $nPushId = $request->id ?? 0;
        if ($nPushId == 0)
            return redirect()->route('gameslist');

        $pushinfo = BSPushListTemp::where('tempid', $request->id)
            ->orderByDesc('created_at')
            ->first();
        $data = array(
            'gameId' => $pushinfo->gameid,
            'game_type' => 'casino',
            'pushinfo' => $pushinfo,
        );

        return view('site.game.casino.push.edit', $data);
    }

    public function casinoPushEditProc(Request $request)
    {
        $nPushId = $request->id ?? 0;
        if ($nPushId == 0)
            return redirect()->route('gameslist');

        BSPushListTemp::insert([
            'tempid' => $request->id,
            'gameid' => $request->gameid,
            'api_url_protocol' => $request->api_url_protocol,
            'api_url' => $request->api_url,
            'api_url_page' => $request->api_url_page,
            'api_username' => $request->api_username,
            'api_key' => $request->api_key,
            'api_sitename' => $request->api_sitename,
            'send_rate' => $request->send_rate,
            'is_enable' => $request->is_enable,
        ]);

        $content = "*사이트명: " . $request['api_sitename'] . " *프로토콜: " . $request['api_url_protocol'] . " *URL: " . $request['api_url'] . " *PAGE: " . $request['api_url_page'] . " *계정: " . $request['api_username'] . " *비번(키): " . $request['api_key'] . " *비율: " . $request['send_rate'] . " *상태: " . $request['is_enable'];
        BSPushListLog::insert([
            "game_push_id" => $request->id,
            "gameid" => $request->gameid,
            "adminid" => auth()->user()->id,
            "ip_addr" => Session::get('ip_addr'),
            "content" => $content
        ]);

        return redirect(route('gamesCasinoPush') . '?gameid=' . $request->gameid);
    }

    public function casinoPushDelete(Request $request)
    {
        $pushInfo = BSPushList::where('id', $request->id)
            ->where('is_enable', 1)
            ->first();
        if (!empty($pushInfo)) {
            $sumInfo = BSPushList::where('gameid', $pushInfo->gameid)
                ->where('is_enable', 1)
                ->selectRaw('SUM(send_rate) as rateSum')
                ->first();
            if (!empty($sumInfo) && $sumInfo->rateSum > 0) {
                BSBetListDay::where('pushid', 0)
                    ->update(['send_rate' => $sumInfo->rateSum]);
            }
        }

        BSPushList::where('id', $request->id)
            ->delete();
        BSPushListTemp::where('tempid', $request->id)
            ->delete();
        return back();
    }

    public function casinoPushLogs(Request $request)
    {
        $data['game_type'] = 'casino';
        $pushinfo = BSPushList::where('id', $request->id)
            ->first();
        $gameinfo = BSGameCategory::where('thirdPartyCode', $pushinfo->gameid)
            ->first();
        $data['logRecord'] = BSPushListLog::where('game_push_id', $request->id)
            ->orderBy('created_at', 'desc')
            ->paginate(25);
        $data['gameName'] = $gameinfo->tKR;

        return view('site.game.casino.push.logs', $data);
    }

    #region slot working
    public function slot_edit_view(Request $request)
    {
        $data['game_id'] = $request->gameid ?? 0;
        if ($data['game_id'] == 0)
            return redirect()->route('gameslist');

        $data['game_type'] = 'slot';

        $data['gameinfo'] = BSGameCategory::where('thirdPartyCode', $request->gameid)
            ->where('is_live', 0)
            ->first();

        return view('site.game.slot.edit', $data);
    }

    public function slot_edit_proc(Request $request)
    {
        $snzContent = getOperationTxt($request->state);

        BSGameCategory::where('thirdPartyCode', $request->gameid)
            ->where('is_live', 0)
            ->update(['is_enable' => $request->state, 'order_no' => $request->order]);
        BSGameCategoryLog::insert(['adminid' => auth()->user()->id,
            'category_id' => $request->gameid,
            'content_id' => 0,
            'content' => '운영상태: ' . $snzContent,
            'ip_addr' => Session::get('ip_addr'),
        ]);

        return back();
    }

    public function slot_edit_list(Request $request)
    {
        $data['game_id'] = $request->gameid ?? 0;
        $data['game_type'] = 'slot';

        $data['gameRecord'] = BSGameContent::where('thirdPartyCode', $request->gameid)
            ->where('c', '!=', 'LIVE')
            ->get();

        return view('site.game.slot.list', $data);
    }

    public function slot_edit_list_enable(Request $request)
    {
        $snzContent = '';
        switch ($request->state) {
            case 0:
                $snzContent = '미사용';
                break;
            case 1:
                $snzContent = '정상운영';
                break;
        }

        $slotinfo = BSGameContent::where('playid', $request->slotid)
            ->first();

        BSGameContent::where('playid', $request->slotid)
            ->update(['is_enable' => $request->enable]);

        BSGameCategoryLog::insert(['adminid' => auth()->user()->id,
            'category_id' => $slotinfo->thirdPartyCode,
            'content_id' => $request->slotid,
            'content' => '운영상태: ' . $snzContent,
            'ip_addr' => Session::get('ip_addr'),
        ]);

        return back();
    }

    public function slot_update()
    {
        if (in_array(config('app.api_type'), ['ximax', 'sdapi']))
            $type = config('app.api_type');
        else
            $type = config('app.slot_type');

        Http::get('http://api.' . \request()->getHost() . "/api/$type/load/slot")
            ->body();

        return back();
    }

    #endregion

    public function blackBankNumbers(Request $request)
    {
        $data['search_opt'] = $request->search_opt ?? '';
        $data['search_text'] = $request->search_text ?? '';
        $tempQuery = SiteBlackNum::orderBy('created_at', 'desc');
        if ($request->search_text != '') {
            $tempQuery->where('num_code', $request->search_text);
        }
        $data['bankRecord'] = $tempQuery->paginate(20);

        return view('site.black_bank', $data);
    }

    public function blackBankNumbersAddView()
    {
        return view('site.black_bank_add');
    }

    public function blackBankNumbersAddProc(Request $request)
    {
        SiteBlackNum::insert([
            'adminid' => auth()->user()->id,
            'num_code' => $request->num_code,
            'name' => $request->name,
            'bank' => $request->bank,
            'memo' => $request->memo,
        ]);

        return redirect()->route('blackBankNumbers');
    }

    public function blackBankNumbersDel(Request $request)
    {
        SiteBlackNum::where('id', $request->id)
            ->delete();

        return back();
    }

    public function setting_view()
    {
        $data = SiteSetting::take(1)->first();
        return view('site.setting', $data);
    }

    public function setting_proc(Request $request)
    {
        $data = $request->all();
        unset($data['_token'], $data['id']);
        if (isset($data['admin_new'])) {
            updateEnv(array('ADMIN_NEW' => $data['admin_new']));
            unset($data['admin_new']);
        }
        SiteSetting::take(1)->update($data);
        return back();
    }

    public function games_list()
    {
        $data['game_id'] = 0;
        $data['game_type'] = '';
        return view('site.game.index', $data);
    }

    #region mini working
    public function mini_edit_view(Request $request)
    {
        $data['game_id'] = $request->gameid ?? 0;
        if ($data['game_id'] == 0)
            return redirect()->route('gameslist');

        $data['game_type'] = 'mini';

        $data['gameinfo'] = MiniGameCategory::where('gameid', $request->gameid)
            ->first();

        return view('site.game.mini.edit', $data);
    }

    public function mini_edit_proc(Request $request)
    {
        MiniGameCategory::where('gameid', $request->gameid)
            ->update([
                'end_time' => $request->end_sec,
                'end_time_site' => $request->early_endsec,
                'limit_sum_money' => str_replace(",", "", $request->max_bet),
                'is_operate' => $request->state,
                'push_fee' => $request->push_fee,
                'show_index' => $request->list_order ?? 0,
            ]);

        $is_active = "";
        switch ($request->state) {
            case 0:
                $is_active = '숨김';
                break;
            case 1:
                $is_active = '정상운영';
                break;
            case 2:
                $is_active = '점검';
                break;
        }
        $content = "*배팅마감: " . $request['end_sec'] . " *early_endsec: " . $request['early_endsec'] . " *배팅총액제한: " . $request['max_bet'] . " *운영여부: " . $is_active;

        MiniGameCategoryLog::insert([
            "gameid" => $request->gameid,
            "adminid" => auth()->user()->id,
            "ip_addr" => Session::get('ip_addr'),
            "content" => $content
        ]);

        return back();
    }

    public function games_logs(Request $request)
    {
        $data['logRecord'] = array();
        switch ($request->gametype) {
            case 'mini':
                $data['logRecord'] = MiniGameCategoryLog::with(['userinfo' => function ($query) {
                    $query->select('id', 'userid');
                }, 'gameinfo' => function ($query) {
                    $query->select('gameid', 'game_name');
                }])
                    ->where('gameid', $request->gameid)
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
                return view('site.game.logs', $data);
            case 'slot':
                $data['logRecord'] = BSGameCategoryLog::where('category_id', $request->gameid)
                    ->orderByDesc('created_at')
                    ->paginate(20);
                $data['cateinfo'] = BSGameCategory::where('thirdPartyCode', $request->gameid)
                    ->where('is_live', 0)
                    ->first();
                return view('site.game.slot.logs', $data);
            case 'casino':
                $data['logRecord'] = BSGameCategoryLog::where('category_id', $request->gameid)
                    ->orderByDesc('created_at')
                    ->paginate(20);
                $data['cateinfo'] = BSGameCategory::where('thirdPartyCode', $request->gameid)
                    ->where('is_live', 1)
                    ->first();
                return view('site.game.casino.logs', $data);
        }

        return response()->json(['status' => 'Access denied'], 200);
    }

    public function mini_pick_view(Request $request)
    {
        $data['pickRecord'] = MiniGamePick::where('gameid', $request->gameid)
            ->get();
        $data['game_id'] = $request->gameid;
        $data['game_type'] = 'mini';
        return view('site.game.mini.pick', $data);
    }

    public function mini_pick_edit_view(Request $request)
    {
        $data['game_id'] = $request->gameid;
        $data['game_type'] = 'mini';
        $data['pickinfo'] = MiniGamePick::where('pick_no', $request->pickid)
            ->where('gameid', $request->gameid)
            ->first();
        return view('site.game.mini.pick_edit', $data);
    }

    public function mini_pick_edit_proc(Request $request)
    {
        MiniGamePick::where('pick_no', $request->pickid)
            ->where('gameid', $request->gameid)
            ->update(['pick_rate' => $request->allot_rate,
                'pick_max_bet' => $request->max_bet]);
        return back();
    }

    public function mini_push_view(Request $request)
    {
        $data['game_id'] = $request->gameid;
        $data['game_type'] = 'mini';
        $data["pushRecord"] = MiniPushList::where('gameid', $request->gameid)
            ->get();
        $nSumPush = 0;
        foreach ($data["pushRecord"] as $record) {
            if ($record->is_enable == 1)
                $nSumPush += $record->send_rate;
        }
        $data['sum_push'] = $nSumPush;

        return view('site.game.mini.push.list', $data);
    }

    public function mini_push_type_view(Request $request)
    {
        $data['game_id'] = $request->gameid;
        $data['game_type'] = 'mini';
        $data['typeRecord'] = MiniPushTypes::all();

        return view('site.game.mini.push.type', $data);
    }

    public function mini_push_type_proc(Request $request)
    {
        $data['game_id'] = $request->gameid;
        $data['game_type'] = 'mini';
        $data['type_id'] = $request->insur_type;

        if ($data['type_id'] == '')
            return back();

        return redirect(route('gamesMiniPushAdd') . '?typeid=' . $data['type_id'] . '&gameid=' . $data['game_id']);
    }

    public function mini_push_add_view(Request $request)
    {
        $data['game_id'] = $request->gameid;
        $data['game_type'] = 'mini';
        $data['type_id'] = $request->typeid;
        $data['typeinfo'] = MiniPushTypes::where('pushid', $request->typeid)
            ->first();

        if (empty($data['typeinfo']))
            return back();

        return view('site.game.mini.push.add', $data);
    }

    public function mini_push_add_proc(Request $request)
    {
        MiniPushList::insert([
            'gameid' => $request->gameid,
            'pushid' => $request->pushid,
            'api_url_protocol' => $request->api_url_protocol,
            'api_url' => $request->api_url,
            'api_url_page' => $request->api_url_page,
            'api_username' => $request->api_username,
            'api_key' => $request->api_key,
            'api_sitename' => $request->api_sitename,
            'send_rate' => $request->send_rate,
            'is_enable' => $request->is_enable
        ]);

        return redirect(route('gamesMiniPush') . '?gameid=' . $request->gameid);
    }

    public function mini_push_edit_view(Request $request)
    {
        $gameinfo = MiniPushList::where('id', $request->id)
            ->first();

        $tempinfo = MiniPushListTemp::where('gameid', $gameinfo->gameid)
            ->where('tblid', $request->id)
            ->where('pushid', $gameinfo->pushid)
            ->orderByDesc("created_at")
            ->first();

        if (!empty($tempinfo))
            $data['typeinfo'] = $tempinfo;
        else
            $data['typeinfo'] = $gameinfo;

        $data['game_id'] = $gameinfo->gameid;
        $data['game_type'] = 'mini';
        $data['type_id'] = $gameinfo->typeid;

        return view('site.game.mini.push.add', $data);
    }

    public function mini_push_edit_proc(Request $request)
    {
        $pushinfo = MiniPushList::where('id', $request->id)
            ->first();

        $datainfo = array(
            'tblid' => $request->id,
            'gameid' => $pushinfo->gameid,
            'pushid' => $pushinfo->pushid,
            'api_url_protocol' => $request->api_url_protocol,
            'api_url' => $request->api_url,
            'api_url_page' => $request->api_url_page,
            'api_username' => $request->api_username,
            'api_key' => $request->api_key,
            'api_sitename' => $request->api_sitename,
            'send_rate' => $request->send_rate,
            'is_enable' => $request->is_enable
        );

        // 게임 시작후 60초안에 보험설정되면 유저 배팅내역에 설정된다
        $gameRecord = MiniGameCategory::where('game_type', 'lottery')
            ->get();
        $isProc = false;
        $nGameIndex = array_search($pushinfo->gameid, array_column($gameRecord->toArray(), 'gameid'));
        if ($nGameIndex !== false) {
            $leftTime = getPwbRemainTime();
            if ($leftTime >= (5 * 60 - 60)) {
                $roundinfo = MiniResultPwb::orderBy('round', 'desc')
                    ->first();
                $nLastRound = $roundinfo->round + 1;

                unset($datainfo['tblid']);
                MiniPushList::where('id', $request->id)
                    ->update($datainfo);

                $newinfo = MiniPushList::where('gameid', $pushinfo->gameid)
                    ->where('is_enable', 1)
                    ->select(DB::raw('SUM(TblGamePush.send_rate) as sum_send_rate'))
                    ->first();

                $nPushRate = 0;
                if (!is_null($newinfo->sum_send_rate)) {
                    $nPushRate = $newinfo->sum_send_rate;
                }

                MiniBetListDay::where('game_code', $gameRecord[$nGameIndex]->game_code)
                    ->where('bet_round', $nLastRound)
                    ->update(['push_rate' => $nPushRate]);
                $isProc = true;
            }
        }

        if (!$isProc) {
            MiniPushListTemp::insert($datainfo);
        }

        $content = "*사이트명: " . $request['api_sitename'] . " *프로토콜: " . $request['api_url_protocol'] . " *URL: " . $request['api_url'] . " *PAGE: " . $request['api_url_page'] . " *계정: " . $request['api_username'] . " *비번(키): " . $request['api_key'] . " *비율: " . $request['send_rate'] . " *상태: " . $request['is_enable'];
        MiniPushListLog::insert([
            "game_push_id" => $request->id,
            "gameid" => $pushinfo->gameid,
            "adminid" => auth()->user()->id,
            "ip_addr" => Session::get('ip_addr'),
            "content" => $content
        ]);

        return redirect(route('gamesMiniPush') . '?gameid=' . $request->gameid);
    }

    public function mini_push_del_proc(Request $request)
    {
        MiniPushList::where('id', $request->id)
            ->delete();
        MiniPushListTemp::where('tblid', $request->id)
            ->delete();

        return back();
    }

    public function mini_push_log_view(Request $request)
    {
        $data['game_type'] = 'mini';
        $data['logRecord'] = MiniPushListLog::with(['userinfo' => function ($query) {
            $query->select('id', 'userid');
        }, 'gameinfo' => function ($query) {
            $query->select('gameid', 'game_name');
        }])
            ->where('game_push_id', $request->id)
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('site.game.mini.push.logs', $data);
    }

    public function mini_limit_view(Request $request)
    {
        $data['game_id'] = $request->gameid;
        $data['game_type'] = 'mini';
        $data['pickRecord'] = MiniGamePick::where('gameid', $request->gameid)
            ->get();
        $data['limitRecord'] = MiniGameBetLimit::where('gameid', $request->gameid)
            ->get();

        return view('site.game.mini.limit.list', $data);
    }

    public function mini_limit_add_view(Request $request)
    {
        $data['game_id'] = $request->gameid;
        $data['game_type'] = 'mini';
        $data['proc_ment'] = '추가';
        $data['pickRecord'] = MiniGamePick::where('gameid', $request->gameid)
            ->get();
        return view('site.game.mini.limit.add', $data);
    }

    public function mini_limit_add_proc(Request $request)
    {
        $data['game_id'] = $request->gameid;
        $data['game_type'] = 'mini';
        MiniGameBetLimit::insert([
            'gameid' => $request->gameid,
            'name' => $request->name,
            'val01' => $request->val01,
            'val02' => $request->val02,
            'val03' => $request->val03,
            'val04' => $request->val04,
            'val05' => $request->val05 ?? '',
            'val06' => $request->val06 ?? '',
            'val07' => $request->val07 ?? '',
            'val08' => $request->val08 ?? '',
            'val09' => $request->val09 ?? '',
            'val10' => $request->val10 ?? '',
            'val11' => $request->val11 ?? '',
            'val12' => $request->val12 ?? '',
        ]);

        return redirect(route('gamesMiniLimit') . '?gameid=' . $request->gameid);
    }

    public function mini_limit_edit_view(Request $request)
    {
        $limitinfo = MiniGameBetLimit::where('limitid', $request->limitid)
            ->first();

        $data['game_id'] = $limitinfo->gameid;
        $data['game_type'] = 'mini';
        $data['pickRecord'] = MiniGamePick::where('gameid', $limitinfo->gameid)
            ->get();
        $data['limitinfo'] = $limitinfo;
        $data['proc_ment'] = '편집';
        return view('site.game.mini.limit.add', $data);
    }

    public function mini_limit_edit_proc(Request $request)
    {
        $limitinfo = MiniGameBetLimit::where('limitid', $request->limitid)
            ->first();

        MiniGameBetLimit::where('limitid', $request->limitid)
            ->update([
                'name' => $request->name,
                'val01' => $request->val01,
                'val02' => $request->val02,
                'val03' => $request->val03,
                'val04' => $request->val04,
                'val05' => $request->val05 ?? '',
                'val06' => $request->val06 ?? '',
                'val07' => $request->val07 ?? '',
                'val08' => $request->val08 ?? '',
                'val09' => $request->val09 ?? '',
                'val10' => $request->val10 ?? '',
                'val11' => $request->val11 ?? '',
                'val12' => $request->val12 ?? '',
            ]);

        return redirect(route('gamesMiniLimit') . '?gameid=' . $limitinfo->gameid);
    }

    public function mini_limit_del(Request $request)
    {
        MiniGameBetLimit::where('limitid', $request->limitid)
            ->delete();
        return back();
    }

    #endregion

}
