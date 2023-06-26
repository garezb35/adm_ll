@extends('layouts.master-without-nav')
@section('content')
    <div class="row">
        <div class="col-xxl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0"> @if ($userInfo != null)
                            {{$userInfo->userid}}({{$userInfo->nickname}})
                        @endif
                        회원손익랭킹</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <form method="get" action="" id="form-search">
                        <input name="sort" id="sort-arrow" type="hidden" value="{{$sort ?? 'desc'}}">
                        <input name="type" id="sort-type" type="hidden" value="{{$type ?? 'loss_sum2'}}">
                        <input name="userid" type="hidden" value="{{$userid}}">
                        <div class="row mb-3">
                            @if (config('app.site_type') != 'PWB')
                                <div class="col-sm-2 col-xxl-6">
                                    <label for="gameType">@lang('translation.game-type')</label>
                                    <select class="form-control" id="gameType" name="gameType" data-choices
                                            data-choices-search-false data-choices-removeItem>
                                        <option value="all" @if ($gameType == 'all') selected @endif>전체</option>
                                        @if (config('app.site_type') != 'BS')
                                            <option value="pwb" @if ($gameType == 'pwb') selected @endif>파워볼</option>
                                        @endif
                                        <option value="live" @if ($gameType == 'live') selected @endif>카지노</option>
                                        <option value="slot" @if ($gameType == 'slot') selected @endif>슬롯</option>
                                    </select>
                                </div>
                            @else
                                <input type="hidden" name="gameType" value="pwb"/>
                            @endif
                            <div class="col-sm-3 col-xxl-6">
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
                            <div class="col-sm-2 col-xxl-6">
                                <label for="seatch-txt">@lang('translation.userid')</label>
                                <div class="search-box">
                                    <input type="text" class="form-control search" name="search_text" id="seatch-txt"
                                           value="{{$search_text}}">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            <div class="col-sm-3 col-xxl-6">
                                <label for="startDate">날짜검색</label>
                                <div>
                                    <input type="text" class="form-control" id="startDate"
                                           data-provider="flatpickr"
                                           data-date-format="Y-m-d" placeholder="Select date" name="start_date"
                                           data-range-date="true"
                                           value="{{$date_start}}">
                                </div>
                            </div>
                            <div class="col-sm-2 col-xxl-12">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit"
                                            class="btn btn-secondary w-100">@lang('translation.filter')</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="row mb-3">
                        <div class="col-sm-2 col-xxl-12">
                            <blockquote class="blockquote custom-blockquote blockquote-danger rounded mb-0">
                                <p class="mb-2">총배팅: {!! printColorMoney($sumInfo['sumBet']) !!}</p>
                            </blockquote>
                        </div>
                        <div class="col-sm-3 col-xxl-12">
                            <blockquote class="blockquote custom-blockquote blockquote-danger rounded mb-0">
                                <p class="mb-2">총당첨금: {!! printColorMoney($sumInfo['sumWin']) !!}</p>
                            </blockquote>
                        </div>
                        <div class="col-sm-2 col-xxl-12">
                            <blockquote class="blockquote custom-blockquote blockquote-danger rounded mb-0">
                                <p class="mb-2">손익: {!! printColorMoney($sumInfo['sumLoss']) !!}</p>
                            </blockquote>
                        </div>
                        <div class="col-sm-2 col-xxl-12">
                            <blockquote class="blockquote custom-blockquote blockquote-danger rounded mb-0">
                                <p class="mb-2">총배수: {{number_format($sumInfo['sumFee'])}}</p>
                            </blockquote>
                        </div>
                        <div class="col-sm-3 col-xxl-12">
                            <blockquote class="blockquote custom-blockquote blockquote-danger rounded mb-0">
                                <p class="mb-2">최종손익: {!! printColorMoney($sumInfo['sumMain']) !!}</p>
                            </blockquote>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="display table table-bordered table-nowrap">
                                    <thead>
                                    <tr>
                                        <th>상위</th>
                                        <th>회원</th>
                                        <th><a href="javascript:void(0)" data-type="bet_sum"
                                               class="@if ($type == 'bet_sum') {{$sort == 'desc' ? 'asc' : 'desc'}} @endif sort-link">배팅</a>
                                        </th>
                                        <th><a href="javascript:void(0)" data-type="win_sum"
                                               class="@if ($type == 'win_sum') {{$sort == 'desc' ? 'asc' : 'desc'}} @endif sort-link">상금</a>
                                        </th>
                                        <th><a href="javascript:void(0)" data-type="loss_sum"
                                               class="@if ($type == 'loss_sum') {{$sort == 'desc' ? 'asc' : 'desc'}} @endif sort-link">손익</a>
                                        </th>
                                        <th><a href="javascript:void(0)" data-type="fee_sum"
                                               class="@if ($type == 'fee_sum') {{$sort == 'desc' ? 'asc' : 'desc'}} @endif sort-link">총배수</a>
                                        </th>
                                        <th><a href="javascript:void(0)" data-type="main_sum"
                                               class="@if ($type == 'main_sum') {{$sort == 'desc' ? 'asc' : 'desc'}} @endif sort-link">최종손익</a>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($betRecord as $record)
                                        <tr>
                                            <td style="color: {{'#' . ($record->p_user_color ?? $record->user_color)}}">
                                                <b>{{$record->p_userid ?? $record->userid}}
                                                    ({{$record->p_nickname ?? $record->nickname}})</b></td>
                                            <td style="color: {{'#' . $record->user_color}}"><b>{{$record->userid}}
                                                    ({{$record->nickname}})</b></td>
                                            <td>{!! printColorMoney($record->bet_sum) !!}</td>
                                            <td>{!! printColorMoney($record->win_sum) !!}</td>
                                            <td>{!! printColorMoney($record->loss_sum) !!}</td>
                                            <td>{{number_format($record->fee_sum)}}</td>
                                            <td>{!! printColorMoney($record->main_sum) !!}</td>
                                        </tr>
                                    @endforeach
                                    @if (count($betRecord) == 0)
                                        <tr>
                                            <td colspan="99">내역이 없습니다.</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                                @if (count($betRecord) > 0)
                                    <div>
                                        {!! $betRecord->withQueryString()->links() !!}
                                    </div>
                                @endif
                            </div>
                        </div>
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
        $('.sort-link').click(function () {
            var elem = $(this);
            if (elem.hasClass('asc')) {
                $('#sort-arrow').val('asc');
                $('#sort-type').val(elem.data('type'));
                $("#form-search").submit();
            } else {
                $('#sort-arrow').val('desc');
                $('#sort-type').val(elem.data('type'));
                $("#form-search").submit();
            }
        });
    </script>
@endsection
