@extends('layouts.master-without-nav')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                회원 로그인 내역
            </h5>
        </div>
        <div class="card-body">
            <form method="get" action="#" id="form-search">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="search_opt" class="form-label">검색조건</label>
                        <select class="form-select" data-choices name="search_opt" id="search_opt">
                            <option value="1" @if ($search_opt == '1') selected @endif>아이디</option>
                            <option value="2" @if ($search_opt == '2') selected @endif>IP주소</option>
                            <option value="3" @if ($search_opt == '3') selected @endif>접속도메인</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <input id="seatch-txt" class="form-control" value="{{$search_text}}"
                               placeholder="검색어 입력"
                              name="search_text" >
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-secondary w-100"><i
                                    class="bi bi-funnel align-baseline me-1"></i> @lang('translation.filter')
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="display table table-bordered table-nowrap" style="width:100%">
                            <thead>
                            <tr>
                                <th>아이디(닉네임)</th>
                                <th>접속도메인</th>
                                <th>IP주소</th>
                                <th>일시</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($logRecord as $record)
                                <tr>
                                    <td>
                            <span class="user-context-menu" data-user-id="{{$record->userinfo->id}}" data-username="{{$record->userinfo->userid}}">
                                <span style="font-weight: bold;color: {{'#' . $record->userinfo->user_color}}">{{$record->userinfo->userid}}({{$record->userinfo->nickname}})</span>
                            </span>
                                    </td>
                                    <td>{{$record->domain}}</td>
                                    <td>{{$record->ip_addr}}</td>
                                    <td>{{$record->created_at}}</td>
                                </tr>
                            @endforeach
                            @if ($logRecord->count() == 0)
                                <tr><td colspan="20">@lang('translation.nohistory')</td></tr>
                            @endif
                            </tbody>
                        </table>
                        <div>
                            {!! $logRecord->withQueryString()->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
