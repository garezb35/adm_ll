@extends('layouts.master-without-nav')
@section('content')
    <div>
        @if (\Session::has('result'))
            @if (\Session::get('result') == 'success')
                <div id="flashMessage" class="message success" style=''>쪽지 발송 성공!</div>
            @else
                <div id="flashMessage" class="message error" style=''>쪽지 발송 실패! 수신자가 존재하지 않습니다.</div>
            @endif
        @endif
    </div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                쪽지보내기
            </h5>
        </div>
        <div class="card-body">
            <form action="" novalidate="novalidate" id="MessageAddForm" method="post" accept-charset="utf-8">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label" for="MessageRecvUserUsername">수신</label>
                        <input name="userid" value="{{$beforeinfo['userid'] ?? $userid}}" type="text"
                               id="MessageRecvUserUsername" class="form-control">
                        <div class="form-check form-switch mt-1">
                            <input class="form-check-input" type="checkbox" role="switch" id="MessageInclude"
                                   name="include" value="1" {{ ($beforeinfo['include'] ?? '') ? 'checked' : '' }}>
                            <label class="form-check-label" for="MessageInclude">하부포함</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="MessagePriority">중요도</label>
                        <select name="importance" id="MessagePriority" data-choices class="form-control">
                            <option value="0"
                                    @if (!is_null($beforeinfo) && $beforeinfo['importance'] == 0) selected @endif>일반
                            </option>
                            <option value="1"
                                    @if (!is_null($beforeinfo) && $beforeinfo['importance'] == 1) selected @endif>중요
                            </option>
                        </select>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            "중요"로 보낸 쪽지는 강제로 읽어야 합니다.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="choose-templet" class="form-label">템플릿</label>
                        <select id="choose-templet" class="form-select">
                            <option value="">== 템플릿 ==</option>
                            @foreach ($tempRecord as $record)
                                <option value="{{$record->id}}">{{$record->title}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="button" onclick="loadTemplet();" class="btn btn-outline-primary">불러오기</button>
                            <button type="button"
                                    onclick="popupCenter('{{route('inquiries_templets')}}', '템플릿관리', 800, 600);"
                                    class="btn btn-outline-info">관리
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label" for="MessageTitle">제목</label>
                        <input name="title" class="form-control" maxlength="100" type="text" id="MessageTitle"
                               value="{{$beforeinfo['title'] ?? ''}}" required="required">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label" for="MessageContents">내용</label>
                        <textarea name="txt_content" class="form-control" cols="30" rows="6" id="MessageContents"
                                  required="required">{{$beforeinfo['txt_content'] ?? ''}}</textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <input type="submit" value="보내기" class="btn btn-primary">
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        function loadTemplet() {
            var val = $("#choose-templet option:selected").val();
            if (val) {
                $.ajax({
                    dataType: "json",
                    method: 'get',
                    data: {
                        inquiry_templet_id: val,
                    },
                    url: "/ajax/inquery/templets",
                    success: function (ret) {
                        try {
                            if (ret.state == 'ok') {
                                $("#MessageTitle").val(ret.title);
                                $("#MessageContents").val(ret.content);
                            }
                        } catch (e) {
                        }
                    },
                    error: function (e) {
                        console.log(e);
                    },
                    complete: function () {
                    }
                });
            }
        }
    </script>
@endsection
