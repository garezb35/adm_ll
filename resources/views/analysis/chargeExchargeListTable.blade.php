<div class="table-responsive">
    <table class="display table table-bordered table-nowrap" style="width:100%">
        <thead>
        <tr>
            <th>@lang('translation.highest')</th>
            <th>@lang('translation.straight')</th>
            <th>@lang('translation.user')</th>
            <th>@lang('translation.requested-classification')</th>
            <th>@lang('translation.burial-process')</th>
            <th>@lang('translation.account-holder')</th>
            <th><a href="javascript:void(0);" data-type="money"
                   class="@if ($type == 'money') {{$sort}} @endif sort-link">@lang('translation.request-amount')</a></th>
            <th><a href="javascript:void(0);" data-type="res" class="@if ($type == 'res') {{$sort}} @endif sort-link">@lang('translation.result-amount')</a>
            </th>
            <th><a href="javascript:void(0);" data-type="req" class="@if ($type == 'req') {{$sort}} @endif sort-link">@lang('translation.request-time')</a>
            </th>
            <th><a href="javascript:void(0);" data-type="proc" class="@if ($type == 'proc') {{$sort}} @endif sort-link">@lang('translation.process-time')</a>
            </th>
            <th>@lang('translation.state')</th>
        </tr>
        </thead>
        @if(!empty($chargeExhRecord))
            @foreach ($chargeExhRecord as $record)
                @php
                    $chxClass = $record->type == 1 ? 'charge-money' : 'exchange-money';
                @endphp
                <tr>
                    <td>
                        @php
                            $headinfo = $record->userinfo->headinfo;
                            if (empty($headinfo))
                                $headinfo = $record->userinfo;
                        @endphp
                        <span class="user-context-menu" data-user-id="{{$headinfo->id}}"
                              data-username="{{$headinfo->userid}}">
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
                            <span class="user-context-menu" data-user-id="{{$joininfo->id}}"
                                  data-username="{{$joininfo->userid}}">
                        <span style="font-weight: bold;color: {{'#' . $joininfo->user_color}}">{{$joininfo->userid}}({{$joininfo->nickname}})</span>
                    </span>
                        @endif
                    </td>
                    <td>
                        @php
                            $userinfo = $record->userinfo;
                        @endphp
                        <span class="user-context-menu" data-user-id="{{$userinfo->id}}"
                              data-username="{{$userinfo->userid}}">
                    <span style="font-weight: bold;color: {{'#' . $userinfo->user_color}}">{{$userinfo->userid}}({{$userinfo->nickname}})</span>
                </span>
                    </td>
                    <td>
                        @lang('translation.'. getPayGroupType($record->group_type, $record->store))
                    </td>
                    <td>
                        @if ($record->group_type == 8)
                            <span
                                style="font-weight: bold">{{$record->adminfo->userid}}({{$record->adminfo->nickname}})</span>
                        @endif
                    </td>
                    <td>{{$userinfo->bankmaster}}</td>
                    <td style="text-align: right"><span class="{{$chxClass}}">{{number_format($record->money)}}</span>
                    </td>
                    <td style="text-align: right"><span class="bold">{{number_format($record->user_money)}}</span></td>
                    <td>{{$record->created_at}}</td>
                    <td>{{$record->updated_at}}</td>
                    <td>@lang("translation.".getPayVerified($record->verified))</td>
                </tr>
            @endforeach
        @endif
        @if (empty($chargeExhRecord) || $chargeExhRecord->count() == 0)
            <tr>
                <td colspan="20">@lang('translation.nohistory')</td>
            </tr>
            @endif
            </tbody>
    </table>
    <div class="pagination-part" style="text-align: center;">
        {!! $chargeExhRecord->withQueryString()->links() !!}
    </div>
</div>
