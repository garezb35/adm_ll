@extends('layouts.master-without-nav')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                정보수정내역
            </h5>
        </div>
        <div class="card-body">
            <form method="get" action="" id="form-search">
                <input type="hidden" name="id" value="{{$id}}"/>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="search_opt" class="form-label">날짜검색</label>
                        <div>
                            <input type="text" class="form-control" id="start_date"
                                   data-provider="flatpickr"
                                   data-date-format="Y-m-d" placeholder="Select date" name="start_date"
                                   data-range-date="true"
                                   value="{{$start_date}}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="seatch-txt">아이디</label>
                        <input id="seatch-txt" class="form-control" value="{{$search_text}}"
                               placeholder="검색어 입력"
                               name="search_text">
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
                                <th>관리자</th>
                                <th>IP주소</th>
                                <th>대상회원</th>
                                <th>원본</th>
                                <th>변경</th>
                                <th>수정일시</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($logRecord as $record)
                                <tr>
                                    <td>{{$record->adminfo->userid}}</td>
                                    <td>{{$record->ip_addr}}</td>
                                    <td>
                            <span class="user-context-menu" data-user-id="{{$record->userinfo->id}}"
                                  data-username="{{$record->userinfo->userid}}">
                                <span style='font-weight: bold;color: {{'#' . $record->userinfo->user_color}}'>{{$record->userinfo->userid}}({{$record->userinfo->nickname}})</span>
                            </span>
                                    </td>
                                    <td>{{$record->old_info}}</td>
                                    <td>{{$record->new_info}}</td>
                                    <td>{{$record->created_at}}</td>
                                </tr>
                            @endforeach
                            @if ($logRecord->count() == 0)
                                <tr>
                                    <td colspan="20">자료가 없습니다.</td>
                                </tr>
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
