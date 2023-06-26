@extends('layouts.master-without-nav')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-2">{{$userinfo->userid}}({{$userinfo->nickname}}) 배당율 설정</h5>
                    <div class="alert alert-secondary" role="alert">
                        모든 소속 파트너의 배당율이 일괄 변경됩니다!!!
                    </div>
                    <div class="mb-3">
                        <a href="{{route('edit_allot_rate_logs')}}?userid={{$userinfo->id}}" class="btn btn-sm btn-secondary JQ_POPUP">수정로그</a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="" id="UserEditAllAllotRateForm" method="post" accept-charset="utf-8">
                        @csrf
                        @foreach ($gameRecord as $record)
                            <div class="game-item">
                                <div class="bg-secondary text-white p-1 mb-1">{{$record->game_name}}({{count($record->pickinfo)}})</div>
                                <div class="item-list d-flex mb-1">
                                    @foreach ($record->pickinfo as $pick)
                                        <div class="item-list-item mx-1">
                                            @php
                                                $arrCode = explode('_', $record->game_code);
                                                $type = $arrCode[0];
                                            @endphp
                                            @if (floatval($rateinfo[$type][$pick->pick_code] ?? 0) <= 0.1)
                                                <div>{{$pick->pick_name}}({{$pick->pick_rate}})</div>
                                            @else
                                                <div>{{$pick->pick_name}}({{$rateinfo[$type][$pick->pick_code]}})</div>
                                            @endif
                                            <div>
                                                <input type="number" name="{{$record->game_code}}[{{$pick->pick_code}}]" value="" step="0.01" min="0" max="10">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                        <div class="submit">
                            <input type="submit" value="저장하기" class="btn btn-primary"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
