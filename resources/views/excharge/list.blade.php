@extends('layouts.master-without-nav')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                환전 관리
            </h5>
            <div class="card-body">
                <form method="get" action="" id="form-search">
                    <input type="hidden" name="sort" id="sort-arrow" value="">
                    <input type="hidden" name="type" id="sort-type" value="">
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label class="form-label" for="start_date">날짜검색</label>
                            <div>
                                <input type="text" class="form-control" id="start_date"
                                       data-provider="flatpickr"
                                       data-date-format="Y-m-d" placeholder="Select date" name="start_date"
                                       data-range-date="true"
                                       value="{{$start_date}}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="state">상태</label>
                            <select name="state" class="form-select" data-choices id="state">
                                <option value="">전체</option>
                                <option value="1" @if ($state == "1") selected @endif>승인대기</option>
                                <option value="0" @if ($state == "0") selected @endif>요청</option>
                                <option value="2" @if ($state == "2") selected @endif>처리</option>
                                <option value="4" @if ($state == "4") selected @endif>취소</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="req_type">분류</label>
                            <select name="req_type" class="form-select" data-choices id="req_type">
                                <option value="">전체</option>
                                <option value="0" @if ($req_type == "0") selected @endif>회원</option>
                                <option value="2" @if ($req_type == "2") selected @endif>관리자</option>
                                <option value="4" @if ($req_type == "4") selected @endif>매장처리</option>
                                <option value="8" @if ($req_type == "8") selected @endif>매장-하부</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="seatch-txt">검색</label>
                            <input id="seatch-txt" class="form-control" value="{{$username}}" placeholder="아이디로 검색"
                                   name="username">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <blockquote class="blockquote custom-blockquote blockquote-danger rounded mb-0"
                                        style="padding: 8px">
                                <p>총액 {{number_format($nSumMoney)}}</p>
                            </blockquote>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-secondary w-100"><i
                                    class="bi bi-funnel align-baseline me-1"></i> @lang('translation.filter')
                            </button>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive" id="data-tables-part">
                            @include('excharge.table', ['sort' => $sort, 'type' => $type,
                                        'chargeRecord' => $chargeRecord
                                    ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
