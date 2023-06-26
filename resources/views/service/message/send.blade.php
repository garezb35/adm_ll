@extends('layouts.master-without-nav')
@section('content')
    @php
        if (\Session::has('form-msg')) {
            $userinfo = Session::get('form-msg');
        }
    @endphp
    @if (\Session::has('result'))
        @if (\Session::get('result'))
            <div id="flashMessage" class="message success" >쪽지 발송 성공!</div>
        @else
            <div id="flashMessage" class="message error" >쪽지 발송 실패!</div>
        @endif
    @endif
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xxl-4">
                            <h5 class="card-title mb-3">쪽지보내기</h5>
                            <p class="text-muted mb-0">"중요"로 보낸 쪽지는 강제로 읽어야 합니다.</p>
                        </div>
                        <!--end col-->
                        <div class="col-xxl-8">
                            <form action="" id="MessageAddForm" method="post" accept-charset="utf-8">
                                @csrf
                                <div class="row mb-3">
                                    <div class="col-lg-4">
                                        <div>
                                            <label for="MessageRecvUserUsername" class="form-label">수신</label>
                                            <input type="text" class="form-control" placeholder="Enter manufacturer name" value="{{$userinfo['userid'] ?? ''}}" id="MessageRecvUserUsername" name="userid">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <label for="importance" class="form-label">중요도</label>
                                        <select class="form-select" id="MessagePriority" data-choices data-choices-search-false name="importance">
                                            <option value="0" @if ($userinfo['importance'] ?? '' == 0) selected @endif>일반</option>
                                            <option value="1" @if ($userinfo['importance'] ?? '' == 1) selected @endif>중요</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-12">
                                        <label for="MessageTitle" class="form-label">제목 <span class="text-danger">*</span></label>
                                        <input name="title" class="form-control" maxlength="100" type="text" id="MessageTitle" value="{{$userinfo['title'] ?? ''}}" required="required">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-12">
                                        <label for="MessageTitle" class="form-label">내용 <span class="text-danger">*</span></label>
                                        <textarea name="contents" class="textarea_style_00 form-control" cols="30" rows="6" id="MessageContents" required="required">{{$userinfo['contents'] ?? ''}}</textarea>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-2">
                                        <input type="submit" value="보내기" class="btn btn-primary">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!--end row-->
                </div>
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->
    <script>

    </script>
@endsection
