@extends('layouts.master-without-nav')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{$userinfo->userid}}({{$userinfo->nickname}}) 배팅금액 제한 설정 로그</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display table table-bordered table-nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>관리자/IP</th>
                                    <th>회원</th>
                                    <th>저장내용</th>
                                    <th>일시</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($logRecord as $record)
                                <tr>
                                    <td>{{$record->adminfo->userid}}<br>{{$record->ip_addr}}</td>
                                    <td>
                                        <span class="user-context-menu" data-user-id="{{$record->userinfo->id}}"
                                              data-username="{{$record->userinfo->userid}}">
                                            <span style="font-weight: bold;color: {{'#'.$record->userinfo->user_color}}">{{$record->userinfo->userid}}({{$record->userinfo->nickname}})</span>
                                        </span>
                                    </td>
                                    <td>{{$record->contents}}</td>
                                    <td>{{date('Y-m-d H:i:s', strtotime($record->created_at))}}</td>
                                </tr>
                            @endforeach
                            @if ($logRecord->count() == 0)
                                <tr>
                                    <td colspan="5">자료가 없습니다</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                        <div class="pagination-part" style="text-align: center;">
                            {!! $logRecord->withQueryString()->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
