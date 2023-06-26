@extends('layouts.master-without-nav')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-3">1:1문의 템플릿 작성하기</h5>
        </div>
        <div class="card-body">
            <form action="" id="InquiryTempletAddForm" method="post" accept-charset="utf-8">
                @csrf
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <label for="InquiryTempletTitle" class="form-label">응답제목</label>
                        <input name="title" value="{{$askinfo['title'] ?? ''}}" class="form-control" maxlength="100" type="text" id="InquiryTempletTitle"  required="required">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <label for="InquiryTempletContents" class="form-label">내용</label>
                        <textarea name="contents" class="textarea_style_00 form-control" cols="30" rows="6" id="InquiryTempletContents" required="required">{{$askinfo['content'] ?? ''}}</textarea>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <input type="submit" value="저장하기"  class="btn btn-primary"/>
                        <a href="{{route('inquiries_templets')}}" class="btn btn-secondary">목록으로</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
