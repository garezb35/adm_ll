@php

    $slotRecord = \App\Models\BSGameCategory::where('is_live', 0)
        ->orderByDesc('order_no')
        ->get();
    $casinoRecord = \App\Models\BSGameCategory::where('is_live', 1)
        ->where('thirdPartyCode', '!=', 999)
        ->where('thirdPartyCode', '!=', 998)
        ->where('thirdPartyCode', '!=', 997)
        ->orderByDesc('order_no')
        ->get();
@endphp
@if (config('app.site_type') != 'BS')
    @if ($game_type != 'mini')
        <ul class="indiv-game-menu">

        </ul>
    @endif
    @if ($game_type == 'mini' && $game_id > 0)
        <ul class="indiv-game-menu">
        </ul>
    @endif
@endif
@if (config('app.site_type') != 'PWB')
    @if ($game_type != 'mini')
        <div class="row">
            <div class="col-12">
                <button class="btn btn-link">[슬롯]</button>
                @foreach ($slotRecord as $record)
                    <a href="{{route('gamesSlotEdit')}}?gameid={{$record->thirdPartyCode}}"
                       class="btn btn-sm @if ($game_type == 'slot' && $game_id == $record->thirdPartyCode) btn-secondary @else btn-outline-secondary @endif @if ($record->is_enable != 1) disabled @endif mb-1">{{$record->tKR}}</a>
                @endforeach
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <button class="btn btn-link">[카지노]</button>
                @foreach ($casinoRecord as $record)
                    <a class="btn btn-sm mb-1  @if ($record->is_enable != 1) disabled @endif @if ($game_type == 'casino' && $game_id == $record->thirdPartyCode) btn-info @else btn-outline-info @endif"
                       href="{{route('gamesCasinoEdit')}}?gameid={{$record->thirdPartyCode}}">{{$record->tKR}}</a>
                @endforeach
            </div>
        </div>
    @endif
@endif
