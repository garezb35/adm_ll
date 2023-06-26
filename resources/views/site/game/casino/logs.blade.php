@extends('layouts.master-without-nav')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">게임 정보 변경 로그</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="display table table-bordered table-nowrap" style="width:100%">
                            <thead>
                            <tr>
                                <th>게임</th>
                                <th>관리자/IP</th>
                                <th>저장내용</th>
                                <th>일시</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($logRecord as $record)
                                @if (!empty($record->userinfo))
                                    <tr>
                                        <td>{{$cateinfo->thirdPartyInfo}}</td>
                                        <td>{{$record->userinfo->userid}}({{$record->ip_addr}})</td>
                                        <td style="text-align: left;"> {{$record->content}} </td>
                                        <td>{{$record->created_at}}</td>
                                    </tr>
                                @endif
                            @endforeach
                            @if (count($logRecord) == 0)
                                <tr>
                                    <td colspan='10'>내역이 없습니다.</td>
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
