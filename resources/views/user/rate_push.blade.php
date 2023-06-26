@extends('layouts.master-without-nav')
@section('content')

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{$userinfo->userid}}({{$userinfo->nickname}}) [파워볼] 누르기 비율 설정</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="alert alert-secondary border-secondary bg-secondary text-white mb-3" role="alert">
                        <p>배팅금액의 일정 비율을 보험으로 보내지 않음</p>
                        <p>최소: 0(%) ~ 최대: 허용비율(%)까지만 입력 가능, 허용비율 초과 입력시 자동 보정</p>
                    </div>
                    <div class="mb-3">
                        <a href="{{route('edit_bet_push_logs')}}?userid={{$userinfo->id}}"
                           class="btn btn-secondary JQ_POPUP">수정로그</a>
                    </div>
                    <div class="form-check form-switch  mb-3">
                        <label for="UserApplyAll" class="form-check-label">소속파트너 전체 적용</label>
                        <input type="hidden" name="apply_all" id="UserApplyAll_" value="0"/>
                        <input type="checkbox" name="apply_all" value="1" id="UserApplyAll"
                               class="form-check-input"
                               role="switch"/>
                        <input type="hidden" name="push_limit" value="0"/>
                    </div>
                    <div class="mb-3 row">
                        @php
                            $arrGameType = array();
                        @endphp
                        @foreach ($gameRecord as $index=>$record)

                            @php
                                    if (!in_array($record->game_type, $arrGameType)) {
                                        $arrGameType[] = $record->game_type;
                                        if($index % 2 == 0 && $index == 0) echo "<div style='clear:both'></div>";
                                    }
                            @endphp
                            <div class="col-3 mb-3">
                                <label class="form-label">{{$record->game_name}}</label>
                                @if ($pushinfo[$record->game_code] == 0)
                                    <input type="number" name="{{$record->game_code}}" value="" step="1" min="0" max="100"  class="form-control" placeholder="%"/>
                                @else
                                    <input type="number" name="{{$record->game_code}}" value="{{floatval($pushinfo[$record->game_code])}}" step="1" min="0" max="100"  class="form-control" placeholder="%"/>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
