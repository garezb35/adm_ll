@extends('layouts.master')
@section('title')
    @lang('translation.live-betting')
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('translation.user-management')</h5>
                </div>
                <div class="card-body">
                    <form method="get" action="{{route('partner_tree')}}" id="form-search">
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <select id="searchOpt"  name="search_opt"  data-choices>
                                    <option value="0" @if ($search_opt == "userid") selected @endif>아이디</option>
                                    <option value="1" @if ($search_opt == "nickname") selected @endif>닉네임</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input id="seatch-txt" name="search_txt" class="form-control" value="{{$search_txt}}" placeholder="검색">
                            </div>
                            <div class="col-md-1">
                                <button class="btn btn-secondary w-100" id="search-btn">
                                    <i class="bi bi-funnel align-baseline me-1"></i> @lang('translation.filter')
                                </button>
                            </div>
                            <div class="col-md-3">
                                <a href="{{route('partner_tree')}}" class="btn btn-subtle-secondary">초기화</a>
                                <a href="javascript:location.reload();" type="button" class="btn btn-secondary">
                                    <span class="icon-on"><i class="mdi  mdi-web-refresh align-bottom me-1"></i> 새로 고침</span>
                                </a>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{route('partner_add')}}" class="JQ_POPUP btn btn-outline-secondary">총판추가</a>
                                <a href="{{route('partner_depth_list')}}" class="JQ_POPUP btn btn-outline-secondary">단계관리</a>
                                <a href="{{route('tree_view_opt')}}" class="JQ_POPUP btn btn-outline-secondary">표시관리</a>
                            </div>
                        </div>
                    </form>
                    <div class="row mb-3">
                        @php
                            $col = config('app.no_mini') == 0 ? 3: 4;
                        @endphp
                        @if (config('app.no_mini') == 0)
                            <div class="col-md-{{$col}}">
                                <blockquote class="blockquote custom-blockquote blockquote-info rounded mb-0 d-flex">
                                    <p class="mb-0 flex-grow-1">보유금총액</p>
                                    <p class="mb-0 flex-shrink-0">{!! printColorMoney($keep_money) !!}</p>
                                </blockquote>
                            </div>
                            <div class="col-md-{{$col}}">
                                <blockquote class="blockquote custom-blockquote blockquote-primary rounded mb-0 d-flex">
                                    <p class="mb-0 flex-grow-1">보유알총액</p>
                                    <p class="mb-0 flex-shrink-0">{!! printColorMoney($sum_bpoint) !!}</p>
                                </blockquote>
                            </div>
                        @else
                            <div class="col-md-{{$col}}">
                                <blockquote class="blockquote custom-blockquote blockquote-primary rounded mb-0 d-flex">
                                    <p class="mb-0 flex-grow-1">보유금총액</p>
                                    <p class="mb-0 flex-shrink-0">{!! printColorMoney($keep_money + $sum_bpoint) !!}</p>
                                </blockquote>
                            </div>
                        @endif
                        <div class="col-md-{{$col}}">
                            <blockquote class="blockquote custom-blockquote blockquote-warning rounded mb-0 d-flex">
                                <p class="mb-0 flex-grow-1">수수료총액</p>
                                <p class="mb-0 flex-shrink-0">{!! printColorMoney($keep_point) !!}</p>
                            </blockquote>
                        </div>
                        <div class="col-md-{{$col}}">
                            <blockquote class="blockquote custom-blockquote blockquote-success rounded mb-0 d-flex">
                                <p class="mb-0 flex-grow-1">루징총액</p>
                                <p class="mb-0 flex-shrink-0">{!! printColorMoney($sumPLosing) !!}|{!! printColorMoney($sumMLosing) !!}</p>
                            </blockquote>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="display table table-bordered table-nowrap" style="width:100%">
                            @if ($siteInfo->view_comm_rate == 1 || $siteInfo->view_allot_rate == 1)
                                <thead>
                                <tr>
                                    <th rowspan="2">NO</th>
                                    @foreach ($stepRecord as $record)
                                        <th rowspan="2">{{$record['step_name']}}</th>
                                    @endforeach
                                    <th rowspan="2">하부</th>
                                    <th rowspan="2" class="actions">기능</th>
                                    @if ($siteInfo->view_comm_rate == 1)
                                        <th colspan="{{count($gameRecord) + 4}}">배수/당수(%)</th>
                                    @endif
                                    @if ($siteInfo->view_allot_rate == 1)
                                        <th colspan="{{count($gameRecord)}}">전용배당율(배)</th>
                                    @endif
                                    <th rowspan="2">전화번호</th>
                                    <th rowspan="2">상태</th>
                                    <th rowspan="2">보유머니</th>
                                    <th rowspan="2">수수료</th>
                                    @if (config('app.game_type') == 0)
                                        <th rowspan="2">카지노알</th>
                                        <th rowspan="2">슬롯알</th>
                                    @else
                                        <th rowspan="2">보유알</th>
                                    @endif
                                    <th rowspan="2">루징</th>
                                    <th rowspan="2">소속</th>
                                </tr>
                                <tr>
                                    @if ($siteInfo->view_comm_rate == 1)
                                        @foreach ($gameRecord as $game)
                                            <th>{{$game->game_name}}</th>
                                        @endforeach
                                        <th>카지노:롤링</th>
                                        <th>카지노:루징</th>
                                        <th>슬롯:롤링</th>
                                        <th>슬롯:루징</th>
                                    @endif
                                    @if ($siteInfo->view_allot_rate == 1)
                                        @foreach ($gameRecord as $game)
                                            <th>{{$game->game_name}}</th>
                                        @endforeach
                                    @endif
                                </tr>
                                </thead>
                            @else
                                <thead>
                                <tr>
                                    <th>NO</th>
                                    @foreach ($stepRecord as $record)
                                        <th>{{$record['step_name']}}</th>
                                    @endforeach
                                    <th>하부</th>
                                    <th class="actions">기능</th>
                                    <th>전화번호</th>
                                    <th>상태</th>
                                    <th>보유머니</th>
                                    <th>수수료</th>
                                    @if (config('app.no_mini') == 0)
                                        @if (config('app.game_type') == 0)
                                            <th>카지노알</th>
                                            <th>슬롯알</th>
                                        @else
                                            <th>보유알</th>
                                        @endif
                                    @endif

                                    <th>루징</th>
                                    <th>소속</th>
                                </tr>
                                </thead>
                            @endif
                            @php $count = 1; @endphp
                            @foreach ($userRecord as $record)
                                @php
                                    if ($search_txt != '') $user = $record['user'];
                                    else $user = $record;
                                @endphp
                                <tr>
                                    <td>{{$count++}}</td>
                                    @for ($index = 1; $index <= $record['deep']; $index++)
                                        <td></td>
                                    @endfor
                                    <td class="member-info">
                                        @if ($user->joinid == 0 && $search_txt == '')
                                            <a href="{{route('partner_tree')}}?search_opt=0&search_txt={{$user->userid}}"
                                               class="btn btn-secondary btn-sm">전체</a>&nbsp;
                                        @endif
                                        {{--                                        <span class="user-context-menu" data-user-id="{{$user->id}}"--}}
                                        {{--                                              data-username="{{$user->userid}}">--}}
                                        {{--                                    <a style="font-weight: bold;color: {{'#' . $user->user_color}}">{{$user->userid}}({{$user->nickname}})</a>--}}
                                        {{--                                </span>--}}
                                        <a class="btn btn-sm btn-link dropdown-toggle" data-bs-toggle="dropdown"
                                           style="font-weight: bold;color: {{'#' . $user->user_color}}">{{$user->userid}}
                                            ({{$user->nickname}})</a>
                                        @include('layouts.dropdown.users', $user)
                                    </td>
                                    @for ($k = 1; $k < $step - $record['deep']; $k++)
                                        <td></td>
                                    @endfor
                                    <td class="actions">
                                        <a href="{{route('partner_add')}}?userid={{$user->id}}"
                                           class="JQ_POPUP btn-tree-add btn btn-sm btn-secondary">추가</a>
                                        @if (config('app.evo_type') == 'boss')
                                            <a href="{{route('partner_evo2')}}?evo2=1&userid={{$user->id}}"
                                               class="">에2적</a>
                                            <a href="{{route('partner_evo2')}}?evo2=0&userid={{$user->id}}"
                                               class="">에2해</a>
                                        @endif
                                        @if (config('app.pra_type') == 'boss')
                                            <a href="{{route('partner_evo2')}}?pra2=1&userid={{$user->id}}"
                                               class="">슬2적</a>
                                            <a href="{{route('partner_evo2')}}?pra2=0&userid={{$user->id}}"
                                               class="">슬2해</a>
                                        @endif
                                    </td>
                                    <td class="actions">
                                        <label for="rateOpt"></label>
                                        <select id="rateOpt" onchange="goSelectURL(this);">
                                            <option value="">== 기능 ==</option>
                                            <option value="{{route('edit_all_allot_rate')}}?userid={{$user->id}}">배당율
                                            </option>
                                            <option value="{{route('edit_all_comm_rates')}}?userid={{$user->id}}">수수료율
                                            </option>
{{--                                            <option value="{{route('edit_bet_push')}}?userid={{$user->id}}">[파]누르기율--}}
{{--                                            </option>--}}
{{--                                            <option value="{{route('editPushCasino')}}?userid={{$user->id}}">[카]누르기율--}}
{{--                                            </option>--}}
                                            <option value="{{route('user_edit_path')}}?userid={{$user->id}}">소속이동
                                            </option>
                                            <option value="{{route('user_bet_limits')}}?userid={{$user->id}}">배팅제한
                                            </option>
{{--                                            <option value="{{route('set_only_domain')}}?userid={{$user->id}}">전용도메인--}}
{{--                                            </option>--}}
                                        </select> | <a href="javascript:delUser({{$user->id}});"
                                                       class="btn btn-sm btn-danger">삭제</a>
                                    </td>
                                    @if ($siteInfo->view_comm_rate == 1)
                                        @php
                                            $rolling = $user->rolling;
                                            $rollingBS = $user->rollingBS;
                                        @endphp
                                        @foreach ($gameRecord as $game)
                                            <td>
                                                @if ($rolling[$game->game_code] > 0)
                                                    {{$rolling[$game->game_code]}}
                                                @endif
                                            </td>
                                        @endforeach
                                        <td>
                                            @if (floatval($rollingBS->live) != 0)
                                                {{floatval($rollingBS->live)}}
                                            @endif
                                        </td>
                                        <td>
                                            @if (floatval($rollingBS->live_lose) != 0)
                                                {{floatval($rollingBS->live_lose) ?? ''}}
                                            @endif
                                        </td>
                                        <td>
                                            @if (floatval($rollingBS->slot) != 0)
                                                {{floatval($rollingBS->slot) ?? ''}}
                                            @endif
                                        </td>
                                        <td>
                                            @if (floatval($rollingBS->slot_lose) != 0)
                                                {{floatval($rollingBS->slot_lose) ?? ''}}
                                            @endif
                                        </td>
                                    @endif
                                    @if ($siteInfo->view_allot_rate == 1)
                                        @php
                                            $rateAll = \App\Models\User::rateAll($user->id);
                                            $rateAll = json_decode(json_encode($rateAll[0]), true);
                                        @endphp
                                        @foreach ($gameRecord as $game)
                                            <td>
                                                <div style="display: flex;">
                                                    @php
                                                        $pickRecord = $game->pickinfo;
                                                    @endphp
                                                    @foreach ($pickRecord as $pick)
                                                        @if ($rateAll != null && $rateAll[$pick->pick_code] > 0)
                                                            <div>{{$rateAll[$pick->pick_code]}}/</div>
                                                        @else
                                                            <div>X/</div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </td>
                                        @endforeach
                                    @endif
                                    <td>{{$user->phone}}</td>
                                    <td class="actions" style="min-width: 80px;">
                                        @if ($user->verified == 1)
                                            <span class="text-success">정상</span>
                                            <a href="javascript:setStop({{$user->id}});" class="btn btn-sm btn-danger">중지</a>
                                            <a href="javascript:setNormalAll({{$user->id}});"
                                               class="btn btn-sm btn-success">전체정상</a>
                                            <a href="javascript:setStopAll({{$user->id}});"
                                               class="btn btn-sm btn-danger">전체중지</a>
                                        @elseif ($user->verified == 3)
                                            <span class="text-danger">중지</span>
                                            <a href="javascript:setNormal({{$user->id}});"
                                               class="btn btn-sm btn-success">정상</a>
                                            <a href="javascript:setNormalAll({{$user->id}});"
                                               class="btn btn-sm btn-success">전체정상</a>
                                            <a href="javascript:setStopAll({{$user->id}});"
                                               class="btn btn-sm btn-danger">전체중지</a>
                                        @elseif ($user->verified == 0)
                                            <span class="text-danger">요청중</span>
                                        @elseif ($user->verified == 2)
                                            <span class="text-danger">보류중</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right;" class="actions">
                                        @if (config('app.no_mini') == 0)
                                            {!! printColorMoney($user->procinfo->money) !!}
                                        @else
                                            {!! printColorMoney($user->procinfo->money + $user->procinfo->bpoint) !!}
                                        @endif
                                        <a href="{{route('user_charge')}}?id={{$user->id}}&live=1"
                                           class="JQ_POPUP btn btn-sm btn-light">충</a>
                                        <a href="{{route('user_excharge')}}?id={{$user->id}}"
                                           class="JQ_POPUP  btn btn-sm btn-light">환</a>
                                        <a href="{{route('user_info')}}?id={{$user->id}}"
                                           class="JQ_POPUP btn btn-sm btn-light"><i class="ri-refresh-line"></i> </a>
                                    </td>
                                    <td class="actions" style="text-align: right;">
                                        {!! printColorMoney(floor($user->procinfo->point)) !!} |
                                        <label for="pointOpt"></label>
                                        <select id="pointOpt" onchange="goSelectURL(this);">
                                            <option value="">== 기능 ==</option>
                                            <option value="{{route('partner_fee_index')}}?userid={{$user->id}}">머니전환
                                            </option>
                                            <option value="{{route('partner_fee_list')}}?userid={{$user->id}}">적립내역
                                            </option>
                                        </select>
                                    </td>
                                    @if (config('app.no_mini') == 0)
                                        @if (config('app.game_type') == 0)
                                            <td>{!! printColorMoney($user->procinfo->bpoint) !!}</td>
                                            <td>{!! printColorMoney($user->procinfo->spoint) !!}</td>
                                        @else
                                            <td>{!! printColorMoney($user->procinfo->bpoint) !!}</td>
                                        @endif
                                    @endif
                                    <td class="actions" style="text-align: right;">
                                        {!! printColorMoney(floor($user->procinfo->losing)) !!} |
                                        <label for="losingOpt"></label>
                                        <select id="losingOpt" onchange="goSelectURL(this);">
                                            <option value="">== 기능 ==</option>
                                            <option data-type="init" value="{{$user->id}}">정산</option>
                                            <option data-type=""
                                                    value="{{route('partner_lose_history2')}}?userid={{$user->id}}">정산내역
                                            </option>
                                            <option data-type="reset" value="{{$user->id}}">초기화</option>
                                            <option data-type=""
                                                    value="{{route('partner_lose_history')}}?userid={{$user->id}}">초기화내역
                                            </option>
                                            <option data-type=""
                                                    value="{{route('partner_lose_list')}}?userid={{$user->id}}">적립내역
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <label for="rankingOpt"></label>
                                        <select id="rankingOpt" onchange="goSelectURL(this);">
                                            <option value="">== 기능 ==</option>
                                            <option value="{{route('partner_search')}}?userid={{$user->id}}">파트너
                                            </option>
                                            <option value="{{route('partner_rank_bettings')}}?userid={{$user->id}}">
                                                배팅손익랭킹
                                            </option>
                                            <option value="{{route('partner_rank_rolling')}}?userid={{$user->id}}">
                                                수수료랭킹
                                            </option>
                                            <option value="{{route('partner_rank_charges')}}?userid={{$user->id}}">
                                                충전랭킹
                                            </option>
                                            <option value="{{route('partner_rank_excharges')}}?userid={{$user->id}}">
                                                환전랭킹
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                            @if (empty($userRecord))
                                <tr>
                                    <td colspan="100">자료가 없습니다.</td>
                                </tr>
                                @endif
                                </tbody>
                        </table>
                        @if ($search_txt != '' && !empty($userRecord))
                            <div class="pagination-part" style="text-align: center;">
                                {!! $userRecord->withQueryString()->links() !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('custom-script')
<script src="/build/js/partner/tree.js"></script>
@endsection
