@php
    $betLimitOptions = array('' ,'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I');
@endphp
@extends('layouts.master-without-nav')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">게임설정</h5>
        </div>
        <div class="card-body">
            @include('site.game.top', ['game_id' => $game_id, 'game_type' => $game_type])
            <form action="" id="IndivGameEditForm" method="post" accept-charset="utf-8">
                @csrf
                <input type="hidden" name="gameid" value="{{$gameinfo->thirdPartyCode}}"/>
                <div class="row mb-3">
                    <div class="col-2">
                        <label for="IndivGameState" class="form-label">운영여부</label>
                        <select class="form-control" id="IndivGameState" name="state" data-choices
                                data-choices-search-false>
                            <option value="0" @if ($gameinfo->is_enable == 0) selected @endif >숨김</option>
                            <option value="1" @if ($gameinfo->is_enable == 1) selected @endif >정상운영</option>
                            <option value="2" @if ($gameinfo->is_enable == 2) selected @endif >점검</option>
                        </select>
                    </div>
                    <div class="col-2">
                        <label for="IndivGameOrder" class="form-label">현시순서</label>
                        <input type="number" id="IndivGameOrder" name="order" value="{{$gameinfo->order_no}}"
                               class="form-control"/>
                        <div style="margin-top: 3px;">설정값이 클수록 먼저 현시됩니다.</div>
                    </div>
                    @if(config('app.api_type') == 'cxapi' && $game_id != 998 && $game_id != 1000)
                        <div class="col-2">
                            <label for="IndivGameState" class="form-label">베팅제한옵션(베팅제한 문서참조)</label>
                            <select name="betLimit" id="betLimit" class="form-control" data-choices
                                    data-choices-search-false>
                                @foreach($betLimitOptions as $value)
                                    <option value="{{$value}}"
                                            @if (trim($gameinfo->betLimit) == $value) selected @endif >{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">저장하기</button>
                        <a onclick="javascript:popupCenter('{{route('gameslogs')}}?gameid={{$game_id}}&gametype={{$game_type}}', '', 800, 600);"
                           href="#" class="btn btn-outline-primary JQ_POPUP">수정로그</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
