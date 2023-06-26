@extends('layouts.master-without-nav')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-2">{{$userinfo->userid}}({{$userinfo->nickname}}) 배팅금액 제한 설정</h5>
            <div class="mb-2">
                <a href="{{route('user_bet_limits_logs')}}?userid={{$userinfo->id}}"
                   class="btn btn-sm btn-secondary JQ_POPUP">수정로그</a>
            </div>
        </div>
        <div class="card-body">
            <form action="" id="UserBetLimitEditAllForm" method="post" accept-charset="utf-8">
                @csrf
                <div class="row">
                    @foreach ($gameRecord as $game)
                        <div class="col-md-3 mb-2 col-sm-6 col-xs-12">
                            <label for="betlimit{{$game->game_code}}" class="form-label">{{$game->game_name}}</label>
                            <select name="{{$game->game_code}}" id="betlimit{{$game->game_code}}" data-choices class="form-control">
                                <option value="">제한없음</option>
                                @foreach ($game->limitinfo as $limit)
                                    <option value="{{$limit->limitid}}" @if ($userinfo->limitinfo[$game->game_code] == $limit->limitid) selected @endif>{{$limit->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                    <div class="col-12">
                        <input type="submit" value="저장하기" class="btn btn-primary">
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
