@extends('layouts.master-without-nav')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{$userinfo->userid}}({{$userinfo->nickname}}) 루징적립 로그</h5>
        </div>
        <div class="card-body">
            <form method="get" action="" id="form-search">
                <input type="hidden" name="userid" value="{{$userid}}"/>
                <div class="row mb-3">
                    <div class="col-sm-2 col-xs-12">
                        <label for="gameType">@lang('translation.game-type')</label>
                        <select class="form-control" id="gameType" name="gameType" data-choices
                                data-choices-search-false data-choices-removeItem>
                            <option value="all"
                                    @if ($gameType == 'all') selected @endif>@lang('translation.total')</option>
                            <option value="live"
                                    @if ($gameType == 'live') selected @endif>@lang('translation.casino')</option>
                            <option value="slot"
                                    @if ($gameType == 'slot') selected @endif>@lang('translation.slot')</option>
                        </select>
                    </div>
                    <div class="col-sm-2 col-xs-12">
                        <label for="gameItem">@lang('translation.game')</label>
                        <select class="form-control" id="gameItem" name="gameItem" data-choices
                                data-choices-search-false data-choices-removeItem>
                            <option value="">@lang('translation.total')</option>
                            @foreach ($gameRecord as $record)
                                <option @if ($gameItem == $record->thirdPartyCode) selected
                                        @endif value="{{$record->thirdPartyCode}}">{{$record->tKR}}</option>
                            @endforeach
                        </select>
                    </div>
                    <!--end col-->
                    <div class="col-sm-3 col-xs-12">
                        <label for="startDate">날짜지정</label>
                        <div>
                            <input type="text" class="form-control" id="startDate"
                                   data-provider="flatpickr"
                                   data-date-format="Y-m-d" data-range-date="true" placeholder="Select date"
                                   name="startDate"
                                   value="{{$startDate}}">
                        </div>
                    </div>
                    <div class="col-sm-2 col-xs-12">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-secondary w-100"><i
                                    class="bi bi-funnel align-baseline me-1"></i> @lang('translation.filter')
                            </button>
                        </div>
                    </div>
                    <div class="col-sm-3 col-xs-12">
                        <label>&nbsp;</label>
                        <div>
                            <blockquote class="blockquote custom-blockquote blockquote-danger rounded mb-0">
                                <p class="mb-0">루징합산: {!! printColorMoney($sum_fee) !!}</p>
                            </blockquote>
                        </div>
                    </div>
                </div>
            </form>
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="display table table-bordered table-nowrap">
                            <thead>
                                <tr>
                                    <th colspan="3">게임정보</th>
                                    <th colspan="3">배팅정보</th>
                                    <th rowspan="2">수혜업체</th>
                                    <th colspan="3">루징</th>
                                    <th rowspan="2">상태</th>
                                </tr>
                                <tr>
                                    <th>게임</th>
                                    <th>날짜</th>
                                    <th>회차</th>
                                    <th>회원</th>
                                    <th>배팅</th>
                                    <th>당첨금</th>
                                    <th>루징(%)</th>
                                    <th>요율(%)</th>
                                    <th>몫</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($feeRecord as $record)
                                <tr>
                                    <td><b>{{$record->gameinfo->thirdPartyInfo}}</b></td>
                                    <td>{{date('Y-m-d H:i:s', strtotime($record->transTime))}}</td>
                                    <td><b>{{$record->round}}</b></td>
                                    <td><b>{{$record->userinfo->userid}}({{$record->userinfo->nickname}})</b></td>
                                    <td style="text-align: right;">{{number_format($record->bet_money)}}</td>
                                    <td style="text-align: right;">{!! printColorMoney($record->win_money) !!}</td>
                                    <td><b>{{$userinfo->userid}}({{$userinfo->nickname}})</b></td>
                                    <td>{{floatval($record->lose_rate)}}</td>
                                    <td>{{floatval($record->fee_rate)}}</td>
                                    <td style="text-align: right;">
                                        @if ($record->lose_amount > 0)
                                            <b>{!! printColorMoney(floor($record->lose_amount)) !!}</b></td>
                                    @else
                                        <b>{!! printColorMoney(ceil($record->lose_amount)) !!}</b></td>
                                    @endif
                                    <td>
                                        @if ($record->init_lose == 1)
                                            정산
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @if (empty($feeRecord) || $feeRecord->count() == 0)
                                <tr><td colspan="20">자료가 없습니다.</td></tr>
                            @endif
                            </tbody>
                        </table>
                        @if (!empty($feeRecord))
                            <div>
                                {!! $feeRecord->withQueryString()->links() !!}
                            </div>
                        @endif
                    </div>
                </div>
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
