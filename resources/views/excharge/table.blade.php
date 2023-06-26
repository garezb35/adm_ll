<table class="display table table-bordered table-nowrap" style="width:100%">
    <thead>
    <tr>
        <th>최상위</th>
        <th>직상위</th>
        <th>회원</th>
        <th>요청분류</th>
        <th>매장처리</th>
        <th>예금주</th>
        <th><a href="javascript:void(0);" data-type="money" class="@if ($type == 'money') {{$sort}} @endif sort-link">신청금액</a></th>
        <th>여신</th>
        <th><a href="javascript:void(0);" data-type="res" class="@if ($type == 'res') {{$sort}} @endif sort-link">결과금액</a></th>
        <th><a href="javascript:void(0);" data-type="req" class="@if ($type == 'req') {{$sort}} @endif sort-link">신청일시</a></th>
        <th><a href="javascript:void(0);" data-type="proc" class="@if ($type == 'proc') {{$sort}} @endif sort-link">처리일시</a></th>
        <th>상태</th>
        <th>메모(클릭)</th>
        <th class="actions">기능</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($chargeRecord as $record)
        <tr @if ($record->userinfo->is_error >= 2) style="background: #ffe2e2;" @endif>
            <td>
                @php
                    $headinfo = $record->userinfo->headinfo;
                    if (empty($headinfo))
                        $headinfo = $record->userinfo;
                @endphp
                <span class="user-context-menu" data-user-id="{{$headinfo->id}}" data-username="{{$headinfo->userid}}">
                    <span style="font-weight: bold; color: {{'#' . $headinfo->user_color}}">{{$headinfo->userid}}({{$headinfo->nickname}})</span>
                </span>
            </td>
            <td>
                @php
                    $joininfo = $record->userinfo->joininfo;
                    if ($record->userinfo->masterid == 0)
                       $joininfo = array();
                    else if (!empty($headinfo) && $record->userinfo->masterid == $record->userinfo->joinid)
                       $joininfo = $record->userinfo;
                @endphp
                @if (!empty($joininfo))
                    <span class="user-context-menu" data-user-id="{{$joininfo->id}}" data-username="{{$joininfo->userid}}">
                        <span style="font-weight: bold;color: {{'#' . $joininfo->user_color}}">{{$joininfo->userid}}({{$joininfo->nickname}})</span>
                    </span>
                @endif
            </td>
            <td>
                @php
                    $userinfo = $record->userinfo;
                @endphp
                <span class="user-context-menu" data-user-id="{{$userinfo->id}}" data-username="{{$userinfo->userid}}">
                    <span style="font-weight: bold;color: {{'#' . $userinfo->user_color}}">{{$userinfo->userid}}({{$userinfo->nickname}})</span>
                </span>
            </td>
            <td>
                {{ getPayGroupType($record->group_type, $record->store) }}
            </td>
            <td>
                @if ($record->group_type == 8)
                    <span style="font-weight: bold">{{$record->adminfo->userid}}({{$record->adminfo->nickname}})</span>
                @endif
            </td>
            <td>{{$record->bank_detail}}</td>
            <td style="text-align: right"><span class="exchange-money">{{number_format($record->money)}}</span></td>
            <td>0</td>
            <td style="text-align: right">
                <span class="font-weight-bold">
                    @if (config('app.no_mini') == 0 && ($record->verified == 1 || $record->verified == 0))
                        {{number_format($userinfo->procinfo->money)}}
                    @else
                        {{number_format($record->user_money)}}
                    @endif
                </span>
            </td>
            <td>{{$record->created_at}}</td>
            <td>{{$record->updated_at}}</td>
            <td>@lang('translation.'.getPayVerified($record->verified))</td>
            <td class="memo-wrapper" style="cursor: pointer;">
                <div class="view" style="text-align: left">{{$record->memo}}</div>
                <div class="edit" style="display: none;" data-id="{{$record->id}}">
                    <input type="text" value="">
                    <button type="button">저장</button>
                </div>
            </td>
            <td class="actions">
                @if ($userinfo->is_error >= 5)
                    @if ($record->verified == 0)
                        <a href="javascript:setReady({{$record->id}});">대기</a> |
                    @endif
                    <span style="font-weight: bold; color: red; ">환전불가!</span>
                @else
                    @if ($record->verified == 0)
                        <a href="javascript:setReady({{$record->id}});">대기</a> |
                        <a href="javascript:setDone({{$record->id}});">처리</a> |
                        <a href="javascript:setDelete({{$record->id}});">취소</a>
                    @elseif ($record->verified == 1)
                        <a href="javascript:setDone({{$record->id}});">처리</a> |
                        <a href="javascript:setDelete({{$record->id}});">취소</a>
                    @endif
                @endif
            </td>
        </tr>
    @endforeach
    @if ($chargeRecord->count() == 0)
        <tr>
            <td colspan="20">자료가 없습니다.</td>
        </tr>
    @endif
    </tbody>
</table>
<div>
    {!! $chargeRecord->withQueryString()->links() !!}
</div>
<script>
    $(document).ready(function() {
        $('.sort-link').bind('click', function() {
            var elem = $(this);
            if (elem.hasClass('asc')) {
                $('#sort-arrow').val('desc');
                $('#sort-type').val(elem.data('type'));
            }
            else {
                $('#sort-arrow').val('asc');
                $('#sort-type').val(elem.data('type'));
            }
            $("#form-search").submit();
        });

    });
</script>
