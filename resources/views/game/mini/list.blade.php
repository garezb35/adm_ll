@extends('layouts.master')
@section('title')
    @lang('translation.mini-betting')
@endsection
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">@lang('translation.mini-betting')</h5>
        </div>
        <div class="card-body">
            <form method="get" action="" id="form-search">
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label class="form-label" for="gameType">게임형식</label>
                        <select data-choices id="gameType" name="gameType">
                            <option value="">전체</option>
                            @foreach ($gameRecord as $record)
                                @if (in_array($record->game_code, $gameList))
                                    <option value="{{$record->gameid}}"
                                            @if ($record->gameid == $gameType) selected @endif>{{$record->game_name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="gameItem">종류</label>
                        <select id="gameItem" name="gameItem" data-choices>
                            <option value="">전체</option>
                            @if ($gameType != '')
                                @php
                                    $index = array_search($gameType, array_column($gameRecord->toArray(), 'gameid'));
                                    $gameTypeCode = $gameRecord[$index]->game_type;
                                @endphp
                                @foreach ($gameRecord as $record)
                                    @if ($gameTypeCode == $record->game_type)
                                        <option value="{{$record->gameid}}"
                                                @if ($record->gameid == $gameItem) selected @endif>{{$record->game_name}}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="start-dat">날짜지정</label>
                        <input type="text" class="form-control" id="start-dat"
                               data-provider="flatpickr"
                               data-date-format="Y-m-d" placeholder="Select date" name="start_date"
                               value="{{$start_date}}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-secondary"><i
                                    class="bi bi-funnel align-baseline me-1"></i> @lang('translation.filter')
                            </button>
                            <a href="javascript:location.reload();" class="btn btn-outline-secondary"> 새로고침(자동(5분)) </a>
                            <a href="{{route('ranking_all')}}" class="btn btn-outline-secondary JQ_POPUP">손익랭킹</a>
                            @if ($notCount > 0)
                                <a href="{{route('find_not_allot')}}" type="button"
                                   class="btn btn-danger btn-load JQ_POPUP">
                                    <span class="d-flex align-items-center">
                                        <span class="spinner-grow flex-shrink-0" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </span>
                                        <span class="flex-grow-1 ms-2">
                                            미배당찾기 {{$notCount}}
                                        </span>
                                    </span>
                                </a>
                            @else
                                <a href="{{route('find_not_allot')}}" class="btn btn-outline-secondary JQ_POPUP">
                                    미배당찾기
                                </a>
                            @endif
                            <a href="{{route('user_fight_comm_rates')}}"
                               class="btn btn-outline-secondary JQ_POPUP">승부율</a>
                            <a href="{{route('stat_calendars')}}" class="btn btn-outline-secondary JQ_POPUP">월집계</a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    @foreach ($gameGroup as $group)
                        <div class="col-md-2 mb-1 col-sm-6 col-xs-12">
                            <select data-choices onchange="changedGameGroup(this)">
                                @foreach ($gameRecord as $record)
                                    @if ($record->game_type == $group)
                                        <option value="{{$record->gameid}}">{{$record->game_name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>
            </form>
            <div class="row mb-3">
                <div class="col-md-3">
                    <blockquote class="blockquote custom-blockquote blockquote-info rounded mb-0 d-flex">
                        <p class="mb-0 flex-grow-1">배팅</p>
                        <p class="mb-0 flex-shrink-0">{{number_format($nSumBet)}}</p>
                    </blockquote>
                </div>
                <div class="col-md-3">
                    <blockquote class="blockquote custom-blockquote blockquote-success rounded mb-0 d-flex">
                        <p class="mb-0 flex-grow-1">당첨지출</p>
                        <p class="mb-0 flex-shrink-0">{{number_format($nSumBetWin)}}</p>
                    </blockquote>
                </div>
                <div class="col-md-2">
                    <blockquote class="blockquote custom-blockquote blockquote-warning rounded mb-0 d-flex">
                        <p class="mb-0 flex-grow-1">기본손익</p>
                        <p class="mb-0 flex-shrink-0">{{number_format($nSumLoss)}}</p>
                    </blockquote>
                </div>
                <div class="col-md-2">
                    <blockquote class="blockquote custom-blockquote blockquote-secondary rounded mb-0 d-flex">
                        <p class="mb-0 flex-grow-1">배수지출</p>
                        <p class="mb-0 flex-shrink-0">{{number_format($nSumBetFee)}}</p>
                    </blockquote>
                </div>
                <div class="col-md-2">
                    <blockquote class="blockquote custom-blockquote blockquote-danger rounded mb-0 d-flex">
                        <p class="mb-0 flex-grow-1">최종손익</p>
                        <p class="mb-0 flex-shrink-0">{{number_format($nSumMainLoss)}}</p>
                    </blockquote>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="display table table-bordered table-nowrap" style="width:100%">
                            <thead>
                            <tr>
                                <th>게임</th>
                                <th>회차</th>
                                <th>결과</th>
                                <th>배팅/횟수</th>
                                <th class="th-showdown">회원승부</th>
                                <th>회원당첨</th>
                                <th><span class="info">손익⑥</span></th>
                                @if ($game_id != 0)
                                    <th class="th-showdown"><span title="회원 승부 손익 = ⑥ - 승부손익" class="info">회승손</span>
                                    </th>
                                @endif
                                <th><span title="배팅 수수료 지출 금액">배수</span></th>
                                <th><span class="info">최종손익</span></th>
                                <th>상태</th>
                                <th class="actions">기능</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $arrSumInfo = array_fill(0, 12, 0);
                                $nUserBetLoss = $nUserBetFee = $nUserBetMainLoss = 0;
                                $nUserBetLossSum = $nUserBetFeeSum = $nUserBetMainLossSum = $nUserMacthLoss = 0;
                            @endphp

                            @foreach ($betRecord as $record)
                                @if ($record->bet_count > 0 || $record->step_reset == 2)
                                    @php
                                        if ($record->step_reset == 0 && $record->game_win_pos != '') {
                                            $nLoss = $record->bet_user  - $record->bet_win;
                                            $nMainLoss = $nLoss - $record->bet_fee;
                                        }
                                        else {
                                            $nLoss = 0;
                                            $nMainLoss = 0;
                                        }
                                        $arrSumInfo[0] += $record->bet_user;
                                        $arrSumInfo[1] += $record->bet_count;
                                        if ($record->push_send == 0 && $record->bet_site == 0)
                                            $record->bet_site = $record->bet_user;
                                        $arrSumInfo[2] += $record->bet_site;
                                        $arrSumInfo[3] += $record->push_user;
                                        $arrSumInfo[4] += $record->push_send;
                                        $arrSumInfo[5] += $record->push_diff;
                                        $arrSumInfo[6] += $record->push_win;
                                        $arrSumInfo[7] += $record->bet_win;
                                        $arrSumInfo[8] += $nLoss;
                                        $arrSumInfo[9] += $record->bet_fee;
                                        $arrSumInfo[10] += $record->push_fee;
                                        $arrSumInfo[11] += $nMainLoss;
                                    @endphp
                                    <tr>
                                        <td>{{$record->game_ment}}</td>
                                        <td class="round-no"><span title="">{{$record->game_round}}({{$record->game_day_round ?? '0'}})</span>
                                        </td>
                                        <td class="round-result">
                                            @if ($record->step_reset == 2)
                                                <b class="text-danger">적특</b>
                                            @else
                                                {{$record->game_result}}
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <span class="money">{{number_format($record->bet_user)}}</span>/{{$record->bet_count}}
                                        </td>
                                        <td class="text-end">
                                            <span class="money">
                                                @if (is_null($record->bet_site2))
                                                    {{number_format($record->bet_site)}}
                                                @else
                                                    {{number_format($record->bet_site2)}}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="text-end"><span
                                                class="money">{{number_format($record->bet_win)}}</span></td>
                                        <td class="text-end fw-bold"><span
                                                class="money @if ($nLoss > 0) money-plus @else money-minus @endif">{{number_format($nLoss)}}</span>
                                        </td>
                                        @if ($game_id != 0)
                                            @if ($record->step_reset != 0)
                                                <td class="text-end"><span class="money">0</span></td>
                                            @else
                                                <td class="text-end">{!! printColorMoney($record->match_loss) !!}</td>
                                            @endif
                                        @endif
                                        <td class="text-end">
                                            @if ($record->game_result != "")
                                                <span class="money">{{number_format(floor($record->bet_fee))}}</span>
                                            @else
                                                0
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold">
                                            @if ($record->game_result != '')
                                                {!! printColorMoney($nMainLoss) !!}
                                            @else
                                                0
                                            @endif
                                        </td>
                                        <td>
                                            @if ($record->step_reset == "2")
                                                <span class="state-bet text-danger">적특</span>
                                            @elseif ($record->game_result != "")
                                                <span class="state-bet green-color">배팅(1)</span>
                                            @else
                                                <span class="state-bet ">배팅(0)</span>
                                            @endif
                                        </td>
                                        <td class="actions">
                                            <a href="javascript:void(0)" data-id="{{$record->id}}" class="btn-betinfo btn-outline-secondary btn-sm">상세</a>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            @if (count($betRecord) == 0)
                                <tr>
                                    <td colspan='99'>내역이 없습니다.</td>
                                </tr>
                            @endif
                            </tbody>
                            <tfoot>
                                <tr class="bg-warning">
                                    <td colspan="3">집계</td>
                                    <td class="text-end">{{number_format($arrSumInfo[0])}}
                                        /{{number_format($arrSumInfo[1])}}</td>
                                    @for ($index = 2; $index < 12; $index++)
                                        @if(($index >= 3 && $index<=6) || $index == 10)
                                            @php continue; @endphp
                                        @endif
                                        @if ($index == 8 || $index == 11)
                                            <td class="text-end fw-bold">{!! printColorMoney($arrSumInfo[$index]) !!}</td>
                                        @else
                                            <td class="text-end">{{number_format($arrSumInfo[$index])}}</td>
                                        @endif
                                    @endfor
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('custom-script')
    <script>
        var myVar = setInterval(myTimer, 1000);
        var reload = true;

        function changedGameGroup(obj) {
            popupCenter("{{route('betting_live_view')}}?game_id=" + $(obj).find('option:selected').val(), "중계화면", 1200, 700);
        }

        function myTimer() {
            var dt = new Date();
            // var nHour = dt.getHours();
            var nMin = dt.getMinutes();
            var nSec = dt.getSeconds();

            // var nDayRound = Math.floor((30 + nHour * 60 * 60 + nMin * 60 + nSec) / 300) + 1;
            var leftTime = 300 - nMin % 5 * 60 - nSec;
            if (reload && leftTime > 15 && leftTime < 10) {
                reload = false;
                setTimeout(function () {
                    window.location.reload(true);
                }, 5000);
            }
        }

        $(function () {
            var gameType = document.getElementById("gameType");
            var gameItem = document.getElementById("gameItem");
            gameType.addEventListener(
                'change',
                function (event) {
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
        })
    </script>
@endsection
