<div class="btn-group" role="group" aria-label="Basic example">
    <a href="{{route('gamesSlotEdit')}}?gameid={{$game_id}}" class="btn @if ($type == 'edit') btn-primary disabled @else btn-outline-primary @endif">일반설정</a>
    <a href="{{route('gamesSlotPick')}}?gameid={{$game_id}}"  class="btn @if ($type == 'list') btn-primary disabled @else btn-outline-primary @endif">개별설정</a>
</div>
