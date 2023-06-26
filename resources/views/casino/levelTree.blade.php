<ul id="tree1" class="tree" style="padding: 12px 0;">
    @foreach($userRecord as $record)
        <li>
            <span class="tree-symbol closed">
                <span class="plus" style="display: none"><i class='ph ph-plus plus-symbol'></i></span>
                <span class="minus" style="display: none"><i class='ph ph-minus plus-symbol'></i></span>
            </span>
            <span class="tree-parent" data-userid="{{$record->userid}}">{{$record->userid}}({{$record->nickname}})</span>
            @if(count($record->children))
                @include('casino.manageChild',['childs' => $record->children])
            @endif
        </li>
    @endforeach
</ul>
