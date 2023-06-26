@extends(isset($header) && $header ? $header: 'layouts.master')

@section('title')
    @lang('translation.live-betting')
@endsection
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">@lang('translation.user-management')</h5>
        </div>
        <div class="card-body">
            <form method="get" action="" id="form-search">
                <input type="hidden" name="sort" id="sort-arrow" value="{{$sort}}"/>
                <input type="hidden" name="type" id="sort-type" value="{{$type}}"/>
                @if (isset($userid))
                    <input type="hidden" name="userid" value="{{$userid}}"/>
                @endif
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label class="form-label" for="state">상태</label>
                        <select name="state" id="state" data-choices
                                data-choices-search-false>
                            <option value="">전체</option>
                            <option value="0" @if ($state == "0") selected @endif>승인대기</option>
                            <option value="2" @if ($state == "2") selected @endif>보류</option>
                            <option value="1" @if ($state == "1") selected @endif>정상</option>
                            <option value="3" @if ($state == "3") selected @endif>사용중지</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="conn">접속</label>
                        <select name="conn" id="conn" data-choices
                                data-choices-search-false>
                            <option value="">전체</option>
                            <option value="1" @if ($conn == 1) selected @endif>접속중</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label" for="last_login">로그인없이</label>
                        <input value="{{$last_login}}" name="last_login" class="form-control" type="number" min="0"
                               max="100">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label" for="last_charged">충전없이</label>
                        <input value="{{$last_charged}}" name="last_charged" class="form-control" type="number" min="0"
                               max="100">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="search_opt">검색옵션</label>
                        <select name="search_opt" data-choices data-choices-search-false id="search_opt">
                            <option value="1" @if ($search_opt == "1") selected @endif>아이디</option>
                            <option value="4" @if ($search_opt == "4") selected @endif>닉네임</option>
                            <option value="2" @if ($search_opt == "2") selected @endif>이름</option>
                            <option value="3" @if ($search_opt == "3") selected @endif>계좌번호</option>
                            <option value="5" @if ($search_opt == "5") selected @endif>전화번호</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="search_text">검색</label>
                        <input id="seatch-txt" class="form-control" value="{{$search_text}}" name="search_text">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-secondary w-100"><i
                                class="bi bi-funnel align-baseline me-1"></i> @lang('translation.filter')
                        </button>
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
                            <p class="mb-0 flex-shrink-0">{!! printColorMoney($sumInfo['sum_money']) !!}</p>
                        </blockquote>
                    </div>
                    <div class="col-md-{{$col}}">
                        <blockquote class="blockquote custom-blockquote blockquote-primary rounded mb-0 d-flex">
                            <p class="mb-0 flex-grow-1">보유알총액</p>
                            <p class="mb-0 flex-shrink-0">{!! printColorMoney($sumInfo['sum_bpoint']) !!}</p>
                        </blockquote>
                    </div>
                @else
                    <div class="col-md-{{$col}}">
                        <blockquote class="blockquote custom-blockquote blockquote-primary rounded mb-0 d-flex">
                            <p class="mb-0 flex-grow-1">보유금총액</p>
                            <p class="mb-0 flex-shrink-0">{!! printColorMoney($sumInfo['sum_money'] + $sumInfo['sum_bpoint']) !!}</p>
                        </blockquote>
                    </div>
                @endif

                <div class="col-md-{{$col}}">
                    <blockquote class="blockquote custom-blockquote blockquote-warning rounded mb-0 d-flex">
                        <p class="mb-0 flex-grow-1">수수료총액</p>
                        <p class="mb-0 flex-shrink-0">{!! printColorMoney($sumInfo['sum_point'] ?? 0) !!}</p>
                    </blockquote>
                </div>
                <div class="col-md-{{$col}}">
                    <blockquote class="blockquote custom-blockquote blockquote-success rounded mb-0 d-flex">
                        <p class="mb-0 flex-grow-1">루징총액</p>
                        <p class="mb-0 flex-shrink-0">{!! printColorMoney($sumInfo['sumPLosing'] ?? 0) !!}
                            |{!! printColorMoney($sumInfo['sumMLosing'] ?? 0) !!}</p>
                    </blockquote>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table class="display table table-bordered table-nowrap" style="width:100%">
                            <thead>
                            <tr>
                                <th>NO</th>
                                <th>최상위</th>
                                <th>직상위</th>
                                <th>회원</th>
                                <th><a href="javascript:void(0);" id="sort-money" data-type="money"
                                       class="@if ($type == 'money') {{$sort}} @endif sort-link">보유금액</a></th>
                                <th><a href="javascript:void(0);" id="sort-loan" data-type="loan"
                                       class="@if ($type == 'loan') {{$sort}} @endif sort-link">여신</a></th>
                                @if (config('app.no_mini') == 0)
                                    @if (config('app.game_type') == 0)
                                        <th><a href="javascript:void(0);" data-type="casino"
                                               class="@if ($type == 'casino') {{$sort}} @endif sort-link">카지노알</a></th>
                                        <th><a href="javascript:void(0);" data-type="slot"
                                               class="@if ($type == 'slot') {{$sort}} @endif sort-link">슬롯알</a></th>
                                    @else
                                        <th><a href="javascript:void(0);" data-type="casino"
                                               class="@if ($type == 'casino') {{$sort}} @endif sort-link">보유알</a></th>
                                    @endif
                                @endif
                                <th><a href="javascript:void(0);" id="sort-point" data-type="point"
                                       class="@if ($type == 'point') {{$sort}} @endif sort-link">수수료</a></th>
                                <th><a href="javascript:void(0);" id="sort-losing" data-type="losing"
                                       class="@if ($type == 'losing') {{$sort}} @endif sort-link">루징</a></th>
                                <th>예금주</th>
                                <th>접속</th>
                                <th>접속실패</th>
                                @if ($state == 0 || $state == 2)
                                    <th>메모</th>
                                @endif
                                <th>상태</th>
                                <th><a href="javascript:void(0);" id="sort-created_at" data-type="create"
                                       class="@if ($type == 'create') {{$sort}} @endif sort-link">가입일</a></th>
                                <th><a href="javascript:void(0);" id="sort-charge_at" data-type="charge"
                                       class="@if ($type == 'charge') {{$sort}} @endif sort-link">최근충전</a></th>
                                <th><a href="javascript:void(0);" id="sort-login_at" data-type="login"
                                       class="@if ($type == 'login') {{$sort}} @endif sort-link">최근로그인</a></th>
                                <th class="actions">기능</th>
                            </tr>
                            </thead>
                            @if (count($userRecords) == 0)
                                <tr style='background:white !important;'>
                                    <td colspan='20'>@lang('translation.nohistory')</td>
                                </tr>
                            @endif
                            @php $index = 1; @endphp
                            @foreach ($userRecords as $record)
                                <tr class="@if (abs(strtotime(date('Y-m-d H:i:s')) - strtotime($record->procinfo->changed_at)) < (60 * 60 * 24 * 2)) bg-info @endif">
                                    <td>{{$index++}}</td>
                                    <td>
                                        @php
                                            $user = $record->headinfo;
                                            if (empty($user))
                                                $user = $record;
                                        @endphp
                                        <a class="btn btn-sm btn-link dropdown-toggle" data-bs-toggle="dropdown"
                                           style="font-weight: bold;color: {{'#' . $user->user_color}}">{{$user->userid}}
                                            ({{$user->nickname}})</a>
                                        @include('layouts.dropdown.users', $user)
                                    </td>
                                    <td>
                                        @php
                                            $user = $record->joininfo;
                                            if ($record->masterid == 0)
                                               $user = array();
                                            else if (!empty($headinfo) && $record->masterid == $record->joinid)
                                               $user = $record;
                                        @endphp
                                        @if (!empty($user))
                                            <a class="btn btn-sm btn-link dropdown-toggle" data-bs-toggle="dropdown"
                                               style="font-weight: bold;color: {{'#' . $user->user_color}}">{{$user->userid}}
                                                ({{$user->nickname}})</a>
                                            @include('layouts.dropdown.users', $user)
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $user = $record;
                                        @endphp
                                        <a class="btn btn-sm btn-link dropdown-toggle" data-bs-toggle="dropdown"
                                           style="font-weight: bold;color: {{'#' . $user->user_color}}">{{$user->userid}}
                                            ({{$user->nickname}})</a>
                                        @include('layouts.dropdown.users', $user)
                                    </td>
                                    <td class="text-end">
                                        @if (config('app.no_mini') == 0)
                                            {!! printColorMoney($record->procinfo->money) !!}
                                        @else
                                            {!! printColorMoney($record->procinfo->money + $record->procinfo->bpoint) !!}
                                        @endif
                                        <a href="{{route('user_charge')}}?id={{$record->id}}" class="JQ_POPUP btn btn-sm btn-outline-secondary">충</a>
                                        <a href="{{route('user_excharge')}}?id={{$record->id}}" class="JQ_POPUP btn btn-sm btn-outline-secondary">환</a>
                                        @if (config('app.site_type') != 'PWB')
                                            <a href="{{route('user_info')}}?id={{$record->id}}" class="JQ_POPUP"><i
                                                    class="fa fa-refresh" aria-hidden="true"></i></a>
                                        @endif
                                    </td>
                                    <td><span
                                            class="red-money">{{number_format($record->procinfo->loan)}}</span></td>
                                    @if (config('app.no_mini') == 0)
                                        @if (config('app.game_type') == 0)
                                            <td class="actions" style="text-align: right;">
                                                {!! printColorMoney($record->procinfo->bpoint) !!}
                                            </td>
                                            <td class="actions" style="text-align: right;">
                                                {!! printColorMoney($record->procinfo->spoint) !!}
                                            </td>
                                        @else
                                            <td class="actions" style="text-align: right;">
                                                {!! printColorMoney($record->procinfo->bpoint) !!}
                                            </td>
                                        @endif
                                    @endif
                                    <td><span
                                            class="text-danger">{{number_format(floor($record->procinfo->point))}}</span>
                                    </td>
                                    <td>{!! printColorMoney(floor($record->procinfo->losing)) !!}</td>
                                    <td>{{$record->bankmaster}}</td>
                                    <td>
                                        @php
                                            $date1 = new DateTime();
                                            $date2 = new DateTime($record->procinfo->request_at);
                                            $diff = $date1->getTimestamp() - $date2->getTimestamp();
                                        @endphp
                                        @if ($diff < 300 && $record->procinfo->request_at != '')
                                            <span class="text-success">접속</span>
                                        @else
                                            <span class="text-danger">해제</span>
                                        @endif
                                    </td>
                                    <td> {{$record->login_fail}} <a
                                            href="javascript:resetLoginFailCnt({{$record->id}});" class="btn btn-outline-secondary btn-sm">reset</a>
                                    </td>
                                    @if ($state == 0 || $state == 2)
                                        <td>
                                            <div title="{{$record->memo}}">{{substr($record->memo, 0, 15)}}</div>
                                        </td>
                                    @endif
                                    <td>
                                        @if ($record->verified == 1)
                                            <span class="text-success">정상</span>
                                            <a href="javascript:setStop({{$record->id}});" class="btn btn-danger btn-sm">중지</a>
                                        @elseif ($record->verified == 3)
                                            <span class="text-danger">중지</span>
                                            <a href="javascript:setNormal({{$record->id}});" class="btn btn-success btn-sm">정상</a>
                                        @elseif ($record->verified == 0)
                                            <span class="text-danger">요청중</span>
                                        @elseif ($record->verified == 2)
                                            <span class="text-warning">보류중</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($record->created_at != '')
                                            <span>{{date('Y-m-d H:i:s', strtotime($record->created_at))}}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($record->procinfo->charge_at != '')
                                            <span>{{date('Y-m-d H:i:s', strtotime($record->procinfo->charge_at))}}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($record->procinfo->login_at != '')
                                            <span>{{date('Y-m-d H:i:s', strtotime($record->procinfo->login_at))}}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($record->verified == '1' || $record->verified == '3')
                                            <select onchange="goSelectURL(this);">
                                                <option value="">== 기능 ==</option>
                                                <option value="{{route('edit_all_allot_rate')}}?userid={{$record->id}}">
                                                    배당율
                                                </option>
                                                <option value="{{route('edit_all_comm_rates')}}?userid={{$record->id}}">
                                                    수수료율
                                                </option>
                                                <option value="{{route('user_bet_limits')}}?userid={{$record->id}}">
                                                    배팅제한
                                                </option>
                                            </select> |
                                        @elseif ($record->verified == '0')
                                            <a href="javascript:setNormal({{$record->id}});" class="btn btn-success btn-sm">수락</a>
                                            <a href="javascript:setPending({{$record->id}});" class="btn btn-warning btn-sm">보류</a>
                                            <a href="javascript:setStop({{$record->id}});" class="btn btn-danger btn-sm">중지</a> |
                                        @elseif ($record->verified == '2')
                                            <a href="javascript:setNormal({{$record->id}});" class="btn btn-success btn-sm">수락</a>
                                            <a href="javascript:setStop({{$record->id}});" class="btn btn-danger btn-sm">중지</a> |
                                        @endif
                                        <a href="javascript:delUser({{$record->id}});"  class="btn btn-danger btn-sm">삭제</a>
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                        </table>
                        <div>
                            {!! $userRecords->withQueryString()->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $(function() {
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
        <script src="/build/js/partner/tree.js"></script>
@endsection
