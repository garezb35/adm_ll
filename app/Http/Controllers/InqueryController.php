<?php

namespace App\Http\Controllers;

use App\Models\SvcAsk;
use App\Models\SvcAskTemp;
use App\Models\SvcMsg;
use App\Models\User;
use Illuminate\Http\Request;

class InqueryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function getInfo(Request $request)
    {
        $data['start_date'] = $request->start_date ?? date('Y-m-d');
        $data['end_date'] = $request->end_date ?? date('Y-m-d');
        $data['state'] = $request->state ?? '';
        $data['search_opt'] = $request->search_opt ?? '';
        $data['search_text'] = $request->search_text ?? '';

        $data['sort_reg_view'] = '';
        $data['sort_reg_link'] = 'desc';
        $data['sort_reply_view'] = '';
        $data['sort_reply_link'] = 'desc';

        if ($request->type != '') {
            if ($request->type == 'pending') {
                $nStatus = 1;
                $data['page_type'] = '대기';
            } else if ($request->type == 'ready') {
                $nStatus = 0;
                $data['page_type'] = '신규';
            }
            $tempQuery = SvcAsk::where('status', $nStatus)
                ->where('isDeleted', 0);
        } else {
            $tempQuery = SvcAsk::whereDate('created_at', '>=', $data['start_date'] . ' 00:00:00')
                ->whereDate('created_at', '<=', $data['end_date'] . ' 23:59:59')
                ->where('isDeleted', 0);
            if ($data['state'] != '')
                $tempQuery->where('status', $data['state']);
            if ($data['search_text'] != '') {
                switch ($data['search_opt']) {
                    case 1:
                        $temp = User::where('userid', $data['search_text'])
                            ->first();
                        $tempQuery->where('userid', $temp->id ?? 0);
                        break;
                    case 2:
                        $temp = User::where('nickname', $data['search_text'])
                            ->first();
                        $tempQuery->where('userid', $temp->id ?? 0);
                        break;
                    case 3:
                        $tempQuery->where('title', 'LIKE', '%' . $data['search_text'] . '%');
                        break;
                    case 4:
                        $tempQuery->where('contents', 'LIKE', '%' . $data['search_text'] . '%');
                        break;
                }
            }
        }

        $snzSortType = '';
        if ($request->sort_reply != '') {
            $data['sort_reply_view'] = '';
            if ($request->sort_reply == 'asc') {
                $data['sort_reply_view'] = 'asc';
                $data['sort_reply_link'] = 'desc';
            }
            if ($request->sort_reply == 'desc') {
                $data['sort_reply_view'] = 'desc';
                $data['sort_reply_link'] = 'asc';
            }
            if ($data['sort_reply_view'] != '') {
                $snzSortType = "reply_at";
                $tempQuery->orderBy('reply_at', $data['sort_reply_view']);
            }
        }
        if ($snzSortType == '' && $request->sort_reg == '') {
            $request->sort_reg = 'desc';
        }
        if ($request->sort_reg != '') {
            $data['sort_reg_view'] = '';
            if ($request->sort_reg == 'asc') {
                $data['sort_reg_view'] = 'asc';
                $data['sort_reg_link'] = 'desc';
            }
            if ($request->sort_reg == 'desc') {
                $data['sort_reg_view'] = 'desc';
                $data['sort_reg_link'] = 'asc';
            }
            if ($data['sort_reg_view'] != '') {
                $snzSortType = "created_at";
                $tempQuery->orderBy('created_at', $data['sort_reg_view']);
            }
        }
        $inqRecord = $tempQuery->paginate(20);
        for ($index = 0; $index < count($inqRecord); $index++) {
            $record = $inqRecord[$index];
            $headinfo = User::with(['joininfo' => function ($query) {
                $query->select('id', 'userid', 'nickname', 'user_color');
            }, 'headinfo' => function ($query) {
                $query->select('id', 'userid', 'nickname', 'user_color');
            }])
                ->where('id', $record->userid)
                ->select('id', 'userid', 'nickname', 'user_color', 'bankmaster', 'joinid', 'masterid')
                ->first();

            if (!empty($headinfo)) {
                $inqRecord[$index]['userinfo'] = $headinfo;
            }
        }

        $data['inqRecord'] = $inqRecord;

        return $data;
    }

    public function list(Request $request)
    {
        $data = $this->getInfo($request);
        return view('inquiries.index.list', $data);
    }

    public function search(Request $request)
    {
        $data = $this->getInfo($request);
        return view('inquiries.index.search', $data);
    }

    public function deleteAllView()
    {
        return view('inquiries.delete_all');
    }

    public function deleteAllProc(Request $request)
    {
        $isProc = false;
        if ($request->super_pwd == auth()->user()->password_show) {
            if ($request->target_user != '' && $request->delete_opt1 == 2) {
                $userinfo = User::where('userid', $request->target_user)
                    ->first();
                if (!empty($userinfo)) {
                    if ($request->delete_opt2 == 1) {
                        SvcAsk::where('status', 3)
                            ->where('userid', $userinfo->id)
                            ->update(['isDeleted' => 1]);
                        $isProc = true;
                    }
                    if ($request->delete_opt2 == 2) {
                        SvcAsk::where('userid', $userinfo->id)
                            ->update(['isDeleted' => 1]);
                        $isProc = true;
                    }
                }
            }
            if ($request->delete_opt1 == 1) {
                if ($request->delete_opt2 == 1) {
                    SvcAsk::where('status', 3)
                        ->update(['isDeleted' => 1]);
                    $isProc = true;
                }
                if ($request->delete_opt2 == 2) {
                    SvcAsk::query()->update(['isDeleted' => 1]);
                    $isProc = true;
                }
            }
        }
        return back()->with('result', $isProc)
            ->with('form-ask', $request->all());
    }

    public function readyProc(Request $request)
    {
        SvcAsk::where('id', $request->id)
            ->update(['status' => 1]);
        return back();
    }

    public function editView(Request $request)
    {
        $data['inqinfo'] = SvcAsk::where('id', $request->id)
            ->first();
        if (empty($data['inqinfo']))
            return '삭제된 문의내역입니다.';
        $data['tempRecord'] = SvcAskTemp::orderBy('created_at', 'desc')->get();
        return view('inquiries.edit', $data);
    }

    public function editProc(Request $request)
    {
        SvcAsk::where('id', $request->id)
            ->update([
                "reply_title" => $request->reply_title,
                "response" => $request->reply_contents,
                "status" => 3,
                "reply_at" => date("Y-m-d H:i:s")
            ]);

        if ($request->reply_alarm == 1) {
            $userinfo = User::where('id', $request->userid)->first();

            SvcMsg::insert([
                "adminid" => auth()->user()->id,
                "userid" => $userinfo->id,
                "title" => '1:1문의회답',
                "content" => $request->reply_contents
            ]);
        }

        return back()->with('result', 'success');
    }

    public function deleteProc(Request $request)
    {
        SvcAsk::where('id', $request->id)
            ->delete();

        return back();
    }

    public function templets()
    {
        $data['tempRecord'] = SvcAskTemp::orderBy('created_at', 'desc')->get();

        return view('inquiries.template', $data);
    }

    public function loadTemplate(Request $request)
    {
        $askinfo = SvcAskTemp::where('id', $request->inquiry_templet_id)
            ->first();
        return json_encode(array(
            'stat' => 'ok',
            'title' => $askinfo->title,
            'contents' => $askinfo->content,
        ));
    }

    public function templetsAddView()
    {
        return view('inquiries.template_add');
    }

    public function templetsAddProc(Request $request)
    {
        SvcAskTemp::insert([
            'title' => $request->title,
            'content' => $request->contents,
        ]);

        return redirect()->route('inquiries_templets');
    }

    public function templetsEditView(Request $request)
    {
        $data['askinfo'] = SvcAskTemp::where('id', $request->id)
            ->first();
        return view('inquiries.template_add', $data);
    }

    public function templetsEditProc(Request $request)
    {
        SvcAskTemp::where('id', $request->id)
            ->update([
                'title' => $request->title,
                'content' => $request->contents,
            ]);

        return redirect()->route('inquiries_templets');
    }

    public function templetsEditDelete(Request $request)
    {
        SvcAskTemp::where('id', $request->id)
            ->delete();

        return redirect()->route('inquiries_templets');
    }
}
