<?php

namespace App\Http\Controllers;

use App\Models\BSBetFeeAll;
use App\Models\BSBetFeeDay;
use App\Models\BSGameCategory;
use App\Models\BSGamePush;
use App\Models\BSLoseInit;
use App\Models\MiniBetFeeAll;
use App\Models\MiniGameCategory;
use App\Models\MiniGamePick;
use App\Models\MiniRateBBP;
use App\Models\MiniRateBBS;
use App\Models\MiniRateCP3;
use App\Models\MiniRateCP5;
use App\Models\MiniRateCS3;
use App\Models\MiniRateCS5;
use App\Models\MiniRateDHPsa;
use App\Models\MiniRateDHPwb;
use App\Models\MiniRateEOS3;
use App\Models\MiniRateEOS5;
use App\Models\MiniRateHSP3;
use App\Models\MiniRateHSP5;
use App\Models\MiniRateHSS3;
use App\Models\MiniRateHSS5;
use App\Models\MiniRateKeno;
use App\Models\MiniRateKlayp2;
use App\Models\MiniRateKlays2;
use App\Models\MiniRateKSA;
use App\Models\MiniRateMtp3;
use App\Models\MiniRateMtp5;
use App\Models\MiniRateMts3;
use App\Models\MiniRateMts5;
use App\Models\MiniRatePsaPlus;
use App\Models\MiniRatePwbPlus;
use App\Models\MiniRateUP1;
use App\Models\MiniRateUP3;
use App\Models\MiniRateUP5;
use App\Models\MiniRateKlayp5;
use App\Models\MiniRateKlays5;
use App\Models\MiniRateXrp3;
use App\Models\MiniRateXrp5;
use App\Models\MiniRateXrs3;
use App\Models\MiniRateXrs5;
use App\Models\PayCharge;
use App\Models\PayExchange;
use App\Models\RollingBS;
use App\Models\RollingMini;
use App\Models\SitePartnerStep;
use App\Models\MiniBetLimit;
use App\Models\MiniGamePush;
use App\Models\UserCheck;
use App\Models\MiniRate;
use App\Models\MiniRatePwb;
use App\Models\MiniRatePsa;
use App\Models\User;
use App\Models\SiteSetting;
use App\Models\UserListProc;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class PartnerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list(Request $request)
    {
        $data = array();
        $data["state"] = $request["state"] ?? '';
        $data["conn"] = $request["conn"] ?? '';
        $data["last_login"] = $request["last_login"] ?? 0;
        $data["last_charged"] = $request["last_charged"] ?? 0;
        $data["search_opt"] = $request["search_opt"] ?? 0;
        $data["search_text"] = $request["search_text"] ?? '';
        $data['sort'] = $request->sort ?? 'asc';
        $data['type'] = $request->type ?? 'money';

        $data['pageType'] = 'list';

        $tempQuery = User::where('rolecode', '0003')
            ->join('sphinx.user_list_proc', 'user_list.id', '=', 'user_list_proc.userno');
        if ($data["state"] != '') {
            if ($data["state"] == 4)
                $tempQuery->where('is_evo2', 1);
            else
                $tempQuery->where('verified', $data["state"]);
        }
        if ($data["conn"] == 1)
            $tempQuery->where('request_at', '>=', DB::raw('DATEADD(second, -300, getdate())'));
        if ($data["last_login"] > 1)
            $tempQuery->where('login_at', '>=', DB::raw('DATEADD(DAY, ' . $data["last_login"] . ',getdate())'));
        if ($data["last_charged"] > 1)
            $tempQuery->where('charge_at', '>=', DB::raw('DATEADD(DAY, ' . $data["last_charged"] . ',getdate())'));
        if ($data["search_opt"] > 0 && $data["search_text"] != '') {
            $column = getUserState($data["search_opt"]);
            $tempQuery->where($column, $data["search_text"]);
        }
        switch ($data['type']) {
            case 'money':
                $snzSortType = 'money';
                break;
            case 'loan':
                $snzSortType = 'loan';
                break;
            case 'point':
                $snzSortType = 'point';
                break;
            case 'losing':
                $snzSortType = 'losing';
                break;
            case 'create':
                $snzSortType = 'created_at';
                break;
            case 'charge':
                $snzSortType = 'charge_at';
                break;
            case 'login':
                $snzSortType = 'login_at';
                break;
            case 'casino':
                $snzSortType = '(user_list_proc.ximax_point + user_list_proc.ban_point + user_list_proc.sg_point + user_list_proc.ppk_point + user_list_proc.kt_point + user_list_proc.ag_point + user_list_proc.fail_point + user_list_proc.boss_point + user_list_proc.pra2_point + user_list_proc.cx_point + user_list_proc.sd_point)';
                break;
            case 'slot':
                $snzSortType = '(user_list_proc.plus_point)';
                break;
            default:
                $snzSortType = 'money';
                break;
        }
        $tempQuery2 = clone $tempQuery;
        if (config('app.no_mini') == 1 && $data['type'] == 'money') {
            $tempQuery->orderBy(DB::raw("money + user_list_proc.ximax_point + user_list_proc.boss_point + user_list_proc.ag_point + user_list_proc.kt_point + user_list_proc.cx_point + user_list_proc.fail_point + user_list_proc.pra2_point"), ($data['sort'] == 'asc' ? 'desc' : 'asc'));
        } else {
            $tempQuery->orderBy(DB::raw($snzSortType), ($data['sort'] == 'asc' ? 'desc' : 'asc'));
        }
        $tempQuery->orderBy('id');
        //$tempRecord = $tempQuery->get();
        $userRecord = $tempQuery->paginate(25);
        $sumInfo = $tempQuery2->selectRaw('SUM(money) as sum_money, SUM(point) as sum_point,
            SUM(ximax_point + ban_point + boss_point + sg_point + ag_point + kt_point + ppk_point + sd_point + cx_point + fail_point + pra2_point) as sum_bpoint, SUM(plus_point) as sum_spoint,
            SUM(CASE WHEN losing < 0 THEN losing ELSE 0 END) as sumMLosing, SUM(CASE WHEN losing > 0 THEN losing ELSE 0 END) as sumPLosing')->first();
        $data['userRecords'] = $userRecord;
        $data['sumInfo'] = $sumInfo;
        $data['apiType'] = config('app.api_type');
        return view('partner.list', $data);
    }

    public function searchUsers(Request $request)
    {
        $data = array();
        $data["state"] = $request["state"] ?? '';
        $data["conn"] = $request["conn"] ?? '';
        $data["userid"] = $request["userid"];
        $data["last_login"] = $request["last_login"] ?? 0;
        $data["last_charged"] = $request["last_charged"] ?? 0;
        $data["search_opt"] = $request["search_opt"] ?? 0;
        $data["search_text"] = $request["search_text"] ?? '';
        $data['sort'] = $request->sort ?? 'asc';
        $data['type'] = $request->type ?? 'money';

        $data['keep_money'] = 0;
        $data['keep_point'] = 0;
        $data['pageType'] = 'search';
        $data['header'] = 'layouts.master-without-nav';
        $search_user = User::where('rolecode', '0003')
            ->where('id', $request->userid)
            ->first();
        $arrSubUserId = array($request->userid);
        getSubUserId($search_user, $arrSubUserId);

        $tempQuery = User::where('rolecode', '0003')
            ->whereIn('id', $arrSubUserId)
            ->join('sphinx.user_list_proc', 'user_list.id', '=', 'user_list_proc.userno');;
        if ($data["state"] != '')
            $tempQuery->where('verified', $data["state"]);
        if ($data["conn"] == 1)
            $tempQuery->where('request_at', '>=', DB::raw('DATEADD(second, -120, getdate())'));
        if ($data["last_login"] > 1)
            $tempQuery->where('login_at', '>=', DB::raw('DATEADD(DAY, ' . $data["last_login"] . ',getdate())'));
        if ($data["last_charged"] > 1)
            $tempQuery->where('charge_at', '>=', DB::raw('DATEADD(DAY, ' . $data["last_charged"] . ',getdate())'));

        if ($data["search_opt"] > 0 && $data["search_text"] != '') {
            $column = getUserState($data["search_opt"]);
            $tempQuery->where($column, $data["search_text"]);
        }

        $tempQuery2 = clone $tempQuery;
        $sumInfo = $tempQuery2->selectRaw('SUM(money) as sum_money, SUM(point) as sum_point,
            SUM(ximax_point + ban_point + boss_point + sg_point + ag_point + kt_point + ppk_point + sd_point + cx_point + fail_point + pra2_point) as sum_bpoint, SUM(plus_point) as sum_spoint,
            SUM(CASE WHEN losing < 0 THEN losing ELSE 0 END) as sumMLosing, SUM(CASE WHEN losing > 0 THEN losing ELSE 0 END) as sumPLosing')->first();
        if (!empty($sumInfo)) {
            $data['sumInfo'] = $sumInfo;
        }
        switch ($data['type']) {
            case 'money':
                $snzSortType = 'money';
                break;
            case 'loan':
                $snzSortType = 'loan';
                break;
            case 'point':
                $snzSortType = 'point';
                break;
            case 'losing':
                $snzSortType = 'losing';
                break;
            case 'create':
                $snzSortType = 'created_at';
                break;
            case 'charge':
                $snzSortType = 'charge_at';
                break;
            case 'login':
                $snzSortType = 'login_at';
                break;
            case 'casino':
                $snzSortType = '(user_list_proc.ximax_point + user_list_proc.ban_point + user_list_proc.sg_point + user_list_proc.ppk_point + user_list_proc.kt_point + user_list_proc.ag_point + user_list_proc.fail_point + user_list_proc.boss_point + user_list_proc.pra2_point)';
                break;
            case 'slot':
                $snzSortType = '(user_list_proc.plus_point)';
                break;
            default:
                $snzSortType = 'money';
                break;
        }
        if (config('app.no_mini') == 1 && $data['type'] == 'money') {
            $tempQuery->orderBy(DB::raw("money + user_list_proc.ximax_point + user_list_proc.boss_point + user_list_proc.ag_point + user_list_proc.kt_point + user_list_proc.fail_point + user_list_proc.cx_point + user_list_proc.pra2_point"), ($data['sort'] == 'asc' ? 'desc' : 'asc'));
        } else {
            $tempQuery->orderBy(DB::raw($snzSortType), ($data['sort'] == 'asc' ? 'desc' : 'asc'));
        }
        $tempQuery->orderBy('id');

        $userRecord = $tempQuery->paginate(25);

        $data['userRecords'] = $userRecord;
        $data['apiType'] = config('app.api_type');

        return view('partner.list', $data);
    }

    public function setEvo2(Request $request)
    {
        $val = $request->evo2 ?? 2;
        $val2 = $request->pra2 ?? 2;
        $userid = $request->userid;
        $user = User::where('id', $userid)
            ->first();
        $arrSubUserIds = array($userid);
        getSubUserId($user, $arrSubUserIds);
        for ($i = 0; $i < count($arrSubUserIds); $i += 100) {
            $arrTemp = array_slice($arrSubUserIds, $i, 100);
            if ($val != 2) {
                User::whereIn('id', $arrTemp)
                    ->update(['is_evo2' => $val]);
            }
            if ($val2 != 2) {
                User::whereIn('id', $arrTemp)
                    ->update(['is_pra2' => $val2]);
            }
        }
        return back();
    }

    public function addView(Request $request)
    {
        $userid = $request->userid;
        if ($userid > 0) {
            $parent = User::where('id', $userid)
                ->first();
            return view('partner.add', ['parent' => $parent->userid]);
        }
        return view('partner.add', ['parent' => '']);
    }

    public function addProc(Request $request)
    {
        $markinfo = array(
            'id' => 0,
            'masterid' => 0,
            'isStore' => 0
        );

        if (preg_match('/[^a-z0-9]/', $request->username) || strlen($request->username) < 4 || strlen($request->username) > 20) {
            return back()->with('result', '아이디는 영문 소문자, 숫자만 입력하여 4문자 이상이여야 합니다.')
                ->with('form-user', $request->all());
        }
        if (preg_match('/[^\x{1100}-\x{11FF}\x{3130}-\x{318F}\x{AC00}-\x{D7AF}0-9a-zA-Z]/u', $request->nickname)) {
            return back()->with('result', '닉네임은 한글, 영문, 숫자만 입력 가능합니다.')
                ->with('form-user', $request->all());
        }

        if (preg_match('/[^\x{1100}-\x{11FF}\x{3130}-\x{318F}\x{AC00}-\x{D7AF}0-9a-zA-Z]/u', $request->bank_name)
            || preg_match('/[^\x{1100}-\x{11FF}\x{3130}-\x{318F}\x{AC00}-\x{D7AF}0-9a-zA-Z]/u', $request->bank_number)
            || preg_match('/[^\x{1100}-\x{11FF}\x{3130}-\x{318F}\x{AC00}-\x{D7AF}0-9a-zA-Z]/u', $request->banker_name)
            || preg_match('/[^\x{1100}-\x{11FF}\x{3130}-\x{318F}\x{AC00}-\x{D7AF}0-9a-zA-Z]/u', $request->phone)) {
            return back()->with('result', '계좌정보와 전화번호는 특수문자 입력 불가 합니다.')
                ->with('form-user', $request->all());
        }

        if ($request->parent_username != '') {
            $markinfo = User::where('userid', $request->parent_username)
                ->where('rolecode', '0003')
                ->where('verified', 1)
                ->first();

            if (empty($markinfo))
                return back()->with('result', '회원가입 실패! 상위회원ID 누락')
                    ->with('form-user', $request->all());
        }

        $userinfo = User::where('userid', $request->username)
            ->first();
        if (!empty($userinfo))
            return back()->with('result', '회원가입 실패! 회원로그인ID 누락')
                ->with('form-user', $request->all());
        $userinfo = User::where('nickname', $request->nickname)
            ->first();
        if (!empty($userinfo))
            return back()->with('result', '회원가입 실패! 회원닉네임 누락')
                ->with('form-user', $request->all());

        $siteinfo = SiteSetting::take(1)->first();
        if ($siteinfo['is_use_pwd'] == 0) {
            if (preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@^()$!%*#?&])[A-Za-z\d@^()$!%*#?&]{6,20}$/", $request->password) == false)
                return back()->with('result', '비밀번호는 영문, 숫자, 특수문자 포함하여 6문자 이상이여야 합니다.')
                    ->with('form-user', $request->all());
        }

        $isStore = 0;
        if ($markinfo['isStore'] == 1) {
            $isStore = 2;
        }

        $snzMent = '회원추가 성공';

        DB::beginTransaction();
        try {
            $newUserId = User::insertGetId([
                'userid' => $request->username,
                'joinid' => $markinfo['id'],
                'domainid' => 0,
                'masterid' => $markinfo['masterid'] == 0 ? $markinfo['id'] : $markinfo['masterid'],
                'nickname' => $request->nickname,
                'password' => Hash::make($request->password),
                'password_show' => $request->password,
                'phone' => $request->phone,
                'verified' => $request->state,
                'rolecode' => '0003',
                'exchangepassword' => $request->exchange_pwd,
                'bankname' => $request->bank_name,
                'banknumber' => $request->bank_number,
                'bankmaster' => $request->banker_name,
                'isStore' => $isStore,
                'createById' => auth()->user()->id,
            ]);

            DB::unprepared('SET IDENTITY_INSERT sphinx.user_check ON');
            UserCheck::insert([
                'id' => $newUserId,
                'userid' => $request->username,
                'nickname' => $request->nickname
            ]);
            DB::unprepared('SET IDENTITY_INSERT sphinx.user_check OFF');

            if (config('app.site_type') != 'BS') {
                MiniRate::insert([
                    'userid' => $newUserId,
                    'pwbid' => MiniRatePwb::insertGetId(['pow_odd' => '0']),
                    'psaid' => MiniRatePsa::insertGetId(['left' => '0']),
                    'up1id' => MiniRateUP1::insertGetId(['up1_pow_odd' => '0']),
                    'up3id' => MiniRateUP3::insertGetId(['up3_pow_odd' => '0']),
                    'up5id' => MiniRateUP5::insertGetId(['up5_pow_odd' => '0']),
                    'cs3id' => MiniRateCS3::insertGetId(['cs3_left' => '0']),
                    'cs5id' => MiniRateCS5::insertGetId(['cs5_left' => '0']),
                    'cp3id' => MiniRateCP3::insertGetId(['cp3_pow_odd' => '0']),
                    'cp5id' => MiniRateCP5::insertGetId(['cp5_pow_odd' => '0']),
                    'bbpid' => MiniRateBBP::insertGetId(['bbp_pow_odd' => '0']),
                    'bbsid' => MiniRateBBS::insertGetId(['bbs_left' => '0']),
                    'kenoid' => MiniRateKeno::insertGetId(['keno_pow_odd' => '0']),
                    'ksaid' => MiniRateKSA::insertGetId(['ksa_left' => '0']),
                    'hsp3id' => MiniRateHSP3::insertGetId(['hsp3_pow_odd' => '0']),
                    'hsp5id' => MiniRateHSP5::insertGetId(['hsp5_pow_odd' => '0']),
                    'hss3id' => MiniRateHSS3::insertGetId(['hss3_left' => '0']),
                    'hss5id' => MiniRateHSS5::insertGetId(['hss5_left' => '0']),
                    'eos3id' => MiniRateEOS3::insertGetId(['eos3_pow_odd' => '0']),
                    'eos5id' => MiniRateEOS5::insertGetId(['eos5_pow_odd' => '0']),
                    'klays2id' => MiniRateKlays2::insertGetId(['klays2_left' => '0']),
                    'klayp2id' => MiniRateKlayp2::insertGetId(['klayp2_pow_odd' => '0']),
                    'klays5id' => MiniRateKlays5::insertGetId(['klays5_left' => '0']),
                    'klayp5id' => MiniRateKlayp5::insertGetId(['klayp5_pow_odd' => '0']),
                    'mtp5id' => MiniRateMtp5::insertGetId(['mtp5_pow_odd' => '0']),
                    'mts5id' => MiniRateMts5::insertGetId(['mts5_left' => '0']),
                    'mtp3id' => MiniRateMtp3::insertGetId(['mtp3_pow_odd' => '0']),
                    'mts3id' => MiniRateMts3::insertGetId(['mts3_left' => '0']),
                    'xrp3id' => MiniRateXrp3::insertGetId(['xrp3_pow_odd' => '0']),
                    'xrp5id' => MiniRateXrp5::insertGetId(['xrp5_pow_odd' => '0']),
                    'xrs3id' => MiniRateXrs3::insertGetId(['xrs3_left' => '0']),
                    'xrs5id' => MiniRateXrs5::insertGetId(['xrs5_left' => '0']),
                    'pwbplusid' => MiniRatePwbPlus::insertGetId(['pwbplus_pow_odd' => '0']),
                    'psaplusid' => MiniRatePsaPlus::insertGetId(['psaplus_left' => '0']),
                    'dhpwbid' => MiniRateDHPwb::insertGetId(['dhpwb_pow_under' => '0']),
                    'dhpsaid' => MiniRateDHPsa::insertGetId(['dhpsa_left' => '0']),
                ]);
            }

            RollingBS::insert(['userid' => $newUserId]);
            RollingMini::insert(['userid' => $newUserId]);

            BSGamePush::insert(['userid' => $newUserId]);
            MiniGamePush::insert(['userid' => $newUserId]);

            MiniBetLimit::insert(['userid' => $newUserId]);
            UserListProc::insert(['userno' => $newUserId]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
            $snzMent = '회원가입 실패! 관리자에 문의해 주세요';
        }

        return back()->with('result', $snzMent)
            ->with('form-user', $request->all());
    }

    public function treeViewOpt()
    {
        $siteinfo = SiteSetting::take(1)->first();
        $data = array(
            "view_allot_rate" => $siteinfo->view_allot_rate,
            "view_comm_rate" => $siteinfo->view_comm_rate,
        );

        return view('partner.treeViewOpt', $data);
    }

    public function treeViewOptProc(Request $request)
    {
        SiteSetting::take(1)
            ->update(['view_allot_rate' => $request->view_allot_rate,
                'view_comm_rate' => $request->view_comm_rate]);
        return back();
    }

    public function tree(Request $request)
    {
        $data['search_opt'] = $request->search_opt ?? '';
        $data['search_txt'] = $request->search_txt ?? '';
        $data['stepRecord'] = SitePartnerStep::all();
        $data['step'] = $data['stepRecord']->count();
        $data['gameRecord'] = MiniGameCategory::orderBy('show_index', 'asc')
            ->get();
        $data['pickRecord'] = MiniGamePick::all();
        $data['siteInfo'] = SiteSetting::take(1)->first();
        $data['search_userid'] = '';

        if ($data['search_txt'] == '') {
            $data['userRecord'] = User::where('rolecode', '0003')
                ->where('joinid', 0)
                ->get();
            $suminfo = UserListProc::selectRaw('SUM(money) as sum_money, SUM(floor(point)) as sum_point,
                SUM(ban_point + ximax_point + sg_point + boss_point + ppk_point + ag_point + kt_point + sd_point + cx_point + fail_point + pra2_point) as sum_bpoint, SUM(plus_point) as sum_spoint,
                SUM(CASE WHEN losing > 0 THEN losing ELSE 0 END) as sumPLosing, SUM(CASE WHEN losing < 0 THEN losing ELSE 0 END) as sumMLosing')
                ->first();
        } else {
            $snzWhere = $data['search_opt'] == 0 ? 'userid' : 'nickname';
            $search_user = User::where('rolecode', '0003')
                ->where($snzWhere, $request->search_txt)
                ->first();

            if (empty($search_user)) {
                $data['userRecord'] = array();
                $suminfo = array('sum_money' => 0, 'sum_point' => 0, 'sum_bpoint' => 0, 'sum_spoint' => 0);
            } else {
                // $arrSubUserId = array($search_user->id);
                // getSubUserId($search_user, $arrSubUserId);
                $arrTreeUserId = array();
                $tempUser = $search_user;
                $userRecord = array();
                while (1) {
                    $parent = $tempUser->parent;
                    if (empty($parent)) {
                        break;
                    } else {
                        array_push($userRecord, $parent);
                    }
                    $tempUser = $parent;
                }
                $userList = array_reverse($userRecord);
                $deep = 0;
                foreach ($userList as $key => $value) {
                    array_push($arrTreeUserId, array('id' => $value->id, 'user' => $value, 'deep' => $deep));
                    $deep++;
                }

                array_push($arrTreeUserId, array('id' => $search_user->id, 'user' => $search_user, 'deep' => $deep));
                $deep++;
                getTreeUserId($search_user, $arrTreeUserId, $deep);

                $arrSubUserId = array_column($arrTreeUserId, 'id');
                $collect = collect($arrTreeUserId);
                $userRecord = $collect->paginate(50);
//                echo json_encode($userRecord);
//                foreach ($userRecord as $record) {
//                    if (!isset($record->userid)) continue;
//                    echo $record->userid . '_';
//                }
//                exit;

                // if ($search_user->masterid == 0)
                // {
                $nCount = 0;
                $temp = $search_user;
                while (true) {
                    $user = $temp->parent;
                    $temp = $user;
                    $nCount++;
                    if (empty($user))
                        break;
                }
                // $data['step'] = $data['stepRecord']->count() - $nCount + 1;
                // $data['stepRecord'] = array_slice($data['stepRecord']->toArray(), $nCount - 1);
                $data['search_userid'] = '~';
                $data['userRecord'] = $userRecord;
                // }
                // else {
                //      $data['userRecord'] = array();
                //     $userRecord = array($search_user);
                //     $tempUser = $search_user;
                //     while(1) {
                //         $parent = $tempUser->parent;
                //         if (empty($parent)) {
                //             break;
                //         }
                //         else {
                //             array_push($userRecord, $parent);
                //         }
                //         $tempUser = $parent;
                //     }
                //     $userList = array_reverse($userRecord);
                //     $deep = 0;
                //     foreach($userList as $key=>$value){
                //        array_push($data['userRecord'], array('id' => $value->id, 'user' => $value, 'deep' => $deep));
                //        $deep++;
                //     }
                //     $arrSubUserId = array_column($data['userRecord'], 'id');
                //     $data['userRecord'] = collect($data['userRecord'])->paginate(50);
                // }
                $suminfo = DB::select(sprintf("SELECT SUM(money) as sum_money, SUM(floor(point)) as sum_point,
                    SUM(ban_point + ximax_point + sg_point + boss_point + ag_point + kt_point + ppk_point + sd_point + cx_point + fail_point + pra2_point) as sum_bpoint, SUM(plus_point) as sum_spoint,
                    SUM(CASE WHEN losing > 0 THEN losing ELSE 0 END) as sumPLosing, SUM(CASE WHEN losing < 0 THEN losing ELSE 0 END) as sumMLosing
                    FROM sphinx.user_list_proc WHERE userno IN (SELECT Item FROM dbo.SplitStringToInt('%s', ','))", implode(',', $arrSubUserId)))[0];
            }
        }
        $data['keep_money'] = $suminfo->sum_money ?? 0;
        $data['keep_point'] = $suminfo->sum_point ?? 0;
        $data['sum_bpoint'] = $suminfo->sum_bpoint ?? 0;
        $data['sum_spoint'] = $suminfo->sum_spoint ?? 0;
        $data['sumPLosing'] = $suminfo->sumPLosing ?? 0;
        $data['sumMLosing'] = $suminfo->sumMLosing ?? 0;
        $data['apiType'] = config('app.api_type');

        return view('partner.tree2', $data);
    }

    public function depth_list()
    {
        $data['depthRecord'] = SitePartnerStep::all();
        return view('partner.depth.list', $data);
    }

    public function depth_add_view()
    {
        return view('partner.depth.add');
    }

    public function depth_add_proc(Request $request)
    {
        SitePartnerStep::insert(['step_name' => $request->step_name]);
        return back();
    }

    public function depth_del_proc(Request $request)
    {
        SitePartnerStep::where('id', $request->id)
            ->delete();
        return back();
    }

    public function depth_edit_view(Request $request)
    {
        $stepinfo = SitePartnerStep::where('id', $request->id)
            ->first();
        return view('partner.depth.edit', $stepinfo);
    }

    public function depth_edit_proc(Request $request)
    {
        SitePartnerStep::where('id', $request->id)
            ->update(['step_name' => $request->step_name]);

        return back();
    }

    public function rank_bettings(Request $request)
    {
        if ($request->userid > 0) {
            $userinfo = User::where('rolecode', '0003')
                ->where('id', $request->userid)
                ->first();
        }

        $date_today = date('Y-m-d');
        $date_start = $request->start_date ?? $date_today . ' to ' . $date_today;
        $objData = getDateFromRangeDate($date_start);
        $gameType = $request->gameType ?? 'all';
        $gameItem = $request->gameItem ?? '';
        $searchTxt = $request->search_text ?? '';

        $sort = $request->sort ?? 'desc';
        $type = $request->type ?? 'main_sum';

        $data = array(
            'userid' => $request->userid,
            'date_start' => $date_start,
            'gameType' => $gameType,
            'gameItem' => $gameItem,
            'userInfo' => $userinfo ?? null,
            'sort' => $sort,
            'type' => $type,
            'search_text' => $searchTxt,
        );

        if (isset($userinfo)) {
            $arrSubUserId = array($request->userid);
            getSubUserId($userinfo, $arrSubUserId);
        } else if ($searchTxt != '') {
            $temp = User::where('rolecode', '0003')
                ->where('userid', $searchTxt)
                ->first();
            if (!empty($temp))
                $arrSubUserId = [$temp->id];
        }

        $miniRecord = MiniGameCategory::orderBy('show_index', 'asc')
            ->get();
        $liveRecord = BSGameCategory::where('is_live', 1)
            ->orderByDesc('order_no')
            ->get();
        $slotRecord = BSGameCategory::where('is_live', 0)
            ->orderByDesc('order_no')
            ->get();
        switch ($gameType) {
            case 'all':
                $collect = collect(DB::select(sprintf("EXEC %s.rankGameBetMonth @Start='%s 00:00:00', @End='%s 23:59:59'",
                    config('global.prefix'), $objData[0], $objData[1])));
                $data['gameRecord'] = array();
                break;
            case 'pwb':
                $code = implode(',', array_column($miniRecord->toArray(), 'game_code'));
                if ($gameItem != '')
                    $code = $gameItem;
                $collect = collect(DB::select(sprintf("EXEC %s.rankMiniBetMonth @Start='%s 00:00:00', @End='%s 23:59:59', @Game='%s'",
                    config('global.prefix'), $objData[0], $objData[1], $code)));
                $data['gameRecord'] = $miniRecord;
                break;
            case 'slot':
                $code = implode(',', array_column($slotRecord->toArray(), 'thirdPartyCode'));
                $data['gameRecord'] = $slotRecord;
                if ($gameItem != '')
                    $code = $gameItem;
                $collect = collect(DB::select(sprintf("EXEC %s.rankBSBetMonth @Start='%s 00:00:00', @End='%s 23:59:59', @Live=%d, @Game='%s'",
                    config('global.prefix'), $objData[0], $objData[1], 0, $code)));
                break;
            case 'live':
                $code = implode(',', array_column($liveRecord->toArray(), 'thirdPartyCode'));
                $data['gameRecord'] = $liveRecord;
                if ($gameItem != '')
                    $code = $gameItem;
                $collect = collect(DB::select(sprintf("EXEC %s.rankBSBetMonth @Start='%s 00:00:00', @End='%s 23:59:59', @Live=%d, @Game='%s'",
                    config('global.prefix'), $objData[0], $objData[1], 1, $code)));
                break;
            default:
                break;
        }
        if (isset($collect)) {
            if (isset($arrSubUserId) && !empty($arrSubUserId))
                $collect = $collect->whereIn('id', $arrSubUserId);
            if ($sort == 'desc')
                $betRecord = $collect->sortByDesc($type);
            else
                $betRecord = $collect->sortBy($type);
            $data['sumInfo'] = array(
                'sumBet' => array_sum(array_column($collect->toArray(), 'bet_sum')),
                'sumWin' => array_sum(array_column($collect->toArray(), 'win_sum')),
                'sumLoss' => array_sum(array_column($collect->toArray(), 'loss_sum')),
                'sumFee' => array_sum(array_column($collect->toArray(), 'fee_sum')),
                'sumMain' => array_sum(array_column($collect->toArray(), 'main_sum')),
            );
            $data['betRecord'] = $betRecord->paginate(26);
        } else {
            $data['betRecord'] = array();
            $data['sumInfo'] = array();
        }
        return view('partner.rank.bets', $data);
    }

    public function rank_rolling(Request $request)
    {
        $search_user = User::where('rolecode', '0003')
            ->where('id', $request->userid)
            ->first();
        $date_start = $request->start_date ?? date("Y-m-d") . ' to ' . date("Y-m-d");
        $objData = getDateFromRangeDate($date_start);

        $arrSubUserId = array($request->userid);
        getSubUserId($search_user, $arrSubUserId);

        $data['direction'] = $request->direction ?? 'asc';
        $direction = $data['direction'] == 'asc' ? 'desc' : 'asc';

        $collect = collect(DB::select(sprintf("EXEC %s.rankFeeMonth @Start='%s 00:00:00', @End='%s 23:59:59', @Sort='%s'",
            config('global.prefix'), $objData[0], $objData[1], $direction)));
        $collect = $collect->whereIn('id', $arrSubUserId);
        $sumInfo = $collect->toArray();
        $nSumFee = array_sum(array_column($sumInfo, 'fee_sum'));
        $betRecord = $collect->paginate(26);

        $data['userinfo'] = $search_user;
        $data['start_date'] = $date_start;
        $data['chargeRecord'] = $betRecord;
        $data['charge_sum'] = $nSumFee ?? 0;
        $data['title'] = '수수료';

        return view('partner.rank.fee', $data);
    }

    public function rank_charges(Request $request)
    {
        $search_user = User::where('rolecode', '0003')
            ->where('id', $request->userid)
            ->first();

        $data["start_date"] = $request->start_date ?? date("Y-m-d") . ' to ' . date("Y-m-d");
        $objDate = getDateFromRangeDate($data['start_date']);

        $arrSubUserId = array($request->userid);
        getSubUserId($search_user, $arrSubUserId);

        $data['direction'] = $request->direction ?? 'asc';
        $direction = $data['direction'] == 'asc' ? 'desc' : 'asc';
        $data['userinfo'] = $search_user;
        $data['chargeRecord'] = PayCharge::whereIn('userid', $arrSubUserId)
            ->where('created_at', '>=', $objDate[0] . ' 00:00:00')
            ->where('created_at', '<=', $objDate[1] . ' 23:59:59')
            ->where('verified', 2)
            ->select('userid', DB::raw('SUM(money) as sum_money'))
            ->groupBy('userid')
            ->orderBy('sum_money', $direction)
            ->paginate(16);
        $suminfo = PayCharge::whereIn('userid', $arrSubUserId)
            ->where('created_at', '>=', $objDate[0] . ' 00:00:00')
            ->where('created_at', '<=', $objDate[1] . ' 23:59:59')
            ->where('verified', 2)
            ->select(DB::raw('SUM(money) as sum_money'))
            ->first();
        $data['charge_sum'] = ($suminfo->sum_money ?? 0);
        $data['title'] = '충전';

        return view('partner.rank.charge', $data);
    }

    public function rank_excharges(Request $request)
    {
        $search_user = User::where('rolecode', '0003')
            ->where('id', $request->userid)
            ->first();

        $data["start_date"] = $request->start_date ?? date("Y-m-d") . ' to ' . date("Y-m-d");
        $objDate = getDateFromRangeDate($data['start_date']);
        $userinfo = User::where('id', $request->userid)
            ->first();
        $arrSubUserId = array($request->userid);
        getSubUserId($userinfo, $arrSubUserId);

        $data['direction'] = $request->direction ?? 'asc';
        $direction = $data['direction'] == 'asc' ? 'desc' : 'asc';
        $data['userinfo'] = $search_user;
        $data['chargeRecord'] = PayExchange::whereIn('userid', $arrSubUserId)
            ->where('created_at', '>=', $objDate[0] . ' 00:00:00')
            ->where('created_at', '<=', $objDate[1] . ' 23:59:59')
            ->where('verified', 2)
            ->select('userid', DB::raw('SUM(money) as sum_money'))
            ->groupBy('userid')
            ->orderBy('sum_money', $direction)
            ->paginate(16);
        $suminfo = PayExchange::whereIn('userid', $arrSubUserId)
            ->where('created_at', '>=', $objDate[0] . ' 00:00:00')
            ->where('created_at', '<=', $objDate[1] . ' 23:59:59')
            ->where('verified', 2)
            ->select(DB::raw('SUM(money) as sum_money'))
            ->first();
        $data['charge_sum'] = ($suminfo->sum_money ?? 0);
        $data['title'] = '환전';

        return view('partner.rank.charge', $data);
    }

    public function indexFee(Request $request)
    {
        $data['chargeRecord'] = PayCharge::where('userid', $request->userid)
            ->where('verified', 2)
            ->whereIn('group_type', [1, 4])
            ->orderByDesc('created_at')
            ->paginate(12);
        $data['userinfo'] = User::where('id', $request->userid)
            ->first();
        return view('partner.fee.index', $data);
    }

    public function procFeeWithdrawal(Request $request)
    {
        $admin = Auth::user();
        if ($admin->password_show == $request->admin_pwd) {
            $userinfo = User::where('id', $request->userid)
                ->first();
            if (!empty($userinfo)) {
                if (UserListProc::where('userno', $request->userid)
                    ->where('point', '>=', $request->amount)
                    ->update(['money' => DB::raw('money + ' . $request->amount),
                        'point' => DB::raw('point - ' . $request->amount)])) {
                    PayCharge::insert([
                        'userid' => $request->userid,
                        'money' => $request->amount,
                        'user_money' => floor($userinfo->procinfo->money),
                        'verified' => 2,
                        'group_type' => 4,
                        'updateBy' => $admin->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            } else {
                return back()->with('ment', '수수료 전환 실패! 다시 시도해 주세요.');
            }
        } else {
            return back()->with('ment', '수수료 전환 실패! 관리자 비밀번호가 일치하지 않습니다.');
        }

        return back()->with('ment', 'ok');
    }

    public function listFee(Request $request)
    {
        $searchUser = User::where('rolecode', '0003')
            ->where('id', $request->userid)
            ->first();

        $today = date('Y-m-d');
        $startDate = $request->startDate ?? $today . ' to ' . $today;
        $objData = getDateFromRangeDate($request->startDate);
        $gameType = $request->gameType ?? 'pwb';
        $gameItem = $request->gameItem ?? '';

        $miniRecord = MiniGameCategory::orderBy('show_index', 'asc')
            ->get();
        $liveRecord = BSGameCategory::where('is_live', 1)
            ->orderByDesc('order_no')
            ->get();
        $slotRecord = BSGameCategory::where('is_live', 0)
            ->orderByDesc('order_no')
            ->get();

        $nSumFee = 0;
        switch ($gameType) {
            case 'pwb':
                $gameRecord = $miniRecord;
                $tempSQL = MiniBetFeeAll::where('created_at', '>=', $objData[0] . ' 00:00:00')
                    ->where('created_at', '<=', $objData[1] . ' 23:59:59')
                    ->where('fee_userid', $request->userid)
                    ->where('status', 1);
                if ($gameItem != '')
                    $tempSQL->where('game_code', $gameItem);
                $tempSQL->orderByDesc('created_at');
                $tempRecord = $tempSQL->get();
                $nSumFee = array_sum(array_column($tempRecord->toArray(), 'fee_amount'));
                $feeRecord = $tempRecord->paginate(16);
                break;
            case 'slot':
                $gameRecord = $slotRecord;
                $tempSQL = BSBetFeeAll::where('transTime', '>=', $objData[0] . ' 00:00:00')
                    ->where('transTime', '<=', $objData[1] . ' 23:59:59')
                    ->where('proc_userid', $request->userid)
                    ->where('is_live', 0)
                    ->where('status', 1);
                if ($gameItem != '')
                    $tempSQL->where('thirdParty', $gameItem);
                $tempSQL2 = clone $tempSQL;
                $tempSQL->orderByDesc('transTime');
                $feeRecord = $tempSQL->paginate(16);
                $sumInfo = $tempSQL2->selectRaw('SUM(fee_amount) as sum_fee')
                    ->first();
                $nSumFee = $sumInfo->sum_fee;
                break;
            case 'live':
                $gameRecord = $liveRecord;
                $tempSQL = BSBetFeeAll::where('transTime', '>=', $objData[0] . ' 00:00:00')
                    ->where('transTime', '<=', $objData[1] . ' 23:59:59')
                    ->where('proc_userid', $request->userid)
                    ->where('is_live', 1)
                    ->where('status', 1);
                if ($gameItem != '')
                    $tempSQL->where('thirdParty', $gameItem);
                $tempSQL2 = clone $tempSQL;
                $tempSQL->orderByDesc('transTime');
                $feeRecord = $tempSQL->paginate(16);
                $sumInfo = $tempSQL2->selectRaw('SUM(fee_amount) as sum_fee')
                    ->first();
                $nSumFee = $sumInfo->sum_fee;
                break;
            default:
                break;
        }

        $data = array(
            'userid' => $request->userid,
            'startDate' => $startDate,
            'sum_fee' => $nSumFee,
            'feeRecord' => $feeRecord,
            'gameRecord' => $gameRecord,
            'userinfo' => $searchUser,
            'gameType' => $gameType,
            'gameItem' => $gameItem,
        );
        return view('partner.fee.list', $data);
    }

    public function listLose(Request $request)
    {
        $searchUser = User::where('rolecode', '0003')
            ->where('id', $request->userid)
            ->first();

        $today = date('Y-m-d');
        $startDate = $request->startDate ?? $today . ' to ' . $today;
        $objDate = getDateFromRangeDate($startDate);
        $gameType = $request->gameType ?? 'live';
        $gameItem = $request->gameItem ?? '';

        $liveRecord = BSGameCategory::where('is_live', 1)
            ->orderByDesc('order_no')
            ->get();
        $slotRecord = BSGameCategory::where('is_live', 0)
            ->orderByDesc('order_no')
            ->get();

        $nSumFee = 0;
        $tempSQL = BSBetFeeAll::where('transTime', '>=', $objDate[0] . ' 00:00:00')
            ->where('transTime', '<=', $objDate[1] . ' 23:59:59')
            ->where('proc_userid', $request->userid)
            ->where('lose_amount', '!=', 0)
            ->where('status', 1);
        if ($gameItem != '')
            $tempSQL->where('thirdParty', $gameItem);
        $tempSQL2 = clone $tempSQL;
        $tempSQL->orderByDesc('transTime');
        switch ($gameType) {
            case 'all':
                $gameRecord = array();
                $sumInfo = $tempSQL2->where('init_lose', 0)
                    ->selectRaw('SUM(lose_amount) as sum_losing')
                    ->first();
                $nSumFee = $sumInfo->sum_losing ?? 0;
                $feeRecord = $tempSQL->paginate(16);
                break;
            case 'slot':
                $gameRecord = $slotRecord;
                $sumInfo = $tempSQL2->where('init_lose', 0)
                    ->where('is_live', 0)
                    ->selectRaw('SUM(lose_amount) as sum_losing')
                    ->first();
                $nSumFee = $sumInfo->sum_losing ?? 0;
                $feeRecord = $tempSQL->where('is_live', 0)->paginate(16);
                break;
            case 'live':
                $gameRecord = $liveRecord;
                $sumInfo = $tempSQL2->where('init_lose', 0)
                    ->where('is_live', 1)
                    ->selectRaw('SUM(lose_amount) as sum_losing')
                    ->first();
                $nSumFee = $sumInfo->sum_losing ?? 0;
                $feeRecord = $tempSQL->where('is_live', 1)->paginate(16);
                break;
            default:
                break;
        }

        $data = array(
            'userid' => $request->userid,
            'startDate' => $startDate,
            'sum_fee' => $nSumFee > 0 ? floor($nSumFee) : ceil($nSumFee),
            'feeRecord' => $feeRecord,
            'gameRecord' => $gameRecord,
            'userinfo' => $searchUser,
            'gameType' => $gameType,
            'gameItem' => $gameItem,
        );
        return view('partner.lose.list', $data);
    }

    public function exactLose(Request $request)
    {
        $userid = $request->userid;
        $user = UserListProc::where('userno', $userid)
            ->first();
        BSBetFeeDay::where('created_at', '<=', date('Y-m-d H:i:s'))
            ->where('init_lose', 0)
            ->update(['init_lose' => 1]);

        $amount = floor($user->losing);
        if ($amount > 0) {
            UserListProc::where('userno', $userid)
                ->update(['money' => DB::raw('money + ' . $user->losing),
                    'losing' => DB::raw('losing - ' . $user->losing)]);
            BSLoseInit::insert([
                'userid' => $userid,
                'money' => $amount,
                'user_money' => $user->money + $amount,
                'is_deposit' => 1,
            ]);

            PayCharge::insert([
                'userid' => $userid,
                'money' => $amount,
                'user_money' => $user->money + $amount,
                'verified' => 2,
                'group_type' => 5,
                'updateBy' => $userid,
            ]);
        } else if ($amount < 0) {
            UserListProc::where('userno', $userid)
                ->update(['losing' => 0]);
            BSLoseInit::insert([
                'userid' => $userid,
                'money' => $amount,
                'user_money' => $user->money,
                'is_deposit' => 0,
            ]);
        }

        return json_encode(array('status' => 'success', 'ment' => ''));
    }

    public function resetLose(Request $request)
    {
        $userid = $request->userid;
        $user = UserListProc::where('userno', $userid)
            ->first();
        BSBetFeeDay::where('created_at', '<=', date('Y-m-d H:i:s'))
            ->where('init_lose', 0)
            ->update(['init_lose' => 1]);

        if ($user->losing != 0) {
            UserListProc::where('userno', $userid)
                ->update(['losing' => 0]);
            BSLoseInit::insert([
                'userid' => $userid,
                'money' => floor($user->losing),
                'user_money' => $user->money,
                'is_deposit' => 0,
            ]);
        }
        return json_encode(array('status' => 'success', 'ment' => ''));
    }

    public function historyLose(Request $request)
    {
        $user = User::where('id', $request->userid)
            ->first();
        $data['type'] = '초기화';
        $data['nickname'] = $user->nickname;
        $data['nickid'] = $user->userid;
        $data['start_date'] = $request->start_date ?? date('Y-m-d') . ' to ' . date('Y-m-d');
        $objData = getDateFromRangeDate($data['start_date']);
        $data['userid'] = $request->userid;
        $data['loseRecord'] = BSLoseInit::where('userid', $request->userid)
            ->where('created_at', '>=', $objData[0] . ' 00:00:00')
            ->where('created_at', '<=', $objData[1] . ' 23:59:59')
            ->where('is_deposit', 0)
            ->orderByDesc('created_at')
            ->paginate(16);
        return view('partner.lose.history', $data);
    }

    public function historyLose2(Request $request)
    {
        $user = User::where('id', $request->userid)
            ->first();
        $data['type'] = '정산';
        $data['nickname'] = $user->nickname;
        $data['nickid'] = $user->userid;
        $data['start_date'] = $request->start_date ?? date('Y-m-d') . ' to ' . date('Y-m-d');
        $objData = getDateFromRangeDate($data['start_date']);
        $data['userid'] = $request->userid;
        $data['loseRecord'] = BSLoseInit::where('userid', $request->userid)
            ->where('created_at', '>=', $objData[0] . ' 00:00:00')
            ->where('created_at', '<=', $objData[1] . ' 23:59:59')
            ->where('is_deposit', 1)
            ->orderByDesc('created_at')
            ->paginate(16);
        return view('partner.lose.history', $data);
    }

    public function checkList(Request $request)
    {
        $userRecord = User::where('rolecode', '0003')
            ->where('verified', 1)
            ->get();
        $chgRecord = PayCharge::where('verified', 2)
            ->get();
        $exchgRecord = PayExchange::where('verified', '!=', 4)
            ->get();


        $data['endDate'] = $request->endDate ?? date('Y-m-d');
        $data['userRecord'] = [];
        return view('partner.check', $data);
    }
}
