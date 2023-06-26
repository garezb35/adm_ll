@extends('layouts.master-without-nav')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">게임설정</h5>
        </div>
        <div class="card-body">
            @include('site.game.top', ['game_id' => $game_id, 'game_type' => $game_type])
            <h1 class="mb-3">게임을 선택하세요</h1>
        </div>
    </div>
@endsection
