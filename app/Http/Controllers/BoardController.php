<?php

namespace App\Http\Controllers;


use App\Models\SvcBoards;
use App\Models\SiteDomain;
use App\Models\SvcAskTemp;
use Illuminate\Http\Request;

class BoardController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    //
    public function board_list(Request $request)
    {
        $data['boardRecord'] = SvcBoards::orderBy('created_at', 'desc')
            ->paginate(10);
        return view('board.list', $data);
    }

    public function board_edit_view(Request $request)
    {
        $data['boardinfo'] = SvcBoards::where('id', $request->id)
            ->first();
        $data['domainRecord'] = SiteDomain::all();
        return view('board.add', $data);
    }

    public function board_add_view(Request $request)
    {
        $domainRecord = SiteDomain::all();
        return view('board.add', ['domainRecord' => $domainRecord]);
    }

    public function board_add_proc(Request $request)
    {
        $isInsert = SvcBoards::insert([
            'contents' => $request->contents,
            'title' => $request->title,
            'is_alert' => $request->is_alert,
            'is_banner' => $request->is_banner,
            'is_delete' => $request->is_delete,
            'hide_title' => $request->hide_title,
            'pos_top' => $request->pos_top,
            'pos_left' => $request->pos_left,
            'pos_width' => $request->pos_width,
            'pos_height' => $request->pos_height,
            'domain' => $request->domain ?? 0
        ]);

        return back()->with('result', $isInsert)
            ->with('form-board', $request->all());
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = $fileName . '_' . time() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('uploads'), $fileName);

            $ckeditor = $request->input('CKEditorFuncNum');
            $url = asset('uploads/' . $fileName);
            $msg = 'Image uploaded successfully';
            echo json_encode([
                'default' => $url,
                '500' => $url
            ]);
        }
        return null;
    }

    public function board_del_proc(Request $request)
    {
        SvcBoards::where('id', $request->id)
            ->delete();

        return back();
    }

    public function board_edit_proc(Request $request)
    {
        $isInsert = SvcBoards::where('id', $request->id)
            ->update([
                'contents' => $request->contents,
                'title' => $request->title,
                'is_alert' => $request->is_alert,
                'is_banner' => $request->is_banner,
                'is_delete' => $request->is_delete,
                'hide_title' => $request->hide_title,
                'pos_top' => $request->pos_top,
                'pos_left' => $request->pos_left,
                'pos_width' => $request->pos_width,
                'pos_height' => $request->pos_height,
            ]);

        return back()->with('result', $isInsert)
            ->with('form-board', $request->all());
    }

    public function loadTemplate(Request $request){
        $askinfo = SvcAskTemp::where('id', $request->inquiry_templet_id)
            ->first();
        return json_encode(array('state' => 'ok', 'title' => $askinfo->title, 'content' => $askinfo->content));
    }
}
