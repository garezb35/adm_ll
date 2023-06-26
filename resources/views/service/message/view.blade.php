@extends('layouts.master-without-nav')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">쪽지 읽기</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display table table-bordered table-nowrap" style="width:100%">
                            <tr>
                                <th>수신자</th>
                                <td>{{$msginfo->userinfo->userid}}({{$msginfo->userinfo->nickname}})</td>
                            </tr>
                            <tr>
                                <th>제목</th>
                                <td>{{$msginfo->title}}</td>
                            </tr>
                            <tr>
                                <th>내용</th>
                                <td>
                                    <div style="min-height:100px;white-space: pre-wrap;">{{$msginfo->content}}</div>
                                </td>
                            </tr>
                            <tr>
                                <th>받은날짜</th>
                                <td>{{$msginfo->createByDtm}}</td>
                            </tr>
                            <tr>
                                <th>읽은날짜</th>
                                <td>{{$msginfo->readByDtm}}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
