@extends('layouts.master')
@section('title')
    @lang('translation.live-betting')
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <form id="form-search" action="" method="get">
                <input type="hidden" name="sort" id="sort-arrow" value="{{$sort}}" />
                <input type="hidden" name="type" id="sort-type" value="{{$type}}" />
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-xxl-3 col-sm-6">
                                <div>
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
                            </div>

                            <!--end col-->
                            <div class="col-xxl-3">
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
                            <!--end col-->
                            <div class="col-xxl-3 col-sm-6">
                                <label for="seatch-txt">@lang('translation.userid')</label>
                                <div class="search-box">
                                    <input type="text" class="form-control search" name="searchUser" id="seatch-txt"
                                           value="{{$searchUser}}">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-xxl-2 col-sm-6">
                                <label for="startDate">@lang('translation.bettingtime')</label>
                                <div>
                                    <input type="text" class="form-control" id="startDate"
                                           data-provider="flatpickr"
                                           data-date-format="Y-m-d" placeholder="Select date" name="startDate"
                                           value="{{$startDate}}">
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-xxl-1 col-sm-6">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-secondary w-100"><i
                                            class="bi bi-funnel align-baseline me-1"></i> @lang('translation.filter')
                                    </button>
                                </div>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </div>
                </div>
            </form>
        </div>
        <!--end col-->
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('translation.live-betting')</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display table table-bordered table-nowrap" style="width:100%">
                            <thead>
                            <tr>
                                <th>@lang('translation.no')</th>
                                <th>@lang('translation.user')</th>
                                <th><a href="javascript:void(0)" data-type="bet_num"
                                       class="@if ($type == 'bet_num') {{$sort}} @endif sort-link">@lang('translation.number-bets')</a>
                                </th>
                                <th><a href="javascript:void(0)" data-type="bet_sum"
                                       class="@if ($type == 'bet_sum') {{$sort}} @endif sort-link">@lang('translation.betting-amount')</a>
                                </th>
                                <th><a href="javascript:void(0)" data-type="win_num"
                                       class="@if ($type == 'win_num') {{$sort}} @endif sort-link">@lang('translation.number-wins')</a>
                                </th>
                                <th><a href="javascript:void(0)" data-type="win_sum"
                                       class="@if ($type == 'win_sum') {{$sort}} @endif sort-link">@lang('translation.winner-amount')</a>
                                </th>
                                <th><a href="javascript:void(0)" data-type="loss_sum2"
                                       class="@if ($type == 'loss_sum2') {{$sort}} @endif sort-link">@lang('translation.bet-profit-loss')</a>
                                </th>
                                <th><a href="javascript:void(0)" data-type="fee_sum"
                                       class="@if ($type == 'fee_sum') {{$sort}} @endif sort-link">@lang('translation.rolling')</a>
                                </th>
                                <th><a href="javascript:void(0)" data-type="main_sum2"
                                       class="@if ($type == 'main_sum2') {{$sort}} @endif sort-link">@lang('translation.total-revenue')</a>
                                </th>
                                <th>@lang('translation.function')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if (count($userRecord) > 0)
                                <tr class="table-info">
                                    <td colspan="2"><span>@lang('translation.sum')</span></td>
                                    <td>{{number_format($sumInfo['sumBetCnt'])}}</td>
                                    <td>{!! printColorMoney($sumInfo['sumBet']) !!}</td>
                                    <td>{{number_format($sumInfo['sumWinCnt'])}}</td>
                                    <td>{!! printColorMoney($sumInfo['sumWin']) !!}</td>
                                    <td>{!! printColorMoney($sumInfo['sumLoss']) !!}</td>
                                    <td>{!! printColorMoney($sumInfo['sumFee']) !!}</td>
                                    <td>{!! printColorMoney($sumInfo['sumLoss2']) !!}</td>
                                    <td></td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="20">@lang('translation.nohistory')</td>
                                </tr>
                            @endif
                            @php
                                $index = 1;
                            @endphp
                            @foreach ($userRecord as $record)
                                <tr>
                                    <td>{{$index++}}</td>
                                    <td>
                                        <a class="user-context-menu fw-medium" data-user-id="{{$record->id}}"
                                           data-username="{{$record->userid}}" href="#">
                                            {{$record->userid}}({{$record->nickname}})
                                        </a>
                                    </td>
                                    <td>{{number_format($record->bet_num)}}</td>
                                    <td>{{number_format($record->bet_sum)}}</td>
                                    <td>{{number_format($record->win_num)}}</td>
                                    <td>{{number_format($record->win_sum)}}</td>
                                    <td>{!! printColorMoney($record->loss_sum2) !!}</td>
                                    <td>{{number_format($record->fee_sum)}}</td>
                                    <td>{!! printColorMoney($record->main_sum2) !!}</td>
                                    <td class="actions">
                                        <a class="bet-detail btn btn-sm btn-light" data-id="{{$record->id}}"
                                           href="javascript:void(0)">@lang('translation.details')</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @if (count($userRecord) > 0 && $searchUser == '')
                            <div class="pagination-part">
                                {!! $userRecord->withQueryString()->links() !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $('.bet-detail').click(function () {
                text = '';
                var startDate = $("input[name=startDate]").val();
                var url = "{{route('betInfo')}}?userid=" + $(this).data('id') + "&gameType=" + $("#gameType").combobox('getValue') + "&startDate=" + startDate + "&endDate=" + startDate;
                popupCenter(url, text, 1250, 800);

            });
            var gameType = document.getElementById("gameType");
            var gameItem = document.getElementById("gameItem");
            const gameItemInstance = new Choices(gameItem, {
                // options here
            });

            gameType.addEventListener(
                'change',
                function (event) {
                    gameItem.value = "";
                    $('#form-search').submit();
                },
                false,
            );
            $('.sort-link').bind('click', function() {
                var elem = $(this);
                if (elem.hasClass('asc')) {
                    $('#sort-arrow').val('desc');
                    $('#sort-type').val(elem.data('type'));
                }
                else {
                    $('#sort-arrow').val('asc');
                    $('#sort-type').val(elem.data('type'));
                }
                $("#form-search").submit();
            });
        });
    </script>
@endsection
