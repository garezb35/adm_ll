<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Lib\BossController;
use App\Http\Controllers\Lib\PlusController;
use App\Http\Controllers\Lib\SGController;
use App\Http\Controllers\Lib\XimaxController;
use App\Http\Controllers\Lib\KtenController;
use App\Models\BSGameCategory;
use App\Models\BSGameContent;
use App\Models\BSGamePush;
use App\Models\BSGamePushLog;
use App\Models\BSGamePushTemp;
use App\Models\BSMeBan;
use App\Models\BSMeMoney;
use App\Models\MiniBetLimit;
use App\Models\MiniBetLimitLog;
use App\Models\MiniGameBetLimit;
use App\Models\MiniGamePush;
use App\Models\MiniGamePushLog;
use App\Models\MiniGamePushTemp;
use App\Models\MiniPushList;
use App\Models\MiniPushListLog;
use App\Models\MiniPushListTemp;
use App\Models\MiniRate;
use App\Models\MiniRateList;
use App\Models\MiniRateLog;
use App\Models\MiniResultPwb;
use App\Models\MiniBetAdmDay;
use App\Models\MiniBetAdm;
use App\Models\MiniGameCategory;
use App\Models\MiniGamePick;
use App\Models\MiniBetListDay;
use App\Models\MiniBetList;
use App\Models\MiniBetFeeDay;
use App\Models\MiniBetFee;
use App\Models\RollingLog;
use App\Models\RollingMini;
use App\Models\RollingBS;
use App\Models\SiteDomain;
use App\Models\SiteSetting;
use App\Models\SvcAskTemp;
use App\Models\User;
use App\Models\SvcMsg;
use App\Models\PayCharge;
use App\Models\PayExcharge;
use App\Models\UserInfoLog;
use App\Models\UserListProc;
use App\Models\UserLoginLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\Input;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit_view(Request $request)
    {
        $userinfo = User::with(['joininfo' => function ($query) {
            $query->select('id', 'userid', 'nickname', 'user_color');
        }, 'headinfo' => function ($query) {
            $query->select('id', 'userid', 'nickname', 'user_color');
        }])
            ->where('id', $request->id)
            ->first();

        return view('user.edit')->with('userinfo', $userinfo);
    }

    public function edit_proc(Request $request)
    {
        $userinfo = User::where('id', $request->id)->first();
        $old_info = '비번:' . $userinfo->password_show . ' / 환전비번:' . $userinfo->exchangepassword . ' / ' . $userinfo->bankname . ' ' . $userinfo->banknumber . ' ' . $userinfo->bankmaster . ' /';
        $new_info = '비번:' . $request->password . ' / 환전비번:' . $request->exchangepassword . ' / ' . $request->bankname . ' ' . $request->banknumber . ' ' . $request->bankmaster . ' /';

        $arrSiteInfo = SiteSetting::take(1)->first();
        if ($arrSiteInfo->is_use_pwd == 0) {
            if (preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$^()!%*#?&])[A-Za-z\d@$^()!%*#?&]{6,20}$/", $request->password) == false) {
                return Redirect::back()->with('result', 'fail2');
            }
        }

        if (preg_match('/[^\x{1100}-\x{11FF}\x{3130}-\x{318F}\x{AC00}-\x{D7AF}0-9a-zA-Z]/u', $request->bankname)
            || preg_match('/[^\x{1100}-\x{11FF}\x{3130}-\x{318F}\x{AC00}-\x{D7AF}0-9a-zA-Z]/u', $request->banknumber)
            || preg_match('/[^\x{1100}-\x{11FF}\x{3130}-\x{318F}\x{AC00}-\x{D7AF}0-9a-zA-Z]/u', $request->bankmaster)) {
            return Redirect::back()->with('result', 'fail3');
        }

        $arrDataInfo = array(
            'exchangepassword' => $request->exchangepassword,
            'api_key' => $request->api_key,
            'api_ip_flag' => $request->api_ip_flag,
            'api_ip' => $request->api_ip,
            'joinkey' => $request->joinkey,
            'bankname' => $request->bankname,
            'banknumber' => $request->banknumber,
            'bankmaster' => $request->bankmaster,
            'phone' => $request->phone,
            'memo' => $request->memo,
            'limit_bet' => $request->limit_bet,
            'limit_exchange' => $request->limit_exchange,
            'limit_rolling' => $request->limit_rolling,
            'user_color' => $request->user_color,
            'isStore' => $request->isStore,
            'is_evo2' => $request->is_evo2 ?? 0,
            'is_pra2' => $request->is_pra2 ?? 0,
            'updated_at' => date('Y-m-d H:i:s'),
        );
        if ($request->password != $userinfo->password_show) {
            $arrDataInfo['password'] = Hash::make($request->password);
            $arrDataInfo['password_show'] = $request->password;
        }
        $isUpdated = User::where('id', $request->id)
            ->update($arrDataInfo);
        UserListProc::where('userno', $request->id)
            ->update([
                'changed_at' => date('Y-m-d H:i:s'),
                'loan' => $request->loan,
            ]);

        $snzFlag = 'fail';
        if ($isUpdated) {
            $snzFlag = 'success';
            UserInfoLog::insert([
                "userid" => $request->id,
                "adminid" => auth()->user()->id,
                "ip_addr" => Session::get('ip_addr'),
                "old_info" => $old_info,
                "new_info" => $new_info
            ]);

            if ($request->isStore != $userinfo->isStore) {
                $arrSubUserId = [];
                getSubUserId($userinfo, $arrSubUserId);
                if (!empty($arrSubUserId)) {
                    // 매장밑에 매장을 둘수 없다
                    if ($request->isStore == 1) {
                        User::whereIn('id', $arrSubUserId)
                            ->where('isStore', '!=', 1)
                            ->update(['isStore' => 2]);
                    } else {
                        getSubUserId2($userinfo, $arrSubUserId);
                        User::whereIn('id', $arrSubUserId)
                            ->update(['isStore' => 0]);
                    }
                }
            }
        }

        return back()->with('result', $snzFlag);
    }

    public function message_view(Request $request)
    {
        $tempRecord = SvcAskTemp::orderBy('created_at', 'desc')->get();
        $userinfo = User::where('id', $request->id)->first();
        return view('user.message', ['beforeinfo' => Session::get('form-message'), 'tempRecord' => $tempRecord, 'userid' => $userinfo->userid]);
    }

    public function message_proc(Request $request)
    {
        $userinfo = User::where('userid', $request->userid)->first();
        if (!empty($userinfo)) {
            $arrUserIds[] = $userinfo->id;
            if ($request->include)
                getSubUserId($userinfo, $arrUserIds);
            $adminid = auth()->user()->id;
            foreach ($arrUserIds as $userno) {
                SvcMsg::insert([
                    'adminid' => $adminid,
                    'userid' => $userno,
                    'importance' => $request->importance,
                    'title' => $request->title,
                    'content' => $request->txt_content,
                ]);
            }
            return back()->with('result', 'success')
                ->with('form-message', $request->all());
        }
        return back()->with('result', 'fail')
            ->with('form-message', $request->all());
    }

    public function charge_view(Request $request)
    {
        $user = User::where('id', $request->id)
            ->first();
        if (!empty($user)) {
            return view('user.charge', ['user' => $user, 'live' => -1]);
        }
        return 'Access denied';
    }

    public function charge_proc(Request $request)
    {
        $request->amount = intval($request->amount);
        if ($request->amount <= 0)
            return back()->with('result', 'fail')
                ->with('form-charge', $request->all());

        if ($request->admin_pwd != auth()->user()->password_show)
            return back()->with('result', 'pass')
                ->with('form-charge', $request->all());

        $userinfo = User::where('id', $request->id)
            ->first();

        $nUserMoney = $userinfo->procinfo->money;
        if (config('app.no_mini') == 1) {
            $nUserMoney += $userinfo->procinfo->bpoint;
            $bs = new BSGameController;
            if ($bs->procCharge($userinfo, $request->amount)) {
                PayCharge::insert([
                    "money" => $request->amount,
                    "userid" => $request->id,
                    "user_money" => $request->amount + $nUserMoney,
                    "verified" => 2,
                    "updateBy" => auth()->user()->id,
                    "group_type" => 2,
                    "memo" => '',
                    "store" => $userinfo->isStore,
                    "updated_at" => date("Y-m-d H:i:s")
                ]);
            }
        } else {
            DB::beginTransaction();
            try {
                PayCharge::insert([
                    "money" => $request->amount,
                    "userid" => $request->id,
                    "user_money" => $request->amount + $nUserMoney,
                    "verified" => 2,
                    "updateBy" => auth()->user()->id,
                    "group_type" => 2,
                    "memo" => '',
                    "store" => $userinfo->isStore,
                    "updated_at" => date("Y-m-d H:i:s")
                ]);

                UserListProc::where('userno', $request->id)
                    ->update(['money' => DB::raw('money + ' . $request->amount),
                        'charge_at' => date("Y-m-d H:i:s")]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();

                return back()->with('result', 'fail')
                    ->with('form-charge', $request->all());
            }
        }
        return back()->with('result', 'success')
            ->with('form-charge', $request->all());
    }

    public function excharge_view(Request $request)
    {
        $userinfo = User::where('id', $request->id)
            ->first();
        if (!empty($userinfo)) {
            return view('user.excharge', ['user' => $userinfo, 'live' => -1]);
        }
    }

    public function excharge_proc(Request $request)
    {
        $request->amount = intval($request->amount);
        if ($request->amount <= 0)
            return back()->with('result', 'fail')
                ->with('form-excharge', $request->all());

        if ($request->admin_pwd != auth()->user()->password_show)
            return back()->with('result', 'pass')
                ->with('form-excharge', $request->all());

        $userinfo = User::where('id', $request->id)
            ->first();

        $nUserMoney = $userinfo->procinfo->money;
        if (config('app.no_mini') == 1) {
            $nUserMoney += $userinfo->procinfo->bpoint;
            $bs = new BSGameController;
            $nAmount = $request->amount;
            // 환전요청하면 게임머니가 money로 전환된다
            if ($nAmount > 0 && $nAmount > $userinfo->procinfo->money) {
                $balance = $nAmount - $userinfo->procinfo->money;
                if (!$bs->procExcharge($userinfo, $balance))
                    return back()->with('result', 'fail')
                        ->with('form-excharge', $request->all());
                $userinfo = User::where('id', $userinfo->id)
                    ->first();
            }
        }

        if ($userinfo->procinfo->money < $request->amount)
            return back()->with('result', 'fail')
                ->with('form-excharge', $request->all());

        if (UserListProc::where('userno', $request->id)
                ->where('money', '>=', $request->amount)
                ->update(['money' => DB::raw('money - ' . $request->amount),
                    'excharge_at' => date("Y-m-d H:i:s")]) > 0) {
            PayExcharge::insert([
                "money" => $request->amount,
                "userid" => $request->id,
                "user_money" => $nUserMoney - $request->amount,
                "verified" => 2,
                "updateBy" => auth()->user()->id,
                "bank_detail" => $userinfo->bankname . ' ' . $userinfo->banknumber . ' ' . $userinfo->bankmaster,
                "group_type" => 2,
                "memo" => "",
                "store" => $userinfo->isStore,
                "updated_at" => date("Y-m-d H:i:s")
            ]);
        } else {
            return back()->with('result', 'fail')
                ->with('form-excharge', $request->all());
        }

        return back()->with('result', 'success')
            ->with('form-excharge', $request->all());
    }

    public function bets_view(Request $request)
    {
        $user = User::where('rolecode', '0003')
            ->where('id', $request->id)
            ->first();

        $date_today = date('Y-m-d');
        $date_start = $request->start_date ?? $date_today;
        $casino_gameid = $request->casino_gameid ?? '';
        $slot_gameid = $request->slot_gameid ?? '';
        $mini_gameid = $request->mini_gameid ?? '';

        $pickRecord = array();
        $betRecord = array();

        if ($casino_gameid != '' || $slot_gameid != '') {
            $live = 1;
            if ($slot_gameid != '') {
                $code = $slot_gameid > 0 ? $slot_gameid : 0;
                $live = 0;
            } else if ($casino_gameid != '') {
                $code = $casino_gameid > 0 ? $casino_gameid : 0;
            }

            $arrGameName = array($code);
            if (getPrevDate($date_start)) {
                $suminfo = collect(DB::select(sprintf("EXEC %s.getBSBetSumMonth @Start='%s 00:00:00', @End='%s 23:59:59', @Game=%d, @Live=%d, @User='%s'",
                    config('global.prefix'), $date_start, $date_start, $code, $live, $request->id)))->first();
                $betRecord = collect(DB::select(sprintf("EXEC %s.getBSBetMonth @Start='%s 00:00:00', @End='%s 23:59:59', @Game=%d, @Live=%d, @User=%d",
                    config('global.prefix'), $date_start, $date_start, $code, $live, $request->id)))->paginate(14);
            } else {
                $suminfo = collect(DB::select(sprintf("EXEC %s.getMiniBetSumAll @Start='%s 00:00:00', @End='%s 23:59:59', @Game='%s', @User=%d",
                    config('global.prefix'), $date_start, $date_start, $code ?? '', $request->id)))->first();
                $betRecord = collect(DB::select(sprintf("EXEC %s.getMiniBetAll @Start='%s 00:00:00', @End='%s 23:59:59', @Game='%s', @User=%d",
                    config('global.prefix'), $date_start, $date_start, implode(',', $arrGameName), $request->id)))->paginate(14);
            }
            $pickRecord = BSGameContent::where('thirdPartyCode', $code)
                ->where('c', '!=', 'LIVE')
                ->get();
        } else if ($mini_gameid != '') {
            $cateinfo = MiniGameCategory::where('gameid', $mini_gameid)->first();
            if (!empty($cateinfo)) {
                $code = $cateinfo->game_code;
                $pickRecord = $cateinfo->pickinfo;
            }
            $arrGameName = array($code);

            if (getPrevDate($date_start)) {
                $suminfo = collect(DB::select(sprintf("EXEC %s.getMiniBetSumMonth @Start='%s 00:00:00', @End='%s 23:59:59', @Game='%s', @User=%d",
                    config('global.prefix'), $date_start, $date_start, $code ?? '', $request->id)))->first();
                $betRecord = collect(DB::select(sprintf("EXEC %s.getMiniBetMonth @Start='%s 00:00:00', @End='%s 23:59:59', @Game='%s', @User=%d",
                    config('global.prefix'), $date_start, $date_start, implode(',', $arrGameName), $request->id)))->paginate(14);
            } else {
                $suminfo = collect(DB::select(sprintf("EXEC %s.getMiniBetSumAll @Start='%s 00:00:00', @End='%s 23:59:59', @Game='%s', @User=%d",
                    config('global.prefix'), $date_start, $date_start, $code ?? '', $request->id)))->first();
                $betRecord = collect(DB::select(sprintf("EXEC %s.getMiniBetAll @Start='%s 00:00:00', @End='%s 23:59:59', @Game='%s', @User=%d",
                    config('global.prefix'), $date_start, $date_start, implode(',', $arrGameName), $request->id)))->paginate(14);
            }
        }

        $data = array(
            'userid' => $request->id,
            'date_start' => $date_start,
            'casino_gameid' => $casino_gameid,
            'slot_gameid' => $slot_gameid,
            'mini_gameid' => $mini_gameid,
            'sum_bet' => $suminfo->sum_bet ?? 0,
            'sum_win' => $suminfo->sum_win ?? 0,
            'sum_fee' => $suminfo->sum_fee ?? 0,
            'betRecord' => $betRecord,
            'pickRecord' => $pickRecord,
        );
        $data['userinfo'] = $user;
        $data['casinoRecord'] = BSGameCategory::where('is_live', 1)->orderByDesc('order_no')->get();
        $data['slotRecord'] = BSGameCategory::where('is_live', 0)->orderByDesc('order_no')->get();
        $data['miniRecord'] = MiniGameCategory::orderBy('show_index')->get();

        return view('user.bets', $data);
    }

    public function login_log_view(Request $request)
    {
        $data = array();
        $data['search_opt'] = $request->search_opt ?? '';
        $data['search_text'] = $request->search_text ?? '';
        $tempQuery = UserLoginLog::with(['userinfo' => function ($query) {
            $query->select('id', 'userid', 'nickname', 'user_color', 'rolecode');
        }])
            ->whereHas('userinfo', function ($query) {
                $query->where('rolecode', '0003');
            });
        if ($request->id != '') {
            $tempQuery->where('userid', $request->id);
        } else {
            if (!empty($request->search_text)) {
                switch ($data['search_opt']) {
                    case 1:
                        $searchUser = User::where('userid', $request->search_text)
                            ->select('id')
                            ->first();
                        if (!empty($searchUser))
                            $tempQuery->where('userid', $searchUser->id);
                        else
                            $tempQuery->where('userid', 0);
                        break;
                    case 2:
                        $tempQuery->where('ip_addr', $request->search_text);
                        break;
                    case 3:
                        $tempQuery->where('domain', $request->search_text);
                        break;
                }
            }

        }

        $logList = $tempQuery->orderBy('created_at', 'desc')
            ->paginate(20);
        $data['logRecord'] = $logList;
        return view('user.login_log', $data);
    }

    public function info_log_view(Request $request)
    {

        $data = array();
        $data['id'] = $request->id ?? '';
        $data['search_text'] = '';
        $selectedUser = User::where('id', $data['id'])->first();
        if (!empty($selectedUser)) {
            $data['search_text'] = $selectedUser->userid;
        }
        $data['start_date'] = $request->start_date ?? date('Y-m-d') . ' to ' . date('Y-m-d');
        $objDate = getDateFromRangeDate($data['start_date']);
        $data['search_text'] = $request->search_text ?? $data['search_text'];
        $tempQuery = UserInfoLog::where('created_at', '>=', $objDate[0] . ' 00:00:00')
            ->where('created_at', '<=', $objDate[1] . ' 23:59:59');
        if ($data['search_text'] != '') {
            $searchUser = User::where('userid', $request->search_text)
                ->first();
            if (!empty($searchUser))
                $tempQuery->where('userid', $searchUser->id);
            else
                $tempQuery->where('userid', 0);
        } else if ($data['id'] != '') {
            $tempQuery->where('userid', $request->id);
        }

        $logList = $tempQuery->orderBy('created_at', 'desc')
            ->paginate(20);
        $data['logRecord'] = $logList;
        return view('user.info_log', $data);
    }

    public function set_account_enable(Request $request)
    {
        if ($request->all == 1) {
            $arrSubUserId = array();
            array_push($arrSubUserId, $request->id);
            $userinfo = User::where('id', $request->id)
                ->where('rolecode', '0003')
                ->first();
            getSubUserId($userinfo, $arrSubUserId);
            User::whereIn('id', $arrSubUserId)
                ->where('rolecode', '0003')
                ->update(['verified' => 1, 'login_fail' => 0]);
        } else {
            User::where('id', $request->id)
                ->where('rolecode', '0003')
                ->update(['verified' => 1, 'login_fail' => 0]);
        }

        return Redirect::back();
    }

    public function set_account_pending(Request $request)
    {
        User::where('id', $request->id)
            ->where('rolecode', '0003')
            ->update(['verified' => 2]);

        return Redirect::back();
    }

    public function set_account_disable(Request $request)
    {
        if ($request->all == 1) {
            $arrSubUserId = array();
            array_push($arrSubUserId, $request->id);
            $userinfo = User::where('id', $request->id)
                ->where('rolecode', '0003')
                ->first();
            getSubUserId($userinfo, $arrSubUserId);

            $iMax = count($arrSubUserId);
            for ($i = 0; $i < $iMax; $i += 100) {
                $arrTemp = array_slice($arrSubUserId, $i, 100);
                User::whereIn('id', $arrTemp)
                    ->where('rolecode', '0003')
                    ->update(['verified' => 3]);
            }

        } else {
            User::where('id', $request->id)
                ->where('rolecode', '0003')
                ->update(['verified' => 3]);
        }
        return Redirect::back();
    }

    public function reset_login_fail_cnt(Request $request)
    {
        User::where('id', $request->id)
            ->update(['login_fail' => 0]);

        return Redirect::back();
    }

    public function remove_user(Request $request)
    {
        $userinfo = UserListProc::where('userno', $request->id)
            ->first();

        if (!empty($userinfo)) {
            if ($userinfo->money >= 1 || $userinfo->point >= 1
                || $userinfo->ximax_point >= 1 || $userinfo->ban_point >= 1
                || $userinfo->plus_point >= 1 || $userinfo->sg_point >= 1 || $userinfo->fail_point >= 1
                || $userinfo->ag_point >= 1 || $userinfo->kt_point >= 1
                || $userinfo->ppk_point >= 1) {
                return Redirect::back()
                    ->withErrors(['error' => '보유머니 또는 포인트가 남아 잇습니다.']);
            }

            $subUsers = User::where('joinid', $request->id)
                ->where('rolecode', '0003')
                ->get();
            if (count($subUsers) > 0) {
                return Redirect::back()
                    ->withErrors(['error' => '하위 유저들을 삭제해야 합니다.']);
            }

            $userid = $request->id;
            DB::statement(sprintf("EXEC %s.deleteUser @User=%d", 'sphinx', $userid));
        }

        return Redirect::back();
    }

    public function edit_all_allot_rate(Request $request)
    {
        $data['userinfo'] = User::where('id', $request->userid)
            ->select('id', 'userid', 'nickname')
            ->first();

        $data['gameRecord'] = MiniGameCategory::orderBy('show_index', 'asc')
            ->get();
        $data['rateinfo'] = MiniRate::where('userid', $request->userid)
            ->first();

        return view('user.rate_bet', $data);
    }

    public function edit_all_allot_rate_proc(Request $request)
    {
        $log_content = '';
        $dbPrefix = config('global.prefix');
        $arrSubUserId = array();

        $typeRecord = MiniRateList::all()->toArray();
        $filter_key = array_column($typeRecord, 'game_name');
        $filter_db = array_column($typeRecord, 'rate_name');
        $post_data = $request;

        array_push($arrSubUserId, $request->userid);
        $userinfo = User::where('id', $request->userid)
            ->where('rolecode', '0003')
            ->select('id', 'masterid', 'joinid')
            ->first();
        getSubUserId($userinfo, $arrSubUserId);

        $kMax = count($filter_db);
        for ($k = 0; $k < $kMax; $k++) {
            $gameRecord = MiniGameCategory::where('game_code', 'like', $filter_key[$k] . '%')
                ->get();
            foreach ($gameRecord as $game) {
                $filter_data = '';
                $game_code = $game->game_code;

                $temp_data_key = array_keys($post_data[$game_code]);
                $temp_data_val = array_values($post_data[$game_code]);

                $indexMax = count($temp_data_val);
                for ($index = 0; $index < $indexMax; $index++) {
                    if ($temp_data_val[$index] != '') {
                        $filter_data = $filter_data . $filter_db[$k] . '.[' . $temp_data_key[$index] . '] = ' . $temp_data_val[$index] . ',';

                        $log_content = $log_content . $game->game_name . ':' . $temp_data_key[$index] . ':' . $temp_data_val[$index];
                        $log_content = $log_content . ' ';
                    }
                }

                $db = strtolower($filter_key[$k]) . 'id';

                if ($filter_data != '') {
                    $userlist = implode(",", $arrSubUserId);

                    $filter_data = substr($filter_data, 0, -1);
                    $sql = sprintf('UPDATE %s.%s SET %s FROM %s.%s JOIN %s.%s ON %s.%s.%s = %s.%s.%s WHERE %s.%s.userid IN (%s)',
                        $dbPrefix, $filter_db[$k], $filter_data, $dbPrefix, $filter_db[$k], $dbPrefix, "mn_rate", $dbPrefix, $filter_db[$k], $db, $dbPrefix, "mn_rate", $db, $dbPrefix, "mn_rate", $userlist);

                    DB::statement($sql);
                }
            }
        }

        if ($log_content != '') {
            $arrInsert = array();
            $adminId = auth()->user()->id;
            $ip_addr = Session::get('ip_addr');
            foreach ($arrSubUserId as $user) {
                array_push($arrInsert, array(
                    'userid' => $user,
                    'adminid' => $adminId,
                    'contents' => $log_content,
                    'ip_addr' => $ip_addr
                ));
            }
            MiniRateLog::insert($arrInsert);
        }
        return back();
    }

    public function edit_allot_rate_logs(Request $request)
    {
        $data['userinfo'] = User::where('id', $request->userid)
            ->select('id', 'userid', 'nickname')
            ->first();
        $data['logRecord'] = MiniRateLog::where('userid', $request->userid)
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('user.rate_bet_log', $data);
    }

    public function edit_all_comm_rates(Request $request)
    {
        $user = User::where('id', $request->userid)
            ->first();
        $data['userinfo'] = $user;
        $subRecord = User::where('joinid', $request->id)
            ->get();
        $arrSubUserId = array_column($subRecord->toArray(), 'id');

        $data['all'] = $request->all ?? 0;
        $data['gameType'] = $request->code ?? '';
        if ($request->code != '') {
            $data['gameRecord'] = MiniGameCategory::where('game_type', $request->code)
                ->orderBy('show_index')
                ->get();
        } else {
            $data['gameRecord'] = MiniGameCategory::orderBy('show_index')
                ->get();
        }
        $data['liveRecord'] = BSGameCategory::where('is_live', 1)
            ->orderByDesc('order_no')
            ->get();
        $data['slotRecord'] = BSGameCategory::where('is_live', 0)
            ->orderByDesc('order_no')
            ->get();

        $data['rol_mini'] = RollingMini::where('userid', $request->userid)
            ->first();
        $rol_bs = RollingBS::where('userid', $request->userid)
            ->first();
        $data['rol_bs'] = $rol_bs;
        if (!empty($arrSubUserId)) {
            $data['userRecord'] = RollingBS::whereIn('userid', $arrSubUserId)
                ->where(function ($query) use ($rol_bs) {
                    $query->where('live', '>', $rol_bs->live);
                    $query->orWhere('live_lose', '>', $rol_bs->live_lose);
                    $query->orWhere('slot', '>', $rol_bs->slot);
                    $query->orWhere('slot_lose', '>', $rol_bs->slot_lose);
                })
                ->get();
        } else {
            $data['userRecord'] = array();
        }

        return view('user.rate_point', $data);
    }

    public function edit_all_comm_rates_proc(Request $request)
    {
        $arrGameFail = array();
        $arrUpdatePwb = $arrUpdateSlot = $arrUpdateEvo = array();
        $log_content = '';
        $isFail = false;

        $userinfo = User::where('id', $request->userid)
            ->first();
        $gameRecord = MiniGameCategory::all();

        $rol_mini = RollingMini::where('userid', $request->userid)->first();
        $userRecord = User::where('joinid', $request->userid)->get();
        $arrSubUsers = array_column($userRecord->toArray(), 'id');
        foreach ($gameRecord as $game) {
            $code = $game->game_code;
            $arrGameFail[$code] = '';

            if ($userinfo->joinid != 0) {
                $rol_parent = RollingMini::where('userid', $userinfo->joinid)->first();
                $compareRol = $rol_parent[$code];
            } else {
                $compareRol = $game['push_fee'];
            }

            $rol_child = RollingMini::whereIn('userid', $arrSubUsers)
                ->selectRaw('MAX(' . $code . ') as ' . $code)
                ->first();

            if (isset($request['PWB'][$code]) && $request['PWB'][$code] != '') {
                if ($compareRol < $request['PWB'][$code]) {
                    $isFail = true;
                    $arrGameFail[$code] = '상위 업체 초과 불가, 배수: ' . floatval($compareRol) . '(%)';
                } else if (!empty($rol_child) && $rol_child[$code] > $request['PWB'][$code]) {
                    $isFail = true;
                    $arrGameFail[$code] = '하위 업체 저하 불가, 배수: ' . floatval($rol_child[$code]) . '(%)';
                } else if ($rol_mini[$code] != $request['PWB'][$code]) {
                    $log_content = $log_content . $game->game_name . ':' . $request['PWB'][$code] . ' ';
                    $arrUpdatePwb[$code] = $request['PWB'][$code];
                }
            }
        }

        if (!empty($request['BS'])) {
            $arrFeeNames = array('live', 'live_lose', 'slot', 'slot_lose');
            $arrCompare = array();

            $rol_bs = RollingBS::where('userid', $request->userid)->first();
            if ($userinfo->joinid != 0) {
                $rol_parent = RollingBS::where('userid', $userinfo->joinid)->first();
                foreach ($arrFeeNames as $name)
                    $arrCompare[$name] = $rol_parent[$name];
            } else {
                $arrCompare['live'] = 10;
                $arrCompare['live_lose'] = 100;
                $arrCompare['slot'] = 10;
                $arrCompare['slot_lose'] = 100;
            }

            foreach ($arrFeeNames as $name) {
                $rol_child = RollingBS::whereIn('userid', $arrSubUsers)
                    ->selectRaw('MAX(' . $name . ') as ' . $name)
                    ->first();

                if ($arrCompare[$name] < $request['BS'][$name]) {
                    $isFail = true;
                    $arrGameFail[$name] = '상위 업체 초과 불가, 배수: ' . floatval($arrCompare[$name]) . '(%)';
                } else if (!empty($rol_child) && $rol_child[$name] > $request['BS'][$name]) {
                    $isFail = true;
                    $arrGameFail[$name] = '하부 업체 저하 불가, 배수: ' . floatval($rol_child[$name]) . '(%)';
                } else if ($request['BS'][$name] != '' && $rol_bs[$name] != $request['BS'][$name]) {
                    $snzMent = '';
                    switch ($name) {
                        case 'live':
                            $snzMent = '카지노';
                            break;
                        case 'live_lose':
                            $snzMent = '카지노루징';
                            break;
                        case 'slot':
                            $snzMent = '슬롯';
                            break;
                        case 'slot_lose':
                            $snzMent = '슬롯루징';
                            break;
                    }
                    $log_content = $log_content . $snzMent . ':' . $request['BS'][$name] . ' ';
                    $arrUpdateEvo[$name] = $request['BS'][$name];
                }
            }
        }

        if ($isFail) {
            return back()->with('result', 'fail')
                ->with('form-rolling', $request->all())
                ->with('error', $arrGameFail);
        }

        if (!empty($arrUpdatePwb)) {
            $iMax = count($arrUpdatePwb);
            for ($i = 0; $i < $iMax; $i += 60) {
                $arrTemp = array_slice($arrUpdatePwb, $i, 60);
                RollingMini::where('userid', $request->userid)
                    ->update($arrTemp);
            }
        }
        if (!empty($arrUpdateEvo)) {
            $iMax = count($arrUpdateEvo);
            for ($i = 0; $i < $iMax; $i += 60) {
                $arrTemp = array_slice($arrUpdateEvo, $i, 60);
                RollingBS::where('userid', $request->userid)
                    ->update($arrTemp);
            }
        }

        if ($log_content != '') {
            RollingLog::insert(['userid' => $request->userid,
                'adminid' => auth()->user()->id,
                'contents' => $log_content,
                'ip_addr' => Session::get('ip_addr')]);
        }

        return back()->with('result', 'success')
            ->with('form-rolling', $request->all())
            ->with('error', $arrGameFail);
    }

    public function edit_comm_rate_logs(Request $request)
    {
        $data['userinfo'] = User::where('id', $request->userid)
            ->select('id', 'userid', 'nickname')
            ->first();
        $data['logRecord'] = RollingLog::where('userid', $request->userid)
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('user.rate_point_log', $data);
    }

    public function edit_bet_push_view(Request $request)
    {
        $data['userinfo'] = User::where('id', $request->userid)
            ->select('id', 'userid', 'nickname')
            ->first();

        $pushinfo = MiniGamePushTemp::where('userid', $request->userid)
            ->orderBy('created_at', 'desc')
            ->first();
        if (empty($pushinfo)) {
            $pushinfo = MiniGamePush::where('userid', $request->userid)
                ->first();
        }
        $bsPushInfo = BSGamePushTemp::where('userid', $request->userid)
            ->orderBy('created_at', 'desc')
            ->first();
        $data['bsPushInfo'] = $bsPushInfo;
        $data['pushinfo'] = $pushinfo;
        $data['gameRecord'] = MiniGameCategory::orderBy('show_index', 'asc')
            ->get();

        return view('user.rate_push', $data);
    }

    public function edit_bet_push_proc(Request $request)
    {
        $arrSubUserId = array();
        array_push($arrSubUserId, $request->userid);
        if ($request->apply_all == 1) {
            $userinfo = User::where('id', $request->userid)
                ->where('rolecode', '0003')
                ->first();
            getSubUserId($userinfo, $arrSubUserId);
        }

        $arrUpdateData = array();
        $snzLogs = sprintf('[하부적용: %d, 손익: %s] ', $request->apply_all, $request->push_limit);
        $nLimit = str_replace(",", "", $request->push_limit);
        $arrUpdateData['push_limit'] = $nLimit;
        $gameRecord = MiniGameCategory::all();
        foreach ($gameRecord as $game) {
            if ($request[$game->game_code] != '' && $request[$game->game_code] >= 0) {
                $arrUpdateData[$game->game_code] = $request[$game->game_code];
                $snzLogs = $snzLogs . $game->game_name . ':' . $request[$game->game_code] . '%, ';
            }
        }
        if (empty($arrUpdateData))
            return back()->with('result', 'fail');

        $bulkInsertData = array();
        foreach ($arrSubUserId as $user) {
            $arrUpdateData['userid'] = $user;
            $bulkInsertData[] = $arrUpdateData;
        }
        $iMax = count($bulkInsertData);
        for ($i = 0; $i < $iMax; $i += 5) {
            $arrTemp = array_slice($bulkInsertData, $i, 5);
            MiniGamePushTemp::insert($arrTemp);
        }
        MiniGamePushLog::insert(['userid' => $request->userid,
            'adminid' => auth()->user()->id,
            'push_limit' => $nLimit,
            'contents' => $snzLogs,
            'is_under' => $request->apply_all,
            'ip_addr' => Session::get('ip_addr')]);

        return back()->with('result', 'success');
    }

    public function edit_bet_push_logs(Request $request)
    {
        $data['userinfo'] = User::where('id', $request->userid)
            ->select('id', 'userid', 'nickname')
            ->first();
        $data['logRecord'] = MiniGamePushLog::where('userid', $request->userid)
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('user.rate_push_log', $data);
    }

    public function initPushCasino(Request $request)
    {
        $userRecord = User::where('rolecode', '0003')
            ->get();
        foreach ($userRecord as $record) {
            BSGamePush::insert(['userid' => $record->id]);
        }
    }

    public function editPushCasino(Request $request)
    {
        $userInfo = User::where('id', $request->userid)
            ->select('id', 'userid', 'nickname')
            ->first();
        $gameRecord = BSGameCategory::where('is_live', 1)
            ->get();
        $pushInfo = BSGamePushTemp::where('userid', $request->userid)
            ->orderBy('created_at', 'desc')
            ->first();
        $data = array('pushInfo' => $pushInfo,
            'userInfo' => $userInfo,
            'gameRecord' => $gameRecord);

        return view('user.push.live', $data);
    }

    public function editPushCasinoProc(Request $request)
    {
        $arrSubUserId = array();
        $arrSubUserId[] = $request->userid;
        if ($request->apply_all == 1) {
            $userInfo = User::where('id', $request->userid)
                ->where('rolecode', '0003')
                ->first();
            getSubUserId($userInfo, $arrSubUserId);
        }
        $arrPushData = array(
            'live_limit' => str_replace(',', '', $request->live_limit),
        );
        $snzLog = sprintf("하부적용:%d, 손익:%d, ", $request->apply_all, $request->live_limit);
        $gameRecord = BSGameCategory::where('is_live', 1)
            ->get();
        foreach ($gameRecord as $record) {
            $arrPushData[$record->thirdPartyInfo] = $request[$record->thirdPartyCode];
            $snzLog = $snzLog . sprintf("%s:%d, ", $record->tKR, $request[$record->thirdPartyCode]);
        }
        foreach ($arrSubUserId as $user) {
            $arrPushData['userid'] = $user;
            BSGamePushTemp::insert($arrPushData);
        }

        BSGamePushLog::insert(['userid' => $request->userid,
            'adminid' => auth()->user()->id,
            'contents' => $snzLog,
            'is_under' => $request->apply_all,
            'ip_addr' => Session::get('ip_addr')
        ]);

        return back()->with('result', 'success');
    }

    public function editPushCasinoLog(Request $request)
    {
        $data['userinfo'] = User::where('id', $request->userid)
            ->first();
        $data['logRecord'] = BSGamePushLog::where('userid', $request->userid)
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('user.push.log', $data);
    }

    public function user_bet_limits_view(Request $request)
    {
        $data['userinfo'] = User::where('id', $request->userid)
            ->select('id', 'userid', 'nickname')
            ->first();
        $data['gameRecord'] = MiniGameCategory::orderBy('show_index', 'asc')
            ->get();

        return view('user.bet_limit', $data);
    }

    public function user_bet_limits_proc(Request $request)
    {
        $snzLogs = '';
        $arrUpdateData = array();
        $gameRecord = MiniGameCategory::all();
        foreach ($gameRecord as $game) {
            if ($request[$game->game_code] > 0) {
                $arrUpdateData[$game->game_code] = $request[$game->game_code];

                $snzLimitName = MiniGameBetLimit::where('limitid', $request[$game->game_code])
                    ->first()->name;
                $snzLogs = $snzLogs . '[' . $game->game_name . ']:' . $snzLimitName . ' ';
            } else {
                $arrUpdateData[$game->game_code] = 0;
            }
        }

        MiniBetLimit::where('userid', $request->userid)
            ->update($arrUpdateData);

        MiniBetLimitLog::insert(['userid' => $request->userid,
            'adminid' => auth()->user()->id,
            'contents' => $snzLogs,
            'ip_addr' => Session::get('ip_addr')]);

        return back();
    }

    public function user_bet_limits_logs(Request $request)
    {
        $data['userinfo'] = User::where('id', $request->userid)
            ->select('id', 'userid', 'nickname')
            ->first();
        $data['logRecord'] = MiniBetLimitLog::where('userid', $request->userid)
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('user.bet_limit_log', $data);
    }

    public function user_edit_path_view(Request $request)
    {
        $user = User::where('id', $request->userid)
            ->first();
        $headinfo = $user->headinfo;
        if ($headinfo == null)
            $master = $user->userid;
        else
            $master = $headinfo->userid;
        $data['master'] = $master;
        $data['userid'] = $request->userid;

        return view('user.path', $data);
    }

    public function user_edit_path_proc(Request $request)
    {
        $siteinfo = SiteSetting::take(1)->first();
        if ($siteinfo->is_site_close == 1) {
            $parent = $request->parent_username;
            $userid = $request->userid;
            $arrSubUserId = array();
            array_push($arrSubUserId, $userid);
            $userinfo = User::where('id', $userid)
                ->where('rolecode', '0003')
                ->first();
            getSubUserId($userinfo, $arrSubUserId);

            $iMax = count($arrSubUserId);
            for ($i = 0; $i < $iMax; $i += 100) {
                $arrTemp = array_slice($arrSubUserId, $i, 100);
                if ($parent == "") {
                    User::whereIn('id', $arrTemp)
                        ->update(['masterid' => $userid]);
                    User::where('id', $userid)
                        ->update([
                            'joinid' => 0,
                            'masterid' => 0
                        ]);
                } else {
                    $data = User::where('userid', $parent)
                        ->where('rolecode', '0003')
                        ->first();
                    if ($data != null) {
                        if ($data->masterid == 0) {
                            $data->masterid = $data->id;
                        }

                        User::where('id', $userid)
                            ->update(['joinid' => $data->id]);
                        User::whereIn('id', $arrTemp)
                            ->update(['masterid' => $data->masterid]);
                    } else {
                        return back()->with('ment', '상부아이디가 존재하지 않습니다.');
                    }
                }
            }
            return back()->with('ment', '소속이동 되었습니다.');
        }
        return back()->with('ment', '소속이동은 점검중일때만 가능합니다.');
    }

    public function set_only_domain(Request $request)
    {
        $user = User::where('id', $request->userid)
            ->first();
        $domainRecord = SiteDomain::all();
        return view("user.domain", ['user' => $user, 'domain' => $domainRecord]);
    }

    public function set_only_domain_proc(Request $request)
    {
        $user = User::where('id', $request->userid)
            ->first();
        $arrSubUserId = array();
        getSubUserId($user, $arrSubUserId);
        $arrSubUserId[] = $user->id;
        User::whereIn('id', $arrSubUserId)
            ->update(['domainid' => $request->domain]);
        return back();
    }

    public function withdraw_charge_view(Request $request)
    {

    }

    public function rolling_logs_view(Request $request)
    {

    }

    public function casinoCharge(Request $request)
    {
        $user = User::where('id', $request->id)
            ->first();
        if (!empty($user)) {
            $live = 1;
            $arrType = ['slot', 'casino'];
            if ($live >= 0) {
                $game = new BSGameController;
                $keep = $game->getBalance($live, $user);
            }
            return view('user.charge', ['user' => $user, 'keep' => $keep[$arrType[$live]]['sum'] ?? 0, 'live' => $live]);
        }
        return 'Access denied';
    }

    public function casinoChargeProc(Request $request)
    {
        $request->amount = intval($request->amount);
        if ($request->amount <= 0)
            return back()->with('result', 'fail')
                ->with('form-charge', $request->all());

        if ($request->admin_pwd != auth()->user()->password_show)
            return back()->with('result', 'pass')
                ->with('form-charge', $request->all());

        $userinfo = User::where('id', $request->id)
            ->first();

        if ($userinfo->procinfo->money < $request->amount) {
            return back()->with('result', 'fail')
                ->with('form-charge', $request->all());
        }

        $game = new BSGameController;
        if ($game->deposit(1, $userinfo, $request->amount)) {
            BSMeMoney::insert([
                'userid' => $userinfo->id,
                'money' => $request->amount,
                'result' => $userinfo->procinfo->money,
                'is_deposit' => 1,
                'thirdPartyCode' => 0,
                'is_admin' => 1,
                'is_live' => 1
            ]);
            return back()->with('result', 'success')
                ->with('form-charge', $request->all());
        }
        return back()->with('result', 'fail')
            ->with('form-charge', $request->all());
    }

    public function casinoExcharge(Request $request)
    {
        $user = User::where('id', $request->id)
            ->first();
        if (!empty($user)) {
            $live = 1;
            $arrType = ['slot', 'casino'];
            if ($live >= 0) {
                $game = new BSGameController;
                $keep = $game->getBalance($live, $user);
            }
            return view('user.excharge', ['user' => $user, 'keep' => $keep[$arrType[$live]]['sum'] ?? 0, 'live' => $live]);
        }
        return 'Access denied';
    }

    public function casinoExchargeProc(Request $request)
    {
        $request->amount = intval($request->amount);
        if ($request->amount <= 0)
            return back()->with('result', 'fail')
                ->with('form-charge', $request->all());

        if ($request->admin_pwd != auth()->user()->password_show)
            return back()->with('result', 'pass')
                ->with('form-charge', $request->all());

        $userinfo = User::where('id', $request->id)
            ->first();

        $game = new BSGameController;
        if ($game->withdrawal(1, $userinfo, $request->amount)) {
            BSMeMoney::insert([
                'userid' => $userinfo->id,
                'money' => $request->amount,
                'result' => $userinfo->procinfo->money,
                'is_deposit' => 0,
                'thirdPartyCode' => 0,
                'is_admin' => 1,
                'is_live' => 1
            ]);
            return back()->with('result', 'success')
                ->with('form-charge', $request->all());
        }
        return back()->with('result', 'fail')
            ->with('form-charge', $request->all());
    }

    public function userinfo(Request $request)
    {
        $user = User::where('id', $request->id)
            ->first();
        if (empty($user))
            return '잘못된 접근입니다.';

        $balance = 0;
        $column = '';
        switch (config('app.api_type')) {
            case 'ximax':
                $column = 'ximax_point';
                $ximax = new XimaxController;
                $balance = $ximax->getAccountBalanceAllWithTimeout($ximax->getGameUserId($user));
                if ($user->procinfo->boss_pra2 == 1) {
                    $tempMny = SiteSetting::take(1)->first()->boss_temp;
                    $balance = $balance - $tempMny;
                }
                break;
            case 'sg':
                $column = 'sg_point';
                $sg = new SGController;
                $balance = $sg->getAccountBalance($user);
                break;
            case 'kten':
                $column = 'kt_point';
                $kten = new KtenController;
                $balance = $kten->getAccountBalance($user->userid);
                break;
            case 'sdapi':
                $column = 'sd_point';
                $sd = new SdapiController;
                $sd->createAccount($user);
                $user->refresh();
                $balance = $sd->getAccountBalance($user->procinfo->sd_user, $user->procinfo->sd_token);
                break;
            case 'cxapi':
                // 머니조회할 필요 없다.
                break;
            default:
                break;
        }
        if ($column != '') {
            UserListProc::where('userno', $user->id)
                ->update([$column => $balance ?? 0]);
        }

        switch (config('app.slot_type')) {
            case 'plus':
                $column = 'plus_point';
                $plus = new PlusController;
                $balance = $plus->getBalance($user->userid);
                UserListProc::where('userno', $user->id)
                    ->update([$column => $balance ?? 0]);
                break;
            default:
                break;
        }
        if (config('app.evo_type') == 'boss' || config('app.pra_type') == 'boss') {
            $boss = new BossController;
            $arrInfo = $boss->getBalance($user);
            if (!is_null($arrInfo)) {
                UserListProc::where('userno', $user->id)
                    ->update(['boss_point' => $arrInfo['live'], 'pra2_point' => $arrInfo['slot']]);
            }
        }

        return view('partner.info', ['user' => $user]);
    }

    public function withdraw(Request $request)
    {
        $user = User::where('id', $request->id)
            ->where('rolecode', '0003')
            ->first();
        if (empty($user))
            return back();
        $balance = 0;
        $column = '';
        switch ($request->type) {
            case 'ximax':
                $ximax = new XimaxController;
                if ($user->procinfo->ximax_wallet != '') {
                    $column = 'ximax_point';
                    $usernick = $ximax->getGameUserId($user);
                    $res = $ximax->subtractMemberPointAll($usernick);
                    if (isset($res['memberBalance']) && $res['memberBalance'] > 0) {
                        $balance = $respose['memberBalance'];
                        if ($user->procinfo->boss_pra2 == 1) {
                            $tempMny = SiteSetting::take(1)->first()->boss_temp;
                            $balance = $balance - $tempMny;
                        }
                    }
                }
                break;
            case 'sg':
                $sg = new SGController;
                if ($user->procinfo->sg_token != '') {
                    $column = 'sg_point';
                    $amount = $sg->getAccountBalance($user);
                    if ($amount > 0 && $sg->withdrawal($user, $amount)) {
                        $balance = $amount;
                    }
                }
                break;
            case 'sd':
                $sd = new SdapiController;
                $usercode = $user->procinfo->sd_user;
                $token = $user->procinfo->sd_token;
                if ($usercode != '') {
                    $column = 'sd_point';
                    $amount = $sd->getAccountBalance($usercode, $token);
                    if ($amount > 0 && $sd->subtractMemberPoint($amount, $usercode, $token)) {
                        $balance = $amount;
                    }
                }
                break;
            case 'kten':
                $kten = new KtenController;
                if ($user->procinfo->kt_user != '') {
                    $column = 'kt_point';
                    $amount = $kten->getAccountBalance($user->userid);
                    if ($amount > 0 && $kten->subtractMemberPoint($amount, $user->userid)) {
                        $balance = $amount;
                    }
                }
                break;
            case 'cx':
                UserListProc::where('userno', $user->id)
                    ->where('cx_point', '>', 0)
                    ->update([
                        'money' => DB::raw("money + cx_point"),
                        'cx_point' => 0
                    ]);
                return back();
            case 'plus':
                $plus = new PlusController;
                $amount = $plus->getBalance($user->userid);
                if ($amount > 0 && $plus->withdraw($user->userid, $amount)) {
                    $column = 'plus_point';
                    $balance = $amount;
                }
                break;
            case 'boss':
                $boss = new BossController;
                if ($user->procinfo->boss_proc == 1) {
                    $column = 'boss_point';
                    $arrInfo = $boss->getBalance($user);
                    if (!is_null($arrInfo) && $arrInfo['live'] > 0 && $boss->withdrawal($user, $arrInfo['live'], 1)) {
                        $balance = $arrInfo['live'];
                    }
                }
                break;
            case 'pra2':
                $boss = new BossController;
                if ($user->procinfo->boss_proc == 1) {
                    $column = 'boss_point';
                    $arrInfo = $boss->getBalance($user);
                    if (!is_null($arrInfo) && $arrInfo['slot'] > 0 && $boss->withdrawal($user, $arrInfo['slot'], 2)) {
                        $balance = $arrInfo['slot'];
                    }
                }
                break;
            case 'fail':
                $failRecord = BSMeBan::where('uid', $user->id)
                    ->where('is_proc', 0)
                    ->get()
                    ->toArray();
                if (count($failRecord) > 0) {
                    $column = 'fail_point';
                    $arrFailIds = array_column($failRecord, 'id');
                    $sumInfo = array_sum(array_column($failRecord, 'money'));
                    BSMeBan::whereIn('id', $arrFailIds)
                        ->update(['is_proc' => 1]);
                    $balance = $sumInfo;
                }
                break;
        }

        if ($balance > 0) {
            UserListProc::where('userno', $user->id)
                ->update([
                    'money' => DB::raw("money + $balance"),
                    $column => 0
                ]);
        }
        return back();
    }
}
