@extends('layouts.master-without-nav')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{$userinfo->userid}}({{$userinfo->nickname}}) 회원 수수료율 변경 로그</h5>
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
                                    @if (isset($record->adminfo->userid))
                                        <td>{{$record->adminfo->userid}}<br>{{$record->ip_addr}}</td>
                                    @else
                                        <td>삭제된 관리자<br>{{$record->ip_addr}}</td>
                                    @endif
                                    <td>
                                        <span class="user-context-menu" data-user-id="{{$record->userinfo->id}}"
                                              data-username="{{$record->userinfo->userid}}">
                                            <span style="font-weight: bold;color: {{'#'.$record->userinfo->user_color}}">{{$record->userinfo->userid}}({{$record->userinfo->nickname}})</span>
                                        </span>
                                    </td>
                                    <td style="max-width: 500px"><p style="white-space: break-spaces">{{$record->contents}}</p></td>
                                    <td>{{$record->created_at}}</td>
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
