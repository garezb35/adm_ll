@extends('layouts.master-without-nav')
@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">게임설정</h5>
    </div>
    <div class="card-body">
        @include('site.game.top', ['game_id' => $game_id, 'game_type' => $game_type])
        <div class="row mb-3">
            <div class="col-12">
                @include('site.game.slot.nav', ['game_id' => $game_id, 'type' => 'list'])
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="display table table-bordered table-nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>게임명</th>
                                <th>상태</th>
                                <th>기능</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($gameRecord as $record)
                            <tr>
                                <td>
                                    {{$record->tKR}}
                                    @if ($record->tKR == '')
                                        {{$record->tEN}}
                                    @endif
                                </td>
                                <td>
                                    @if ($record->is_enable == 1)
                                        <span class="badge bg-primary">사용중</span>
                                    @else
                                        <span class="badge bg-danger">미사용</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($record->is_enable == 1)
                                        <a href="{{route('gamesSlotPickEnable')}}?slotid={{$record->playid}}&enable=0" class="btn btn-danger btn-sm">미사용</a>
                                    @else
                                        <a href="{{route('gamesSlotPickEnable')}}?slotid={{$record->playid}}&enable=1" class="btn btn-primary  btn-sm">사용</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
