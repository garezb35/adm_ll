@extends('layouts.master-without-nav')
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-header">
                <h5 class="card-title mb-0">{{$userinfo->userid}}({{$userinfo->nickname}}) 수수료 로그</h5>
            </div>
        </div>
        <div class="card-body">
            <form method="get" action="" id="form-search">
                <input type="hidden" name="userid" value="{{$userid}}"/>
                <div class="row mb-3">
                    <div class="col-sm-2">
                        <label for="gameType">@lang('translation.game-type')</label>
                        <select class="form-control" id="gameType" name="gameType" data-choices
                                data-choices-search-false data-choices-removeItem>
                            <option value="pwb" @if ($gameType == 'pwb') selected @endif>파워볼</option>
                            <option value="live" @if ($gameType == 'live') selected @endif>카지노</option>
                            <option value="slot" @if ($gameType == 'slot') selected @endif>슬롯</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label for="gameItem">@lang('translation.game')</label>
                        <select class="form-control" id="gameItem" name="gameItem" data-choices
                                data-choices-search-false data-choices-removeItem>
                            <option value="">@lang('translation.total')</option>
                            @if ($gameType == 'pwb')
                                @foreach ($gameRecord as $record)
                                    <option @if ($gameItem == $record->game_code) selected
                                            @endif value="{{$record->game_code}}">{{$record->game_name}}</option>
                                @endforeach
                            @else
                                @foreach ($gameRecord as $record)
                                    <option @if ($gameItem == $record->thirdPartyCode) selected
                                            @endif value="{{$record->thirdPartyCode}}">{{$record->tKR}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class=" col-sm-3">
                        <label for="startDate">날짜지정</label>
                        <div>
                            <input type="text" class="form-control" id="startDate"
                                   data-provider="flatpickr"
                                   data-date-format="Y-m-d" placeholder="Select date" name="startDate"
                                   data-range-date="true"
                                   value="{{$startDate}}">
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-secondary w-100"><i
                                    class="bi bi-funnel align-baseline me-1"></i> @lang('translation.filter')
                            </button>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <label>&nbsp;</label>
                        <blockquote class="blockquote custom-blockquote blockquote-danger rounded mb-0">
                            <p>수수료계: <span class="text-danger">{{number_format($sum_fee)}}</span></p>
                        </blockquote>
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                <table class="display table table-bordered table-nowrap" style="width:100%">
                    <thead>
                    <tr>
                        <th colspan="3">게임정보</th>
                        <th colspan="2">배팅</th>
                        <th rowspan="2">수혜업체</th>
                        <th colspan="2">배팅수수료</th>
                        <th rowspan="2">결과</th>
                    </tr>
                    <tr>
                        <th>게임</th>
                        <th>날짜</th>
                        <th>회차</th>
                        <th>회원</th>
                        <th>배팅</th>
                        <th>요율(%)</th>
                        <th>몫</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if ($gameType != 'pwb')
                        @foreach ($feeRecord as $record)
                            <tr>
                                <td><b>{{$record->gameinfo->thirdPartyInfo}}</b></td>
                                <td>{{date('Y-m-d H:i:s', strtotime($record->transTime))}}</td>
                                <td>{{$record->transID}}</td>
                                <td><b>{{$record->userInfo->userid}}({{$record->userInfo->nickname}})</b></td>
                                <td style="text-align: right;">{!! printColorMoney($record->bet_money) !!}</td>
                                <td><b>{{$userinfo->userid}}({{$userinfo->nickname}})</b></td>
                                <td>{{floatval($record->fee_rate)}}</td>
                                <td style="text-align: right;"><b>{{floatval($record->fee_amount)}}</b></td>
                                <td>@if ($record->status == 2)
                                        취소
                                    @else
                                        배당
                                    @endif</td>
                            </tr>
                        @endforeach
                    @else
                        @foreach ($feeRecord as $record)
                            <tr>
                                <td>{{$record->gameinfo->game_name}}</td>
                                <td>{{date('Y-m-d', strtotime($record->created_at))}}</td>
                                <td>{{$record->bet_round}}</td>
                                <td>{{$record->userInfo->userid}}({{$record->userInfo->nickname}})</td>
                                <td style="text-align: right;">{!! printColorMoney($record->bet_amount) !!}</td>
                                <td>{{$userinfo->userid}}({{$userinfo->nickname}})</td>
                                <td>{{$record->fee_rate}}</td>
                                <td style="text-align: right;">{!! printColorMoney(floor($record->fee_amount)) !!}</td>
                                <td>@if ($record->status == 0)
                                        취소
                                    @else
                                        배당
                                    @endif</td>
                            </tr>
                        @endforeach
                    @endif
                    @if (empty($feeRecord) || $feeRecord->count() == 0)
                        <tr>
                            <td colspan="20">자료가 없습니다.</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
                @if (!empty($feeRecord))
                    <div class="pagination-part">
                        {!! $feeRecord->withQueryString()->links() !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <script>
        var gameType = document.getElementById("gameType");
        var gameItem = document.getElementById("gameItem");
        gameType.addEventListener(
            'change',
            function (event) {
                gameItem.value = "";
                $('#form-search').submit();
            },
            false,
        );
        gameItem.addEventListener(
            'change',
            function (event) {
                $('#form-search').submit();
            },
            false,
        );
    </script>
@endsection
