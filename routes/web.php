<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('index/{locale}',[App\Http\Controllers\HomeController::class, 'lang']);
Route::get('/',  [\App\Http\Controllers\BettingController::class, 'casinoBetHistory'])->name('root');
Route::get('/index',  [\App\Http\Controllers\BettingController::class, 'casinoBetHistory'])->name('root');
Route::prefix('casino')->group(function() {
    Route::get('betHistory', [\App\Http\Controllers\BettingController::class, 'casinoBetHistory'])->name('liveBetHistory');
    Route::get('/betInfo', [\App\Http\Controllers\BettingController::class, 'betInfo'])->name('betInfo');
});

Route::prefix('user')->group(function () {
    Route::get('/edit', [App\Http\Controllers\UserController::class, 'edit_view'])->name('user_edit');
    Route::post('/edit', [App\Http\Controllers\UserController::class, 'edit_proc']);
    Route::get('/send_message', [App\Http\Controllers\UserController::class, 'message_view'])->name('user_message_view');
    Route::post('/send_message', [App\Http\Controllers\UserController::class, 'message_proc']);
    Route::get('/charge', [App\Http\Controllers\UserController::class, 'charge_view'])->name('user_charge');
    Route::post('/charge', [App\Http\Controllers\UserController::class, 'charge_proc']);
    Route::get('/exchange', [App\Http\Controllers\UserController::class, 'excharge_view'])->name('user_excharge');
    Route::post('/exchange', [App\Http\Controllers\UserController::class, 'excharge_proc']);
    // Route::get('/bets', [App\Http\Controllers\UserController::class, 'bets_view']);
    Route::get('/login_log', [App\Http\Controllers\UserController::class, 'login_log_view'])->name('user_login_log');
    Route::get('/info_log', [App\Http\Controllers\UserController::class, 'info_log_view'])->name('user_info_log');

    Route::get('/info', [App\Http\Controllers\UserController::class, 'userinfo'])->name('user_info');
    Route::get('/withdraw', [App\Http\Controllers\UserController::class, 'withdraw'])->name('user_withdraw');

    Route::get('/set_account_enable', [App\Http\Controllers\UserController::class, 'set_account_enable']);
    Route::get('/set_account_pending', [App\Http\Controllers\UserController::class, 'set_account_pending']);
    Route::get('/set_account_disable', [App\Http\Controllers\UserController::class, 'set_account_disable']);
    Route::get('/reset_login_fail_cnt', [App\Http\Controllers\UserController::class, 'reset_login_fail_cnt']);
    Route::get('/remove_user', [App\Http\Controllers\UserController::class, 'remove_user']);

    Route::get('/edit_all_allot_rate', [App\Http\Controllers\UserController::class, 'edit_all_allot_rate'])->name('edit_all_allot_rate');
    Route::post('/edit_all_allot_rate', [App\Http\Controllers\UserController::class, 'edit_all_allot_rate_proc']);
    Route::get('/edit_allot_rate_logs', [App\Http\Controllers\UserController::class, 'edit_allot_rate_logs'])->name('edit_allot_rate_logs');

    Route::get('/edit_all_comm_rates', [App\Http\Controllers\UserController::class, 'edit_all_comm_rates'])->name('edit_all_comm_rates');
    Route::post('/edit_all_comm_rates', [App\Http\Controllers\UserController::class, 'edit_all_comm_rates_proc']);
    Route::get('/edit_comm_rate_logs', [App\Http\Controllers\UserController::class, 'edit_comm_rate_logs'])->name('edit_comm_rate_logs');

//    Route::get('/edit_bet_push', [App\Http\Controllers\UserController::class, 'edit_bet_push_view'])->name('edit_bet_push');
//    Route::post('/edit_bet_push', [App\Http\Controllers\UserController::class, 'edit_bet_push_proc']);
//    Route::get('/edit_bet_push_logs', [App\Http\Controllers\UserController::class, 'edit_bet_push_logs'])->name('edit_bet_push_logs');

//    Route::prefix('push')->group(function () {
//        Route::get('/casino/init', [App\Http\Controllers\UserController::class, 'initPushCasino']);
//        Route::get('/casino/edit', [App\Http\Controllers\UserController::class, 'editPushCasino'])->name('editPushCasino');
//        Route::post('/casino/edit', [App\Http\Controllers\UserController::class, 'editPushCasinoProc']);
//        Route::get('/casino/log', [App\Http\Controllers\UserController::class, 'editPushCasinoLog'])->name('editPushCasinoLog');
//
//    });

    Route::prefix('casino')->group(function () {
        Route::get('/charge', [App\Http\Controllers\UserController::class, 'casinoCharge']);
        Route::post('/charge', [App\Http\Controllers\UserController::class, 'casinoChargeProc']);
        Route::get('/excharge', [App\Http\Controllers\UserController::class, 'casinoExcharge']);
        Route::post('/excharge', [App\Http\Controllers\UserController::class, 'casinoExchargeProc']);
    });

    Route::get('/user_bet_limits', [App\Http\Controllers\UserController::class, 'user_bet_limits_view'])->name('user_bet_limits');
    Route::post('/user_bet_limits', [App\Http\Controllers\UserController::class, 'user_bet_limits_proc']);
    Route::get('/user_bet_limits_logs', [App\Http\Controllers\UserController::class, 'user_bet_limits_logs'])->name('user_bet_limits_logs');

    Route::get('/user_edit_path', [App\Http\Controllers\UserController::class, 'user_edit_path_view'])->name('user_edit_path');
    Route::post('/user_edit_path', [App\Http\Controllers\UserController::class, 'user_edit_path_proc']);

    Route::get('/withdraw_charge', [App\Http\Controllers\UserController::class, 'withdraw_charge_view'])->name('withdraw_charge');

    Route::get('/rolling_logs', [App\Http\Controllers\UserController::class, 'rolling_logs_view'])->name('rolling_logs');

    Route::get('/set_only_domain', [App\Http\Controllers\UserController::class, 'set_only_domain'])->name('set_only_domain');
    Route::post('/set_only_domain', [App\Http\Controllers\UserController::class, 'set_only_domain_proc']);

});

Route::prefix('analysis')->group(function() {
    Route::get('/daily', [\App\Http\Controllers\AnalysisController::class, 'dailyList'])->name('dailyStatistics');
    Route::get('/staged', [\App\Http\Controllers\AnalysisController::class, 'stageList'])->name('stagedStatistics');
    Route::get('/partner/tree', [\App\Http\Controllers\AnalysisController::class, 'partnerTree'])->name('stat_partner_tree');
    Route::get('/partner/table', [\App\Http\Controllers\AnalysisController::class, 'partnerTable'])->name('stat_partner_table');
    Route::get('/chexdetails', [\App\Http\Controllers\AnalysisController::class, 'chexdetails'])->name('chexdetails');
});

Route::prefix('ajax')->group(function() {
    Route::get('/combo/game', [App\Http\Controllers\HomeController::class, 'loadComboGame'])->name('loadComboGame');
    Route::get('/inquery/templets', [App\Http\Controllers\BoardController::class, 'loadTemplate']);
//    Route::get('/live/content', [App\Http\Controllers\StatisticsController::class, 'liveBetContent']);
//    Route::get('/live/findNotAllot', [App\Http\Controllers\BettingController::class, 'findNotAllot']);
});


Route::prefix('board')->group(function () {
    Route::get('/index', [App\Http\Controllers\BoardController::class, 'board_list'])->name('board_list');
    Route::get('/add', [App\Http\Controllers\BoardController::class, 'board_add_view'])->name('board_add_view');
    Route::post('/add', [App\Http\Controllers\BoardController::class, 'board_add_proc']);
    Route::post('/images', [App\Http\Controllers\BoardController::class, 'uploadImage'])->name('post.image');
    Route::get('/edit', [App\Http\Controllers\BoardController::class, 'board_edit_view'])->name('board_edit_view');
    Route::post('/edit', [App\Http\Controllers\BoardController::class, 'board_edit_proc']);
    Route::get('/del', [App\Http\Controllers\BoardController::class, 'board_del_proc'])->name('board_del_proc');
});

Route::prefix('message')->group(function () {
    Route::get('/index', [App\Http\Controllers\ServiceController::class, 'message_list'])->name('message_list');
    Route::get('/delete_all', [App\Http\Controllers\ServiceController::class, 'message_del_all'])->name('message_del_all');
    Route::get('/add_all', [App\Http\Controllers\ServiceController::class, 'message_add_all_view'])->name('message_add_all_view');
    Route::post('/add_all', [App\Http\Controllers\ServiceController::class, 'message_add_all_proc']);
    Route::get('/view', [App\Http\Controllers\ServiceController::class, 'message_view'])->name('message_view');
    Route::get('/send', [App\Http\Controllers\ServiceController::class, 'message_send_view'])->name('message_send_view');
    Route::post('/send', [App\Http\Controllers\ServiceController::class, 'message_send_proc'])->name('message_send_proc');
    Route::get('/del', [App\Http\Controllers\ServiceController::class, 'message_del_proc'])->name('message_del_proc');
});

Route::prefix('templets')->group(function () {
    Route::get('/', [App\Http\Controllers\InqueryController::class, 'templets'])->name('inquiries_templets');
    Route::get('/add', [App\Http\Controllers\InqueryController::class, 'templetsAddView'])->name('inquiries_templets_add');
    Route::post('/add', [App\Http\Controllers\InqueryController::class, 'templetsAddProc']);
    Route::get('/edit', [App\Http\Controllers\InqueryController::class, 'templetsEditView'])->name('inquiries_templets_edit');
    Route::post('/edit', [App\Http\Controllers\InqueryController::class, 'templetsEditProc']);
    Route::post('/delete', [App\Http\Controllers\InqueryController::class, 'templetsEditDelete'])->name('inquiries_templets_del');
});

Route::prefix('site')->group(function () {
    Route::get('/setting', [App\Http\Controllers\SiteController::class, 'setting_view'])->name('siteSetting');
    Route::post('/setting', [App\Http\Controllers\SiteController::class, 'setting_proc']);

    Route::prefix('black_bank_numbers')->group(function () {
        Route::get('/', [App\Http\Controllers\SiteController::class, 'blackBankNumbers'])->name('blackBankNumbers');
        Route::get('/delete', [App\Http\Controllers\SiteController::class, 'blackBankNumbersDel'])->name('blackBankNumbersDel');
        Route::get('/add', [App\Http\Controllers\SiteController::class, 'blackBankNumbersAddView'])->name('blackBankNumbersAdd');
        Route::post('/add', [App\Http\Controllers\SiteController::class, 'blackBankNumbersAddProc']);
    });
});

Route::prefix('games')->group(function () {
    Route::get('/', [App\Http\Controllers\SiteController::class, 'games_list'])->name('gameslist');
    Route::get('/logs', [App\Http\Controllers\SiteController::class, 'games_logs'])->name('gameslogs');

    Route::prefix('mini')->group(function () {
        Route::get('/edit', [App\Http\Controllers\SiteController::class, 'mini_edit_view'])->name('gamesMiniEdit');
        Route::post('/edit', [App\Http\Controllers\SiteController::class, 'mini_edit_proc']);

        Route::prefix('pick')->group(function () {
            Route::get('/', [App\Http\Controllers\SiteController::class, 'mini_pick_view'])->name('gamesMiniPick');
            Route::get('/edit', [App\Http\Controllers\SiteController::class, 'mini_pick_edit_view'])->name('gamesMiniPickEditView');
            Route::post('/edit', [App\Http\Controllers\SiteController::class, 'mini_pick_edit_proc']);
        });

        Route::prefix('limit')->group(function () {
            Route::get('/', [App\Http\Controllers\SiteController::class, 'mini_limit_view'])->name('gamesMiniLimit');
            Route::get('/add', [App\Http\Controllers\SiteController::class, 'mini_limit_add_view'])->name('gamesMiniLimitAdd');
            Route::post('/add', [App\Http\Controllkers\SiteController::class, 'mini_limit_add_proc']);
            Route::get('/edit', [App\Http\Controllers\SiteController::class, 'mini_limit_edit_view'])->name('gamesMiniLimitEdit');
            Route::post('/edit', [App\Http\Controllers\SiteController::class, 'mini_limit_edit_proc']);
            Route::get('/del', [App\Http\Controllers\SiteController::class, 'mini_limit_del'])->name('gamesMiniLimitDel');
        });
    });

    Route::prefix('slot')->group(function () {
        Route::get('/edit', [App\Http\Controllers\SiteController::class, 'slot_edit_view'])->name('gamesSlotEdit');
        Route::post('/edit', [App\Http\Controllers\SiteController::class, 'slot_edit_proc']);
        Route::get('/list', [App\Http\Controllers\SiteController::class, 'slot_edit_list'])->name('gamesSlotPick');
        Route::get('/enable', [App\Http\Controllers\SiteController::class, 'slot_edit_list_enable'])->name('gamesSlotPickEnable');
        Route::get('/update', [App\Http\Controllers\SiteController::class, 'slot_update'])->name('updateSlot');
    });

    Route::prefix('casino')->group(function () {
        Route::get('/edit', [App\Http\Controllers\SiteController::class, 'casino_edit_view'])->name('gamesCasinoEdit');
        Route::post('/edit', [App\Http\Controllers\SiteController::class, 'casino_edit_proc']);
    });
});


Route::prefix('partner')->group(function () {
    Route::get('/index', [App\Http\Controllers\PartnerController::class, 'list'])->name('partner_list');
    Route::get('/search', [App\Http\Controllers\PartnerController::class, 'searchUsers'])->name('partner_search');
    Route::get('/add', [App\Http\Controllers\PartnerController::class, 'addView'])->name('partner_add');
    Route::post('/add', [App\Http\Controllers\PartnerController::class, 'addProc']);

    Route::get('/set/evo2', [App\Http\Controllers\PartnerController::class, 'setEvo2'])->name('partner_evo2');

    Route::get('/tree_view_opt', [App\Http\Controllers\PartnerController::class, 'treeViewOpt'])->name('tree_view_opt');
    Route::post('/tree_view_opt', [App\Http\Controllers\PartnerController::class, 'treeViewOptProc']);

    Route::prefix('depth')->group(function () {
        Route::get('/', [App\Http\Controllers\PartnerController::class, 'depth_list'])->name('partner_depth_list');
        Route::get('/add', [App\Http\Controllers\PartnerController::class, 'depth_add_view'])->name('partner_depth_add');
        Route::post('/add', [App\Http\Controllers\PartnerController::class, 'depth_add_proc']);
        Route::get('/edit', [App\Http\Controllers\PartnerController::class, 'depth_edit_view'])->name('partner_depth_edit');
        Route::post('/edit', [App\Http\Controllers\PartnerController::class, 'depth_edit_proc']);
        Route::get('/del', [App\Http\Controllers\PartnerController::class, 'depth_del_proc'])->name('partner_depth_del');
    });

    Route::prefix('ranking')->group(function () {
        Route::get('/bettings', [App\Http\Controllers\PartnerController::class, 'rank_bettings'])->name('partner_rank_bettings');
        Route::get('/rolling', [App\Http\Controllers\PartnerController::class, 'rank_rolling'])->name('partner_rank_rolling');
        Route::get('/charges', [App\Http\Controllers\PartnerController::class, 'rank_charges'])->name('partner_rank_charges');
        Route::get('/excharges', [App\Http\Controllers\PartnerController::class, 'rank_excharges'])->name('partner_rank_excharges');
    });

    Route::prefix('fee')->group(function () {
        Route::get('/', [App\Http\Controllers\PartnerController::class, 'indexFee'])->name('partner_fee_index');
        Route::post('/', [App\Http\Controllers\PartnerController::class, 'procFeeWithdrawal']);
        Route::get('/list', [App\Http\Controllers\PartnerController::class, 'listFee'])->name('partner_fee_list');
    });

    Route::prefix('lose')->group(function () {
        Route::get('/list', [App\Http\Controllers\PartnerController::class, 'listLose'])->name('partner_lose_list');
        Route::get('/history', [App\Http\Controllers\PartnerController::class, 'historyLose'])->name('partner_lose_history');
        Route::get('/history2', [App\Http\Controllers\PartnerController::class, 'historyLose2'])->name('partner_lose_history2');
        Route::get('/exact', [App\Http\Controllers\PartnerController::class, 'exactLose']);
        Route::get('/reset', [App\Http\Controllers\PartnerController::class, 'resetLose']);
    });

    Route::get('/tree', [App\Http\Controllers\PartnerController::class, 'tree'])->name('partner_tree');
    Route::get('/check/list', [App\Http\Controllers\PartnerController::class, 'checkList'])->name('check_list');
});

Route::prefix('charge')->group(function () {
    Route::get('/', [App\Http\Controllers\ChargeController::class, 'list'])->name('charge_list');
    Route::get('/search', [App\Http\Controllers\ChargeController::class, 'searchList'])->name('charge_search_list');

    Route::get('/set_ready', [App\Http\Controllers\ChargeController::class, 'setReadyProc']);
    Route::get('/set_done', [App\Http\Controllers\ChargeController::class, 'setDoneProc']);
    Route::get('/set_delete', [App\Http\Controllers\ChargeController::class, 'setDeleteProc']);
    Route::get('/set_hide', [App\Http\Controllers\ChargeController::class, 'setHideProc']);
});

Route::prefix('excharge')->group(function () {
    Route::get('/', [App\Http\Controllers\ExchargeController::class, 'list'])->name('excharge_list');
    Route::get('/search', [App\Http\Controllers\ExchargeController::class, 'searchList'])->name('excharge_search_list');

    Route::get('/set_ready', [App\Http\Controllers\ExchargeController::class, 'setReadyProc']);
    Route::get('/set_done', [App\Http\Controllers\ExchargeController::class, 'setDoneProc']);
    Route::get('/set_delete', [App\Http\Controllers\ExchargeController::class, 'setDeleteProc']);
    Route::get('/set_hide', [App\Http\Controllers\ExchargeController::class, 'setHideProc']);
});


Route::prefix('betting')->group(function () {
    Route::get('/', [App\Http\Controllers\BettingController::class, 'list'])->name('betting_list');
    Route::prefix('view')->group(function () {
        Route::get('/', [App\Http\Controllers\BettingController::class, 'view'])->name('betting_view');
        Route::get('/live', [App\Http\Controllers\BettingController::class, 'live_view'])->name('betting_live_view');
        Route::get('/live_sub', [App\Http\Controllers\BettingController::class, 'live_sub_view'])->name('betting_live_sub_view');
    });
    Route::get('/gameList', [App\Http\Controllers\BettingController::class, 'gameList'])->name('gameList');
    Route::prefix('push')->group(function () {
        Route::get('/', [App\Http\Controllers\BettingController::class, 'viewPush'])->name('push_view');
        Route::get('/detail', [App\Http\Controllers\BettingController::class, 'viewPushDetail'])->name('push_detail');
    });

    Route::prefix('adjust')->group(function () {
        Route::get('/cancel', [App\Http\Controllers\BettingController::class, 'cancel_adjust'])->name('cancel_adjust');
        Route::post('/update', [App\Http\Controllers\BettingController::class, 'update_adjust'])->name('update_adjust');
        Route::get('/delete', [App\Http\Controllers\BettingController::class, 'delete_adjust'])->name('delete_adjust');
    });

    Route::get('/ranking_all', [App\Http\Controllers\BettingController::class, 'ranking_all'])->name('ranking_all');
    Route::get('/find_not_allot', [App\Http\Controllers\BettingController::class, 'find_not_allot'])->name('find_not_allot');
    Route::get('/find_not_allotLive', [App\Http\Controllers\BettingController::class, 'find_not_allotLive'])->name('find_not_allotLive');

    Route::prefix('user_fight_comm_rates')->group(function () {
        Route::get('/', [App\Http\Controllers\BettingController::class, 'user_fight_comm_rates'])->name('user_fight_comm_rates');
        Route::post('/proc', [App\Http\Controllers\BettingController::class, 'user_fight_comm_rates_proc'])->name('user_fight_comm_rates_proc');
        Route::get('/add', [App\Http\Controllers\BettingController::class, 'user_fight_comm_rates_add'])->name('user_fight_comm_rates_add');
        Route::get('/edit', [App\Http\Controllers\BettingController::class, 'user_fight_comm_rates_edit'])->name('user_fight_comm_rates_edit');
        Route::get('/delete', [App\Http\Controllers\BettingController::class, 'user_fight_comm_rates_delete'])->name('user_fight_comm_rates_delete');
    });

    Route::prefix('stat_calendars')->group(function () {
        Route::get('/', [App\Http\Controllers\BettingController::class, 'stat_calendars'])->name('stat_calendars');
        Route::get('/month', [App\Http\Controllers\BettingController::class, 'stat_calendars_month'])->name('stat_calendars_month');
    });

});
