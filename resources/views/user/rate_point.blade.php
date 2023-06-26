@php
    use Illuminate\Support\Facades\Session;
    $snzResult = '';
    $arrErrors = array();
    $arrForm = array();
    if (Session::has('result'))
        $snzResult = Session::get('result');
    if (Session::has('error'))
        $arrErrors = Session::get('error');
    if (Session::has('form-rolling'))
        $arrForm = Session::get('form-rolling');
@endphp
@extends('layouts.master-without-nav')
@section('content')
    <div>
        @if ($snzResult == 'success')
            <div id="flashMessage" class="message success" style=''>수수료율 설정 성공!</div>
        @elseif ($snzResult == 'fail')
            <div id="flashMessage" class="message error" style=''>수수료율 설정 실패!</div>
        @endif
    </div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0"> {{$userinfo->userid}}({{$userinfo->nickname}}) 배팅/당첨 수수료율 설정 </h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <a href="{{route('edit_comm_rate_logs')}}?userid={{$userinfo->id}}"
                   class="btn btn-sm btn-secondary JQ_POPUP">수정로그</a>
                <a href="{{route('edit_all_comm_rates')}}?userid={{$userinfo->id}}"
                   class="btn btn-sm btn-secondary">목록가기</a>
                <a href="{{route('edit_all_comm_rates')}}?userid={{$userinfo->id}}&all=1"
                   class="btn btn-sm btn-secondary JQ_NEWPOPUP" data-u="all{{$userinfo->id}}">전체보기</a>
            </div>

            <form action="" id="UserEditAllCommRatesForm" method="post" accept-charset="utf-8">
                <div class="row mb-3">
                    @csrf
                    <div class="col-12 p-1 bg-info mb-3">[파워볼/사다리]</div>
                    @php
                        $arrGameType = array();
                    @endphp
                    @foreach ($gameRecord as $record)
                        @php
                            if (!in_array($record->game_type, $arrGameType)) {
                                $arrGameType[] = $record->game_type;
                                if ($all != 1 && $gameType == '') {
                                    echo '<div class="col-md-2 col-sm-6 col-xs-12 mb-2 "><a href="'.route('edit_all_comm_rates').'?userid='.$userinfo->id.'&code='.$record->game_type.'" class="d-block  mb-1 btn btn-sm btn-outline-secondary JQ_NEWPOPUP" data-u='.$userinfo->id.'>'.$record->game_name.'</a></div>';
                                }
                                else {
                                    echo "";
                                }
                            }
                        @endphp
                        @if ($all == 1 || $gameType != '')
                            <div class="col-md-3 mb-2 col-sm-6 col-xs-12">
                                <label class="form-label">{{$record->game_name}}</label>
                                <input type="number" name="PWB[{{$record->game_code}}]"
                                       value="{{$arrForm['PWB'][$record->game_code] ?? floatval($rol_mini[$record->game_code])}}"
                                       step="0.01" min="0" max="100" class="form-control"/>
                                <div class="text-danger">{{$arrErrors[$record->game_code] ?? ''}}</div>
                            </div>
                        @endif
                    @endforeach
                    @if (config('app.site_type') != 'PWB')
                        <div class="mt-3">
                            <label class="form-label">카지노 게임(배팅)</label>
                            <input type="number" name="BS[live]"
                                   value="{{$arrForm['BS']['live'] ?? floatval($rol_bs['live'])}}" step="0.01"
                                   min="0" max="100" class="form-control" style="width: 100px">
                        </div>
                        @if (config('app.no_mini') == 0)
                            <!-- <span>루징:</span> -->
                            <input type="hidden" name="BS[live_lose]"
                                   value="{{$arrForm['BS']['live_lose'] ?? floatval($rol_bs['live_lose'])}}"
                                   step="1" min="0" max="100">
                        @else
                            <div>
                                <label class="form-label">루징:</label>
                                <input type="number" name="BS[live_lose]"
                                       value="{{$arrForm['BS']['live_lose'] ?? floatval($rol_bs['live_lose'])}}"
                                       step="1" min="0" max="100" class="form-control" style="width: 100px">
                            </div>
                        @endif
                        <div class="text-danger">{{$arrErrors['live'] ?? ''}}</div>
                        <div class="text-danger">{{$arrErrors['live_lose'] ?? ''}}</div>

                        <div class="mt-4">
                            <label class="form-label">슬롯 게임(배팅):</label>
                            <input type="number" name="BS[slot]"
                                   value="{{$arrForm['BS']['slot'] ?? floatval($rol_bs['slot'])}}"
                                   step="0.01" min="0" max="100" class="form-control" style="width: 100px">
                        </div>
                        @if (config('app.no_mini') == 1)
                            <div class="mt-4">
                                <label class="form-label">루징:</label>
                                <input type="number" name="BS[slot_lose]"
                                       value="{{$arrForm['BS']['slot_lose'] ?? floatval($rol_bs['slot_lose'])}}"
                                       step="1" min="0" max="100" class="form-control" style="width: 100px">
                            </div>
                        @else
                            <!-- <span>루징:</span> -->
                            <input type="hidden" name="BS[slot_lose]"
                                   value="{{$arrForm['BS']['slot_lose'] ?? floatval($rol_bs['slot_lose'])}}"
                                   step="1" min="0" max="100">
                        @endif
                        <div class="text-danger">{{$arrErrors['slot'] ?? ''}}</div>
                        <div class="text-danger">{{$arrErrors['slot_lose'] ?? ''}}</div>
                    @endif

                    @if (count($userRecord) > 0)
                        <div class="text-danger">하부회원 수수료가 잘못 설정되엿습니다.</div>
                        <div>
                            @foreach ($userRecord as $record)
                                [{{$record->userInfo->userid}}] &nbsp;
                            @endforeach
                        </div>
                    @endif
                </div>
                @if (config('app.site_type') != 'PWB' || $all == 1 || $gameType != '')
                    <div class="submit">
                        <input type="submit" value="저장하기" class="btn btn-secondary"/>
                    </div>
            @endif
        </div>
        </form>
    </div>

@endsection
