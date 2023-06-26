<?php

namespace App\Http\Controllers;

use App\Models\SvcAskTemp;
use App\Models\SvcBoards;
use App\Models\SvcMsg;
use App\Models\User;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function message_list(Request $request)
    {
        $dateRange = getDateFromRangeDate($request->startDate ?? date('Y-m-d'));
        $date_opt = $request->date_opt ?? 'write';
        $is_read = $request->is_read ?? '';
        $startDate = $dateRange[0] . ' 00:00:00';
        $endDate = $dateRange[1] . ' 23:59:59';
        $type = $request->type ?? 'req';
        $sort = $request->sort ?? 'asc';

        $snzSort = $sort == 'asc' ? 'desc' : 'asc';
        switch ($type) {
            case 'req':
                $snzType = 'created_at';
                break;
            case 'read':
                $snzType = 'is_read';
                break;
            case 'unread':
                $snzType = 'read_at';
                break;
            case '':
                break;
        }
        if ($date_opt == 'write') {
            $tempQuery = SvcMsg::where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate);
        } else {
            $tempQuery = SvcMsg::where('read_at', '>=', $startDate)
                ->where('read_at', '<=', $endDate);
        }
        if ($is_read != '') {
            $tempQuery->where('is_read', $is_read);
        }
        $data = array(
            'date_opt' => $date_opt,
            'is_read' => $is_read,
            'startDate' => $request->startDate,
            'type' => $type,
            'sort' => $sort,
        );
        $data['msgRecord'] = $tempQuery->orderBy($snzType, $snzSort)->paginate(25);
        return view('service.message.list', $data);
    }

    public function message_del_all(Request $request)
    {
        SvcMsg::truncate();
        return back();
    }

    public function message_add_all_view()
    {
        $tempRecord = SvcAskTemp::orderBy('created_at', 'desc')->get();
        return view('service.message.add_all', ['tempRecord' => $tempRecord]);
    }

    public function message_add_all_proc(Request $request)
    {
        $tempQuery = User::where('verified', 1)
            ->where('rolecode', '0003');
        if ($request->user_type == 1) {
            $tempQuery->where('masterid', 0);
        }
        $listUsers = $tempQuery->get();

        $nAdmId = auth()->user()->id;
        $arrMessage = array();
        foreach ($listUsers as $user) {
            $msg = array(
                "adminid" => $nAdmId,
                "userid" => $user->id,
                "importance" => $request->importance,
                "title" => $request->title,
                "content" => $request->contents
            );
            array_push($arrMessage, $msg);
        }

        for ($i = 0; $i < count($arrMessage); $i += 200) {
            $arrTemp = array_slice($arrMessage, $i, 200);
            $isInsert = SvcMsg::insert($arrTemp);
        }

        return back()->with('result', $isInsert ?? 0)
            ->with('form-board', $request->all());
    }

    public function message_view(Request $request)
    {
        $data['msginfo'] = SvcMsg::with(['userinfo' => function ($query) {
            $query->select('id', 'userid', 'nickname');
        }])
            ->where('id', $request->id)
            ->first();
        return view('service.message.view', $data);
    }

    public function message_send_view(Request $request)
    {
        $data['userinfo'] = User::where('id', $request->userid)
            ->where('rolecode', '0003')
            ->where('verified', 1)
            ->select('id', 'userid', 'nickname')
            ->first();

        return view('service.message.send', $data);
    }

    public function message_send_proc(Request $request)
    {
        $isInsert = false;
        $userinfo = User::where('userid', $request->userid)
            ->where('rolecode', '0003')
            ->where('verified', 1)
            ->first();
        if (!empty($userinfo)) {
            $isInsert = SvcMsg::insert([
                "adminid" => auth()->user()->id,
                "userid" => $userinfo->id,
                "importance" => $request->importance,
                "title" => $request->title,
                "content" => $request->contents
            ]);
        }

        return back()->with('result', $isInsert)
            ->with('form-msg', $request->all());
    }

    public function message_del_proc(Request $request)
    {
        SvcMsg::where('id', $request->id)
            ->delete();

        return back();
    }

}
