@extends('layouts.master-without-nav')
@section('content')
    @php
        if (\Session::has('form-board')) {
            $msginfo = Session::get('form-board');
        }
    @endphp
    @if (\Session::has('result'))
        @if (\Session::get('result'))
            <div id="flashMessage" class="message success" >쪽지 발송 성공!</div>
        @else
            <div id="flashMessage" class="message error" >쪽지 발송 실패!</div>
        @endif
    @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-3">전체 쪽지보내기</h5>
                    <p class="text-muted mb-0">정상회원에게만 발송됩니다.</p>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <label for="MessageTarget" class="form-label">대상</label>
                            <select name="user_type" class="form-select" id="MessageTarget" data-choices data-choices-search-false>
                                <option value="">==선택하세요==</option>
                                <option value="0" @if ($msginfo['user_type'] ?? '' == '0') selected @endif>전체회원</option>
                                <option value="1" @if ($msginfo['user_type'] ?? '' == '1') selected @endif>최상위파트너</option>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <label for="importance" class="form-label">중요도</label>
                            <select class="form-select" id="MessagePriority" data-choices data-choices-search-false name="importance">
                                <option value="0" @if ($msginfo['importance'] ?? '' == 0) selected @endif>일반</option>
                                <option value="1" @if ($msginfo['importance'] ?? '' == 1) selected @endif>중요</option>
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label for="choose-templet" class="form-label">템플릿</label>
                            <select id="choose-templet" class="form-select">
                                <option value="">== 템플릿 ==</option>
                            @foreach ($tempRecord as $record)
                                <option value="{{$record->id}}">{{$record->title}}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="button" onclick="loadTemplet();" class="btn btn-outline-primary">불러오기</button>
                                <button type="button" onclick="popupCenter('{{route('inquiries_templets')}}', '템플릿관리', 800, 600);" class="btn btn-outline-info">관리</button>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <label for="MessageTitle" class="form-label">제목 <span class="text-danger">*</span></label>
                            <input name="title" class="form-control" maxlength="100" type="text" id="MessageTitle" value="{{$msginfo['title'] ?? ''}}" required="required">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <label for="MessageTitle" class="form-label">내용 <span class="text-danger">*</span></label>
                            <textarea name="contents" class="textarea_style_00 form-control" cols="30" rows="6" id="MessageContents" required="required">{{$msginfo['contents'] ?? ''}}</textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-2">
                            <input type="submit" value="보내기" class="btn btn-primary">
                        </div>
                    </div>
                </div>
            </div>
    <script>
        function loadTemplet() {
            var val = $("#choose-templet option:selected").val();
            if(val) {
                $.ajax({
                    dataType: "json",
                    method: 'get',
                    data: {
                        inquiry_templet_id: val,
                    },
                    url: "/ajax/inquery/templets",
                    success: function(ret) {
                        try {
                            if(ret.state == 'ok') {
                                $("#MessageTitle").val(ret.title);
                                $("#MessageContents").val(ret.content);
                            }
                        } catch(e) {}
                    },
                    error: function(e) {
                        console.log(e);
                    },
                    complete: function() {}
                });
            }
        }
    </script>
@endsection
