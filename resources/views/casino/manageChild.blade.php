<ul class="tree dropdown " >
    @foreach($childs as $child)
        <li>
            <span class="tree-symbol closed">
                <span class="plus" style="display: none"><i class='ph ph-plus plus-symbol'></i></span>
                <span class="minus" style="display: none"><i class='ph ph-minus plus-symbol'></i></span>
            </span>
            <span class="tree-parent" data-userid="{{$child->userid}}">{{$child->userid}}({{$child->nickname}})</span>
            @if(count($child->children))
                @include('casino.manageChild',['childs' => $child->children])
            @endif
        </li>
    @endforeach
</ul>
